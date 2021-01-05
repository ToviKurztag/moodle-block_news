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
 * Form for editing HTML block instances.
 *
 * @package   block_news
 * @copyright 2020 Tovi Kurztag
 * @license   http://www.gnu.org/copyleft/gpl.news GNU GPL v3 or later
 */
//if (!$this->block->user_can_edit()) {

//defined('MOODLE_INTERNAL') || die();
global $PAGE;
$PAGE->requires->js("/blocks/news/animation.js");

class block_news extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_news');
    }

    function has_config() {
        return true;
    }

    function applicable_formats() {
        return array('all' => true);
    }

    function specialization() {
        if (isset($this->config->title)) {
            $this->title = $this->title = format_string($this->config->title, true, ['context' => $this->context]);
        } else {
            $this->title = get_string('newnewsblock', 'block_news');
        }
    }
    protected function specific_definition($mform) {
 
        $mform->addElement('header', 'config_header', get_string('blocksettings', 'block'));
        $mform->addElement('text', 'config_text', get_string('pluginname', 'block_news'));
        $mform->setDefault('config_text', 'default value');
        $mform->setType('config_text', PARAM_RAW);
    }

    function get_content() {
         global $CFG, $OUTPUT, $DB, $PAGE;
        $context = context_system::instance();
        if ($PAGE->user_is_editing() && has_capability('block/news:addnews', $context)) {
            $edit = 1;
        } else {
            $edit = 0;
        }
        if ($this->content !== NULL) {
            return $this->content;
        }

         $this->content = new stdClass;
         $this->content->footer = '';
         $sql = "SELECT * FROM mdl_block_news ORDER BY datecreated DESC";
         $news = $DB->get_records_sql($sql, null, $limitfrom = 0, $limitnum = 0, $strictness=IGNORE_MISSING);
        if ($edit) {
            $this->content->text = $OUTPUT->render_from_template('block_news/addnew', [
                'link' => $CFG->wwwroot . '/blocks/news/post.php',
             ]);
        } else {
            $this->content->text = '';
        }
         if (isset($news)) {
            $news = array_values($news);
            $news = array_map(function($v)
            {
                $v->datecreated = date('d/m/Y', $v->datecreated);
                return $v;
            }, $news);
            $news = array_map(function($v)
            {
                global $CFG;
                if (isset($v->filename) && !empty($v->filename)) {
                    $v->filename = $CFG->wwwroot . '/pluginfile.php/1/block_news/attachment/'. $v->filename;
                } else {
                    $v->filename = "";
                }
                
                return $v;
            }, $news);
            $this->content->text = $this->content->text . $OUTPUT->render_from_template('block_news/displaynew', [
                'wwwroot' => $CFG->wwwroot,
                'listnews' => array_values($news),
                'edit' => $edit
            ]);
        }
          return $this->content;
    }
 }
