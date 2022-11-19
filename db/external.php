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
        $group = groups_get_all_groups($cid, $USER->id, $grouping);
        if (!is_array($group) || sizeof($group) <= 0) {
            throw new Exception(get_string('nogroup', 'mod_write'));
        }
        $group = array_shift($group);
        $groupid = intval($group->id);
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
        if (!in_array($epgroup . '$' . $padName, $padsOfTheGroup->padIDs)) {
            $eppad = $api->create_group_pad($epgroup, $padName);
            if ($eppad === false || !is_string($eppad)) {
                throw new Exception(get_string('couldnotcreatepad', 'mod_write'));
            }
        } else {
            $eppad = $epgroup . '$' . $padName;
        }
        $authorName = $USER->firstname . " " . $USER->lastname;
        $epauthor = $api->create_author_if_not_exists_for($USER->id, $authorName);
        if ($epauthor === false || !is_string($epauthor)) {
            throw new Exception(get_string('couldnotcreateauthor', 'mod_write'));
        }

        $openSessions = $api->list_sessions_of_author($epauthor);
        foreach ($openSessions as $id => $openSession) {
            if ($openSession->groupID == $epgroup) {
                $sessionid = $id;
            }
        }
        if (!isset($sessionid)) {
            $sessionid = $api->create_session($epgroup, $epauthor, time() + 43200);
        }
        if ($sessionid === false) {
            throw new Exception(get_string('couldnotcreatesession', 'mod_cwr'));
        }

        if ($config->localinstallation == 1) {
            $uri = "http://localhost:9001";
            $eva = "http://localhost:8083";
        } else {
            $uri = rtrim($url, '/');
            $eva = rtrim($_SERVER['SERVER_NAME'] . ':9002', '/');
        }
        $link = $uri . '/auth_session?sessionID=' . $sessionid . '&groupId=' . $epgroup . '&padName=' . $padName;

        return array(
            'sessionID' => $sessionid,
            'padName' => $padName,
            'groupId' => $epgroup,
            'link' => $link,
            'eva' => $eva
        );
    }

    public static function getEditorLink_returns()
    {
        return new external_single_structure(array(
            'sessionID' => new external_value(PARAM_RAW, 'sessionID'),
            'padName' => new external_value(PARAM_RAW, 'pad name'),
            'groupId' => new external_value(PARAM_RAW, 'group id'),
            'link' => new external_value(PARAM_RAW, 'the link for etherpad'),
            'eva' => new  external_value(PARAM_RAW, 'the link for eva requests'),
        ));
    }

    public static function getEditorLink_is_allowed_from_ajax()
    {
        return true;
    }

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
        $dashboards = new external_multiple_structure(
            new external_single_structure(array(
                'id' => new external_value(PARAM_INT, 'widget id'),
                'component' => new external_value(PARAM_RAW, 'A string representing the frontend component to render this widget.'),
                'configuration' => new external_value(PARAM_RAW, 'Optional additional configuration for the frontend component to evaluate.', VALUE_OPTIONAL)
            ))
        );

        return new external_single_structure(array(
            'project' => $dashboards,
            'user' => $dashboards,
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
        $widgets = $DB->get_records('write_widget', array('write' => $cm->instance));

        $usersWidgets = [];
        $projectWidgets = [];
        foreach ($widgets as $widget) {
            if ($widget->user == $USER->id) {
                $usersWidgets[] = self::mapDashboardWidget($widget);
            } else if ($widget->user == null) {
                $projectWidgets[] = self::mapDashboardWidget($widget);
            }
        }

        return array(
            'project' => $projectWidgets,
            'user' => $usersWidgets
        );
    }

    private static function mapDashboardWidget($widget)
    {
        return array(
            'id' => $widget->id,
            'component' => $widget->component
        );
    }


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
}

?>