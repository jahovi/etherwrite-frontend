<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file keeps track of upgrades to mod_write.
 *
 * @package    mod_write
 * @copyright  2019 Martin Gauk, innoCampus, TU Berlin
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Upgrade code for mod_write.
 *
 * @param int $oldversion the version we are upgrading from.
 */
function xmldb_write_upgrade($oldversion = 0)
{
    global $CFG, $DB;

    $dbman = $DB->get_manager();


    issue_25($oldversion, $dbman);

    issue_17($oldversion, $dbman);

    issue_8($oldversion, $dbman);

    issue_27($oldversion, $dbman);

    return true;
}

function issue_25($oldversion, $dbman)
{
    $newversion = 2022111901;
    if ($oldversion < $newversion) {
        // Define table write_widget to be created.
        $table = new xmldb_table('write_widget');

        // Adding fields to table write_widget.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('write', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('user', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('component', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('configuration', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Adding keys to table write_widget.
        $table->add_key('id', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('write', XMLDB_KEY_FOREIGN, ['write'], 'write', ['id']);
        $table->add_key('user', XMLDB_KEY_FOREIGN, ['user'], 'user', ['id']);

        // Conditionally launch create table for write_widget.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Write savepoint reached.
        upgrade_mod_savepoint(true, $newversion, 'write');

    }
}

function issue_17(int $oldversion, $dbman)
{
    global $DB;
    $newversion = 2022112001;
    if ($oldversion < $newversion) {
        $function = array(
            "name" => "mod_write_getAuthors",
            "classname" => "mod_write_external",
            "methodname" => "getAuthors",
            "classpath" => "mod/write/db/external.php",
            "component" => "mod_write"
        );
        $DB->insert_record("external_functions", $function, false, false);

        $getFunction = array(
            "name" => "mod_write_getDashboards",
            "classname" => "mod_write_external",
            "methodname" => "getDashboards",
            "classpath" => "mod/write/db/external.php",
            "component" => "mod_write"
        );
        $DB->insert_record("external_functions", $getFunction, false, false);


        // Write savepoint reached.
        upgrade_mod_savepoint(true, $newversion, 'write');
    }
}

function issue_8(int $oldversion, $dbman)
{
    global $DB;

    $newversion = 2022112701;

    if ($oldversion < $newversion) {

        $saveFunction = array(
            "name" => "mod_write_saveDashboard",
            "classname" => "mod_write_external",
            "methodname" => "saveDashboard",
            "classpath" => "mod/write/db/external.php",
            "component" => "mod_write"
        );
        $DB->insert_record("external_functions", $saveFunction, false, false);

        // Define field w to be added to write_widget.
        $table = new xmldb_table('write_widget');
        $newFields = [];
        $w = new xmldb_field('w', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1', 'The widget\'s display width.');
        $newFields[] = $w;
        $h = new xmldb_field('h', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1', 'The widget\'s display height.');
        $newFields[] = $h;
        $x = new xmldb_field('x', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1', 'The widget\'s x position.');
        $newFields[] = $x;
        $y = new xmldb_field('y', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1', 'The widget\'s y position.');
        $newFields[] = $y;

        foreach ($newFields as $newField) {
            // Conditionally launch add field w.
            if (!$dbman->field_exists($table, $newField)) {
                $dbman->add_field($table, $newField);
            }
        }

        $field = new xmldb_field('user', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'write');

        // Launch rename field user.
        $dbman->rename_field($table, $field, 'user_id');

        // Write savepoint reached.
        upgrade_mod_savepoint(true, $newversion, 'write');
    }
}

function issue_27(int $oldversion, $dbman)
{
    global $DB;

    $newversion = 2022120503;

    if ($oldversion < $newversion) {
        $authoringRatios = $DB->get_records('write_widget', array('component' => 'authoringRatios'));
        foreach ($authoringRatios as $widget) {
            $widget->component = 'authoringRatios_pie';
            $DB->update_record('write_widget', $widget);
        }
    }
}