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
 * Library of functions for mod_write.
 *
 * @package    mod_write
 * @copyright  2019 Martin Gauk, innoCampus, TU Berlin
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Saves a new write instance into the database.
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $write an object from the form in mod_form.php
 * @return int the id of the newly inserted record
 */
function write_add_instance($write) {
    global $DB;

    $write->timecreated = time();
    $write->timemodified = time();
       
    $id = $DB->insert_record('write', $write);
    return $id;
}

/**
 * Updates a write instance.
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $write an object from the form in mod_form.php
 * @return boolean Success/Fail
 */
function write_update_instance($write) {
    global $DB;

    $write->timemodified = time();
    $write->id = $write->instance;

    $ret = $DB->update_record('write', $write);
    return $ret;
}

/**
 * Removes a write instance from the database.
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id ID of the module instance.
 * @return boolean Success/Failure
 */
function write_delete_instance($id) {
    global $DB;

    // Check if an instance with this id exists.
    if (!$writeinstance = $DB->get_record('write', array('id' => $id))) {
        return false;
    }

    $DB->delete_records('write', ['id' => $id]);
    return true;
}

/**
 * @param string $feature FEATURE_xx constant for requested feature
 * @return bool True if feature is supported
 */
function write_supports($feature) {
    switch ($feature) {
        case FEATURE_GROUPS:
            return false;
        case FEATURE_GROUPINGS:
            return false;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        default:
            return null;
    }
}

/**
 * View or submit an mform.
 *
 * Returns the HTML to view an mform.
 * If form data is delivered and the data is valid, this returns 'ok'.
 *
 * @param $args
 * @return string
 * @throws moodle_exception
 */
function mod_write_output_fragment_mform($args) {
    $context = $args['context'];
    if ($context->contextlevel != CONTEXT_MODULE) {
        throw new \moodle_exception('fragment_mform_wrong_context', 'write');
    }

    list($course, $coursemodule) = get_course_and_cm_from_cmid($context->instanceid, 'write');
    $write = new \mod_write\write($coursemodule);

    $formdata = [];
    if (!empty($args['jsonformdata'])) {
        $serialiseddata = json_decode($args['jsonformdata']);
        if (is_string($serialiseddata)) {
            parse_str($serialiseddata, $formdata);
        }
    }

    $moreargs = (isset($args['moreargs'])) ? json_decode($args['moreargs']) : new stdClass;
    $formname = $args['form'] ?? '';

    $form = \mod_write\form\form_controller::get_controller($formname, $write, $formdata, $moreargs);

    if ($form->success()) {
        $ret = 'ok';
        if ($msg = $form->get_message()) {
            $ret .= ' ' . $msg;
        }
        return $ret;
    } else {
        return $form->render();
    }
}
