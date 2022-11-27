<?php

namespace mod_write\util;

defined('MOODLE_INTERNAL') || die();

class Util
{

    /**
     * Finds an object in the given array by comparing its 'id' attribute to the given id.
     * @param int $id The id to find.
     * @param array $array The array of objects.
     * @return object|false The found object, or false if none was found.
     */
    public static function findByIdInArray(int $id, array $array)
    {
        foreach ($array as $entry) {
            if (isset($entry->id) && $id == $entry->id) {
                return $entry;
            }
        }

        return false;
    }
}