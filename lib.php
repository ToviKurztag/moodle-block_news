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
 * Form for editing news block instances.
 *
 * @copyright 2020 Tovi Kurztag
 * @license   http://www.gnu.org/copyleft/gpl.news GNU GPL v3 or later
 * @package   block_news
 * @category  files
 * @param stdClass $course course object
 * @param stdClass $birecord_or_cm block instance record
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool
 * @todo MDL-36050 improve capability check on stick blocks, so we can check user capability before sending images.
 */
function block_news_pluginfile($course, $birecord_or_cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $DB, $CFG, $USER;

    if ($context->contextlevel != CONTEXT_SYSTEM) {
        send_file_not_found();
    }

    $fs = get_file_storage();

    $filename = array_pop($args);
    $filepath = $args ? '/'.implode('/', $args).'/' : '/';
    // $itemid = $DB->get_record_select('block_news', "filename like " .$filename,
    //              null , 'id');
    $itemid = $DB->get_field('files', 'itemid', ["filename" => $filename, "component" => "block_news"], $strictness = IGNORE_MISSING);
    //print_r($itemid);die;
    if (!$file = $fs->get_file($context->id, 'block_news', 'attachment', $itemid, $filepath, $filename)) {
        send_file_not_found();
    }
    $forcedownload = false;

    \core\session\manager::write_close();
    print_r($file->get_content());
    send_stored_file($file, null, 0, $forcedownload, $options);
}