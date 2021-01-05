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
 * @package   block_news
 * @copyright 2020 Tovi Kurztag
 * @license   http://www.gnu.org/copyleft/gpl.news GNU GPL v3 or later
 */

require_once( __DIR__ . '/../../config.php');
require_login();
defined('MOODLE_INTERNAL') || die();

global $CFG;
$id = optional_param('id', 0, PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_BOOL);
$PAGE->set_context(context_system::instance());
$PAGE->set_heading(get_string('deletenew', 'block_news'));
$PAGE->set_title(get_string('deletenew', 'block_news'));
$PAGE->set_url('/blocks/news/delete.php?id=' . $id);
$PAGE->set_pagelayout('standard');

$return = $CFG->wwwroot . '/?redirect=0';
$new = $DB->get_record('block_news', array('id' => $id), '*', MUST_EXIST);
if (!$confirm or !confirm_sesskey()) {
    $optionsyes = array('confirm' => 1, 'delete' => $id, 'sesskey' => sesskey());
    $strdelete = get_string('strdelete', 'block_news');
    echo $OUTPUT->header();
    echo $OUTPUT->box_start('noticebox');
    $formcontinue = new single_button(new moodle_url($CFG->wwwroot . "/blocks/news/delete.php?id=" . $id, $optionsyes), get_string('yes'));
    $formcancel = new single_button(new moodle_url($return), get_string('no'), 'get');
    echo $OUTPUT->confirm($strdelete, $formcontinue, $formcancel);
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
    exit;
}
$new = $DB->delete_records('block_news', array('id' => $id));

redirect($return);

