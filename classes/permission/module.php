<?php

/**
 *
 * @package    mod_write
 * @copyright  2022 Marc Burchart <marc.burchart@fernuni-hagen.de> , Kooperative Systeme, FernUniversität Hagen
 * 
 */

namespace mod_write\permission;

defined('MOODLE_INTERNAL') || die();

class module extends context {
    function __construct($userid, $cmid){
        $context = \context_module::instance($cmid);
        parent::__construct($userid, $context);
    }
}

?>
