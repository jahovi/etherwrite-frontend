<?php

/**
 *
 * @package    mod_write
 * @copyright  2021 Marc Burchart <marc.burchart@fernuni-hagen.de> , Kooperative Systeme, FernUniversität Hagen
 *
 */

defined('MOODLE_INTERNAL') || die();

$functions = array(
    'mod_write_getEditorLink' => array(
        'classname' => 'mod_write_external',
        'methodname' => 'getEditorLink',
        'classpath' => 'mod/write/db/external.php',
        'description' => 'Get the editor link.',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => true
    ),
    'mod_write_getTaskDescription' => array(
        'classname' => 'mod_write_external',
        'methodname' => 'getTaskDescription',
        'classpath' => 'mod/write/db/external.php',
        'description' => 'Show the intro of the activity.',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => true
    ),
    'mod_write_getAuthors' => array(
        'classname' => 'mod_write_external',
        'methodname' => 'getAuthors',
        'classpath' => 'mod/write/db/external.php',
        'description' => 'Show all users that are working in the current module.',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => true
    ),
    'mod_write_getDashboards' => array(
        'classname' => 'mod_write_external',
        'methodname' => 'getDashboards',
        'classpath' => 'mod/write/db/external.php',
        'description' => 'Return the currently usable dashboards of the user.',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => true
    )
);
