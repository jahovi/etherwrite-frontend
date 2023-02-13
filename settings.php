<?php

/**
 *
 * @package mod_write
 * @copyright  2022 Marc Burchart <marc.burchart@fernuni-hagen.de> , Kooperative Systeme, FernUniversität Hagen
 *
 */

defined('MOODLE_INTERNAL') || die();

$settings->add(
    new admin_setting_configtext(
        'write/serverurl',
        get_string('serverurl', 'write'),
        get_string('serverurldescription', 'write'),
        null,
        PARAM_RAW,
        40
    )
);

$settings->add(
    new admin_setting_configtext(
        'write/apikey',
        get_string('apikey', 'write'),
        get_string('apikeydescription', 'write'),
        null,
        PARAM_RAW,
        40
    )
);

$settings->add(
    new admin_setting_configtext(
        'write/evaurl',
        get_string('evaurl', 'write'),
        get_string('evaurldescription', 'write'),
        null,
        PARAM_RAW,
        40
    )
);

$settings->add(
    new admin_setting_configtext(
        'write/evajwtsecretkey',
        get_string('evasecret', 'write'),
        get_string('evasecretdescription', 'write'),
        null,
        PARAM_RAW,
        40
    )
);

$settings->add(
    new admin_setting_configcheckbox(
        'write/localinstallation',
        get_string('localinstallation', 'write'),
        get_string('localinstallationdescription', 'write'),
        0, 1, 0
    )
);

