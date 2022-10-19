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

class mod_write_external extends external_api {

    public static function getEditorLink_parameters(){
        return new external_function_parameters(
            array(
                'cmid' => new external_value(PARAM_INT, 'id of write')
            )
        );
    }

    public static function getEditorLink($cmid){
        try{
            global $DB, $USER;
            $config = get_config('write');
            // Get the serverurl.
            if(!isset($config->serverurl) || is_null($config->serverurl) || !is_string($config->serverurl)){                    
                return array('success' => false, 'data' => get_string('novalidepurl', 'mod_write'));
            }
            $url = rtrim(str_replace(' ', '', $config->serverurl), '/');   
            if(strlen($url) <= 0){
                return array('success' => false, 'data' => get_string('novalidepurl', 'mod_write'));
            }
            // Get the apikey.
            if(!isset($config->apikey) || is_null($config->serverurl) || !is_string($config->apikey)){                    
                return array('success' => false, 'data' => get_string('novalidapikey', 'mod_write'));
            }
            $apikey = str_replace(' ', '', $config->apikey);
            if(strlen($apikey) <= 0){
                return array('success' => false, 'data' => get_string('novalidapikey', 'mod_write'));
            }           
            // Now we need the cm data.            
            $cm = get_coursemodule_from_id('write', $cmid, 0, false, MUST_EXIST); // Coursemodule
            $write = $DB->get_record('write', array('id' => $cm->instance), '*', MUST_EXIST); // Data stored in the db for the cm (see e.g. install.xml).
            $cid = intval($write->course); // Courseid            
            if(!isset($write->grouping) || is_null($write->grouping) || intval($write->grouping) === 0){
                return array('success' => false, 'data' => get_string('nogrouping', 'mod_write'));
            }
            $grouping = intval($write->grouping);
            $group = groups_get_all_groups($cid, $USER->id, $grouping);
            if(!is_array($group) || sizeof($group) <= 0){                   
                return array('success' => false, 'data' => get_string('nogroup', 'mod_write'));
            }
            $group = array_shift($group);
            $groupid = intval($group->id);                                
            $api = new mod_write\etherpad\client($apikey, $url.'/api');
            // Now we create a etherpad group via api.
            $epgroup = $api->create_group_if_not_exists_for($groupid); 
            if($epgroup === false){
                return array('success' => false, 'data' => get_string('couldnotcreateepgroup', 'mod_write'));
            }
            // Create a etherpad pad.
            $padName = 'ex_'.$write->id.'_g_'.$groupid;
            // Check already existing pads.
            $padsOfTheGroup = $api->list_pads($epgroup);          
            if($padsOfTheGroup === false || !isset($padsOfTheGroup->padIDs)){
                return array('success' => false, 'data' => get_string('couldnotfetchgrouppads', 'mod_write'));
            }                      
            if(!in_array($epgroup.'$'.$padName, $padsOfTheGroup->padIDs)){
                $eppad = $api->create_group_pad($epgroup, $padName);  
                if($eppad === false || !is_string($eppad)){
                    return array('success' => false, 'data' => get_string('couldnotcreatepad', 'mod_write'));
                }                   
            } else {
                $eppad = $epgroup.'$'.$padName;
            }
            $epauthor = $api->create_author_if_not_exists_for($USER->id, $authorName);
            if($epauthor === false || !is_string($epauthor)){
                return array('success' => false, 'data' => get_string('couldnotcreateauthor', 'mod_write'));
            } 
            $sessionid = $api->create_session($epgroup, $epauthor, time() + 43200);
            if($sessionid === false){
                return array('success' => false, 'data' => get_string('couldnotcreatesession', 'mod_cwr'));
            }         
            if($config->localinstallation == 1){
                $link = 'http://localhost:9001/auth_session?sessionID='.$sessionid.'&groupId='.$epgroup.'&padName='.$padName;
            } else {
                $link = rtrim($url, '/').'/auth_session?sessionID='.$sessionid.'&groupId='.$epgroup.'&padName='.$padName;  
            }                                 
            return array('success' => true, 'data' => $link);
        } catch(Exception $ex){
            return array('success' => false, 'data' => $ex->getMessage());
        }
    }

    public static function getEditorLink_returns(){
        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, 'data'),
                'data' => new external_value(PARAM_RAW, 'data'),
            )
        );
    }

    public static function getEditorLink_is_allowed_from_ajax(){
        return true;
    }

    public static function getTaskDescription_parameters(){
        return new external_function_parameters(
            array(
                'cmid' => new external_value(PARAM_INT, 'id of write')
            )
        );
    }    

    public static function getTaskDescription($cmid){
        global $USER, $DB;
        try{
            $cm = get_coursemodule_from_id('write', $cmid, 0, false, MUST_EXIST); // Coursemodule
            $write = $DB->get_record('write', array('id' => $cm->instance), '*', MUST_EXIST);
            $intro = format_module_intro('write', $write, $cm->id, false);
            return array('success' => true, 'data' => $intro);
        } catch(Exception $ex){
            return array('success' => false, 'data' => $ex->getMessage());
        }
    }

    public static function getTaskDescription_returns(){
        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, 'data'),
                'data' => new external_value(PARAM_RAW, 'data'),
            )
        );
    }

    public static function getTaskDescription_is_allowed_from_ajax(){
        return true;
    } 

}

?>