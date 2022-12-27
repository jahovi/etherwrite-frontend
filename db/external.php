<?php

/**
 *
 * @package    mod_write
 * @copyright  2022 Marc Burchart <marc.burchart@tu-dortmund.de> , Kooperative Systeme, FernUniversität Hagen
 *
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . "/externallib.php");
require_once($CFG->dirroot . "/lib/moodlelib.php");
require_once($CFG->dirroot . '/group/lib.php');

class mod_write_external extends external_api
{

    private static $TABLE_WIDGETS = 'write_widget';

    private static function getWidgetStructure(): external_single_structure
    {
        return new external_single_structure(array(
            'id' => new external_value(PARAM_INT, 'widget id', VALUE_OPTIONAL),
            'component' => new external_value(PARAM_RAW, 'A string representing the frontend component to render this widget.'),
            'x' => new external_value(PARAM_INT, 'The widget\'s x position.'),
            'y' => new external_value(PARAM_INT, 'The widget\'s y position.'),
            'w' => new external_value(PARAM_INT, 'The widget\'s width.'),
            'h' => new external_value(PARAM_INT, 'The widget\'s height.'),
            'configuration' => new external_value(PARAM_RAW, 'Optional additional configuration for the frontend component to evaluate.', VALUE_OPTIONAL)
        ));
    }

    // getEditorLink ////////////////////////////////////////////////////////////////////////////////////////////////////

    public static function getEditorLink_parameters()
    {
        return new external_function_parameters(
            array(
                'cmid' => new external_value(PARAM_INT, 'id of write')
            )
        );
    }

    public static function getEditorLink($cmid)
    {
        global $DB, $USER;
        $config = get_config('write');
        // Get the serverurl.
        if (!isset($config->serverurl) || is_null($config->serverurl) || !is_string($config->serverurl)) {
            throw new Exception(get_string('novalidepurl', 'mod_write'));
        }
        $url = rtrim(str_replace(' ', '', $config->serverurl), '/');
        if (strlen($url) <= 0) {
            throw new Exception(get_string('novalidepurl', 'mod_write'));
        }
        // Get the apikey.
        if (!isset($config->apikey) || is_null($config->serverurl) || !is_string($config->apikey)) {
            throw new Exception(get_string('novalidapikey', 'mod_write'));
        }
        $apikey = str_replace(' ', '', $config->apikey);
        if (strlen($apikey) <= 0) {
            throw new Exception(get_string('novalidapikey', 'mod_write'));
        }
        // Now we need the cm data.
        $cm = get_coursemodule_from_id('write', $cmid, 0, false, MUST_EXIST); // Coursemodule
        $write = $DB->get_record('write', array('id' => $cm->instance), '*', MUST_EXIST); // Data stored in the db for the cm (see e.g. install.xml).
        $cid = intval($write->course); // Courseid
        if (!isset($write->grouping) || is_null($write->grouping) || intval($write->grouping) === 0) {
            throw new Exception(get_string('nogrouping', 'mod_write'));
        }
        $grouping = intval($write->grouping);
        $groups = groups_get_all_groups($cid, $USER->id, $grouping);
        if (!is_array($groups) || sizeof($groups) <= 0) {
            throw new Exception(get_string('nogroup', 'mod_write'));
        }
        $editorInstances = [];
        
        if ($config->localinstallation == 1) {
            $uri = "http://localhost:9001";
            $eva = "http://localhost:8083";
        } else {
            $uri = rtrim($url, '/');
            $eva = rtrim('http://' . $_SERVER['SERVER_NAME'] . ':9002', '/');
        }

        foreach($groups AS $group){
            $groupid = intval($group->id);
            $groupName = $group->name;
            $api = new mod_write\etherpad\client($apikey, $url . '/api');
            // Now we create a etherpad group via api.
            $epgroup = $api->create_group_if_not_exists_for($groupid);
            if ($epgroup === false) {
                throw new Exception(get_string('couldnotcreateepgroup', 'mod_write'));
            }
            // Create a etherpad pad.
            $padName = 'ex_' . $write->id . '_g_' . $groupid;
            // Check already existing pads.
            $padsOfTheGroup = $api->list_pads($epgroup);
            if ($padsOfTheGroup === false || !isset($padsOfTheGroup->padIDs)) {
                throw new Exception(get_string('couldnotfetchgrouppads', 'mod_write'));
            }

            $newPadName = $epgroup . '$' . $padName;
            if (!in_array($epgroup . '$' . $padName, $padsOfTheGroup->padIDs)) {
                $eppad = $api->create_group_pad($epgroup, $padName);
                if ($eppad === false || !is_string($eppad)) {
                    throw new Exception(get_string('couldnotcreatepad', 'mod_write'));
                }
                $padsOfTheGroup->padIDs[] = $newPadName;
            }

            $authorName = $USER->firstname . " " . $USER->lastname;
            $epauthor = $api->create_author_if_not_exists_for($USER->id, $authorName);
            if ($epauthor === false || !is_string($epauthor)) {
                throw new Exception(get_string('couldnotcreateauthor', 'mod_write'));
            }

            $sessionid = $api->create_session($epgroup, $epauthor, time() + 43200);
            if ($sessionid === false) {
                throw new Exception(get_string('couldnotcreatesession', 'mod_cwr'));
            }

            //setcookie('sessionID', $sessionid);
            $link = $uri . '/p/' . $padName . '?groupId=' . $epgroup;

           $editorInstances[] = array(
                'padName' => $padName,
                'epGroup' => $epgroup,
                'groupName' => $groupName,
                'link' => $link,
           );
        }
        $module = new mod_write\permission\module($USER->id, $cmid);

        $jwt = mod_write\etherpad\client::generateJWT(array(
            'userId' => $USER->id,
            'epAuthorId' => $epauthor,
            'editorInstances' => $editorInstances,
            'isModerator' => $module->isAnyKindOfModerator(),
        ));

        return array(
            'editorInstances' => $editorInstances,
            'isModerator' => $module->isAnyKindOfModerator(),
            'eva' => $eva,
            'jwt' => $jwt,
        );
    }

    public static function getEditorLink_returns()
    {
        return new external_single_structure(array(
            'editorInstances' => new external_multiple_structure(
                new external_single_structure(array(
                    'padName' => new external_value(PARAM_RAW, 'Name of the pad'),
                    'epGroup' => new external_value(PARAM_RAW, 'Etherpad ID for this Group'),
                    'groupName' => new external_value(PARAM_RAW, 'Name of the group'),
                    'link' => new external_value(PARAM_RAW, 'Link to this etherpad'),
                )
                )
            ),
            'isModerator' => new  external_value(PARAM_BOOL, 'A flag determining if the user is in any way a moderator for this course module.'),
            'eva' => new  external_value(PARAM_RAW, 'the link for eva requests'),
            'jwt' => new  external_value(PARAM_RAW, 'A JSON Web token containing moodle authentication data'),
        ));
    }

    public static function getEditorLink_is_allowed_from_ajax()
    {
        return true;
    }

    // getTaskDescription ////////////////////////////////////////////////////////////////////////////////////////////////////

    public static function getTaskDescription_parameters()
    {
        return new external_function_parameters(
            array(
                'cmid' => new external_value(PARAM_INT, 'id of write')
            )
        );
    }

    public static function getTaskDescription($cmid)
    {
        global $USER, $DB;
        try {
            $cm = get_coursemodule_from_id('write', $cmid, 0, false, MUST_EXIST); // Coursemodule
            $write = $DB->get_record('write', array('id' => $cm->instance), '*', MUST_EXIST);
            $intro = format_module_intro('write', $write, $cm->id, false);
            return $intro;
        } catch (Exception $ex) {
            return array(
                'message' => $ex->getMessage()
            );
        }
    }

    public static function getTaskDescription_returns()
    {
        return new external_value(PARAM_RAW, 'data');
    }

    public static function getTaskDescription_is_allowed_from_ajax()
    {
        return true;
    }

    // getAuthors ////////////////////////////////////////////////////////////////////////////////////////////////////

    public static function getAuthors_parameters()
    {
        return new external_function_parameters(
            array(
                'cmid' => new external_value(PARAM_INT, 'id of write')
            )
        );
    }

    public static function getAuthors($cmid)
    {
        global $USER, $DB;
        $config = get_config('write');
        // Now we need the cm data.
        $cm = get_coursemodule_from_id('write', $cmid, 0, false, MUST_EXIST); // Coursemodule
        $write = $DB->get_record('write', array('id' => $cm->instance), '*', MUST_EXIST);
        $cid = intval($write->course); // Courseid
        if (!isset($write->grouping) || is_null($write->grouping) || intval($write->grouping) === 0) {
            return array('success' => false, 'data' => get_string('nogrouping', 'mod_write'));
        }
        $grouping = intval($write->grouping);
        $group = groups_get_all_groups($cid, $USER->id, $grouping);
        if (!isset($write->grouping) || is_null($write->grouping) || intval($write->grouping) === 0) {
            return array('success' => false, 'data' => get_string('nogroup', 'mod_write'));
        }
        $group = array_shift($group);
        $members = groups_get_members($group->id, 'u.id, u.firstname, u.lastname');
        $data = [];
        foreach ($members as $member) {
            $data[] = [
                "id" => $member->id,
                "fullName" => $member->firstname . ' ' . $member->lastname,
            ];
        }
        return $data;
    }

    public static function getAuthors_returns()
    {
        return new external_multiple_structure(
            new external_single_structure(array(
                    'id' => new external_value(PARAM_INTEGER, 'ID of the user'),
                    'fullName' => new external_value(PARAM_RAW, 'Full name (first name + last name) of the user.')
                )
            )
        );
    }

    public static function getAuthors_is_allowed_from_ajax()
    {
        return true;
    }

    // getDashboards ////////////////////////////////////////////////////////////////////////////////////////////////////

    public static function getDashboards_parameters()
    {
        return new external_function_parameters(
            array(
                'cmid' => new external_value(PARAM_INT, 'id of write')
            )
        );
    }

    public static function getDashboards_returns()
    {

        return new external_single_structure(array(
            'project' => new external_multiple_structure(self::getWidgetStructure()),
            'user' => new external_multiple_structure(self::getWidgetStructure()),
        ));
    }

    public static function getDashboards_is_allowed_from_ajax()
    {
        return true;
    }

    public static function getDashboards($cmid)
    {
        global $USER, $DB;
        $cm = get_coursemodule_from_id('write', $cmid, 0, false, MUST_EXIST); // Coursemodule
        $projectWidgets = $DB->get_records(self::$TABLE_WIDGETS, array(
            'write' => $cm->instance,
            'user_id' => null,
        ));
        $usersWidgets = $DB->get_records(self::$TABLE_WIDGETS, array(
            'write' => $cm->instance,
            'user_id' => intval($USER->id),
        ));

        return array(
            'project' => $projectWidgets,
            'user' => $usersWidgets
        );
    }

    // saveDashboard ////////////////////////////////////////////////////////////////////////////////////////////////////


    public static function saveDashboard_parameters()
    {
        return new external_function_parameters(array(
            'cmid' => new external_value(PARAM_INT, 'id of write'),
            'widgets' => new external_multiple_structure(
                self::getWidgetStructure()
            )
        ));
    }

    public static function saveDashboard_returns()
    {
        return null;
    }

    public static function saveDashboard_is_allowed_from_ajax()
    {
        return true;
    }

    public static function saveDashboard($cmid, $sentWidgets)
    {
        global $USER, $DB;

        $userId = intval($USER->id);

        $cm = get_coursemodule_from_id('write', $cmid, 0, false, MUST_EXIST); // Coursemodule
        $existingWidgets = $DB->get_records(self::$TABLE_WIDGETS, array(
            'write' => $cm->instance,
            'user_id' => $USER->id
        ));

        $widgetsToUpdate = [];
        $widgetsToCreate = [];
        $widgetsToDelete = [];

        foreach ($sentWidgets as $sentWidget) {
            $existingWidget = false;
            if (isset($sentWidget['id'])) {
                $existingWidget = mod_write\etherpad\client::findByIdInArray($sentWidget['id'], $existingWidgets);
            }

            if ($existingWidget) {
                $widgetsToUpdate[] = $sentWidget;
            } else {
                $widgetsToCreate[] = $sentWidget;
            }
        }

        foreach ($existingWidgets as $w) {
            $stillThere = mod_write\etherpad\client::findByIdInArray($w->id, $widgetsToUpdate);
            if (!$stillThere) {
                $widgetsToDelete[] = $w;
            }
        }

        $result = [];

        foreach ($widgetsToUpdate as $w) {
            $w['user_id'] = $userId;
            $w['write'] = $cm->instance;
            $DB->update_record(self::$TABLE_WIDGETS, $w, true);
            $result[] = $w;
        }

        foreach ($widgetsToCreate as $w) {
            $w['user_id'] = $userId;
            $w['write'] = $cm->instance;
            $w['id'] = $DB->insert_record(self::$TABLE_WIDGETS, $w, true);
            $result[] = $w;
        }

        foreach ($widgetsToDelete as $w) {
            $DB->delete_records(self::$TABLE_WIDGETS, ['id' => $w->id]);
        }

        return $result;
    }
}

?>