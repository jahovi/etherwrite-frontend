<?php

/**
 *
 * @package    mod_write
 * @copyright  2022 Marc Burchart <marc.burchart@fernuni-hagen.de> , Kooperative Systeme, FernUniversität Hagen
 * 
 */

namespace mod_write\permission;

class coursecategory extends context {
    function __construct($userid, $category_id){
        $context = \context_coursecat::instance($category_id);
        parent::__construct($userid, $context);
    }
}

?>
