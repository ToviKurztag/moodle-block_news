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
 * Upload video(s).
 *
 * @package    block_news
 * @copyright  2020 Tovi Kurztag
 * @license    http://www.gnu.org/copyleft/gpl.news GNU GPL v3 or later
 */

//defined('MOODLE_INTERNAL') || die();
require_once( __DIR__ . '/../../config.php');
require_once("$CFG->libdir/formslib.php");
require_once($CFG->dirroot."/repository/lib.php");

$id = optional_param('id', 0, PARAM_INT);
if ($id == 0) { // Add new.
    $PAGE->set_context(context_system::instance());
    $PAGE->set_heading(get_string('addnew', 'block_news'));
    $PAGE->set_title(get_string('addnew', 'block_news'));
    $PAGE->set_url('/blocks/news/post.php');
} else { // Edit new.
    $PAGE->set_context(context_system::instance());
    $PAGE->set_heading(get_string('editnew', 'block_news'));
    $PAGE->set_title(get_string('editnew', 'block_news'));
    $PAGE->set_url('/blocks/news/edit.php?id=' . $id);
}
$PAGE->set_pagelayout('standard');
$context = context_system::instance();
$fileoptions = array('subdirs' => 0, 'maxfiles' => 1,
        'accepted_types' => array('image'), 'return_types' => FILE_INTERNAL | FILE_EXTERNAL);
class post_form extends moodleform {
    public function definition() {
        global $CFG, $DB, $context, $USER, $id;

        $mform = $this->_form;

        $mform->addElement('hidden', 'id', $id);
        $mform->setType('id', PARAM_RAW);

        $mform->addElement('text', 'title', get_string('title', 'block_news'));
        $mform->setType('title', PARAM_RAW);

        $mform->addElement('text', 'link', get_string('link', 'block_news'));
        $mform->setType('link', PARAM_RAW);

        $mform->addElement('textarea', 'description', get_string('description'), 'wrap="virtual" rows="5" cols="50"');
        $mform->setType('description', PARAM_RAW);

        $fileoptions = array('subdirs' => 0, 'maxfiles' => 1,
        'accepted_types' => array('image'), 'return_types' => FILE_INTERNAL | FILE_EXTERNAL);
        $mform->addElement('filemanager', 'attachments', get_string('file', 'moodle'), null, $fileoptions);

        $mform->addElement('date_selector', 'datecreated', get_string('datecreated', 'block_news'));

        $buttonarray = array();
        $buttonarray[] =& $mform->createElement('submit', 'submitbutton', get_string('savechanges'));
        $buttonarray[] =& $mform->createElement('cancel', 'cancel', get_string('cancel'));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
    }
    public function validation($data, $files) {
        return array();
    }
}

$mform = new post_form();

// Form processing and displaying is done here.
if ($mform->is_cancelled()) {
    redirect($CFG->wwwroot . '/?redirect=0');
} else if ($fromform = $mform->get_data()) {
    global $DB, $id;

    if ($id == 0) { // Add new.
        $record = new stdclass();
        $record->owner_id = $USER->id;
        $record->title = $fromform->title? $fromform->title: null;
        $record->description = $fromform->description? $fromform->description: null;
        $record->link = $fromform->link? $fromform->link: null;
        $record->datecreated = $fromform->datecreated;
        $record->datemodified = time();
        $newid = $DB->insert_record('block_news', $record);
    } else { // Update new.
        $record = new stdclass();
        $record->id = $id;
        $record->owner_id = $USER->id;
        $record->title = $fromform->title? $fromform->title: null;
        $record->description = $fromform->description? $fromform->description: null;
        $record->link = $fromform->link? $fromform->link: null;
        $record->datecreated = $fromform->datecreated;
        $record->datemodified = time();
        $newid = $DB->update_record('block_news', $record);
        $newid = $id;
    }
            $draftitemid = $fromform->attachments;
            $fileoptions = array('subdirs' => 0, 'maxfiles' => 1, 'accepted_types' => array('image'), 'return_types' => FILE_INTERNAL | FILE_EXTERNAL);
            $filesave = file_save_draft_area_files($draftitemid, $context->id, 'block_news', 'attachment', $newid, $fileoptions);
            file_prepare_draft_area($draftitemid, $context->id, 'block_news', 'attachment',
                            array('subdirs' => 0, 'maxfiles' => 1));
                $file = $DB->get_record_select('files', "itemid = $newid and filename <> '.' and component = 'block_news'",
                null , 'contenthash , filename');
            if (isset($file->filename)) {
                $record = new stdclass();
                $record->id = $newid;
                $record->filename = $file->filename;
                $newid = $DB->update_record('block_news', $record);
            } else {
                $record = new stdclass();
                $record->id = $newid;
                $record->filename = "";
                $newid = $DB->update_record('block_news', $record);
                
            }
    redirect($CFG->wwwroot . '/?redirect=0', get_string('new_saved', 'block_news'));
} else {
    // Displays the form.
    echo $OUTPUT->header();

    if ($id != 0) {
        $sql = "SELECT *
        FROM mdl_block
        WHERE name LIKE ?";
        $block = $DB->get_record_sql($sql, ['news'], null);
        $sql = "SELECT *
        FROM mdl_block_news
        WHERE id LIKE ?";
        $record = $DB->get_record_sql($sql, [$id], null);
        if (isset($record)) {
            if (empty($entry->id)) {
                $entry = new stdClass;
                $entry->id = null;
                $fileoptions = array('subdirs' => 0, 'maxfiles' => 1, 'accepted_types' => array('image'), 'return_types' => FILE_INTERNAL | FILE_EXTERNAL);
                $draftitemid = file_get_submitted_draft_itemid('attachments');
                file_prepare_draft_area($draftitemid, $context->id, 'block_news', 'attachment', $id,
                        $fileoptions);
                $entry->owner_id = $USER->id;
                $entry->title = $record->title;
                $entry->description = strip_tags($record->description);
                $entry->link = $record->link;
                $entry->datecreated = $record->datecreated;
                $entry->attachments = $draftitemid;

                $mform->set_data($entry);
            }
        }
    }
    $mform->display();
    echo $OUTPUT->footer();
}


