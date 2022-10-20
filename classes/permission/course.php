<?php

/**
 *
 * @package    mod_write
 * @copyright  2022 Marc Burchart <marc.burchart@fernuni-hagen.de> , Kooperative Systeme, FernUniversität Hagen
 * 
 */

namespace mod_write\permission;

defined('MOODLE_INTERNAL') || die();

class course extends context {
    
    function __construct($userid, $courseid){
        $context = \context_course::instance($courseid);        
        parent::__construct($userid, $context);
    }

}

?>
