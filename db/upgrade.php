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
 * This file keeps track of upgrades to the news block
 *
 * @since Moodle 2.0
 * @package block_news
 * @copyright 2020 Tovi Kurztag
 * @license http://www.gnu.org/copyleft/gpl.news GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade code for the news block.
 *
 * @param int $oldversion
 */
function xmldb_block_news_upgrade($oldversion) {
    global $CFG, $DB;
    $dbman = $DB->get_manager();
    if ($oldversion < 2019111810) {
        // Define table local_video_multi to be created.
        $table = new xmldb_table('block_news');

        // Adding fields to table block_news.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('owner_id', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('title', XMLDB_TYPE_CHAR, '200', null, null, null, null);
        $table->add_field('description', XMLDB_TYPE_CHAR, '500', null, null, null, null);
        $table->add_field('link', XMLDB_TYPE_CHAR, '500', null, null, null, null);
        $table->add_field('filename', XMLDB_TYPE_CHAR, '200', null, null, null, null);
        $table->add_field('datecreated', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('datemodified', XMLDB_TYPE_INTEGER, '20', null, null, null, null);

        // Adding keys to table block_news.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for block_news.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        // block_news savepoint reached.
       upgrade_plugin_savepoint(true, 2019111810, 'block', 'news');
    }

    if ($oldversion < 2019111811) {
        // Define table local_video_multi to be created.
        $table = new xmldb_table('block_news');
        $field = new xmldb_field('title', XMLDB_TYPE_CHAR, '200', null, null, null, null);

        // Conditionally launch create table for block_news.
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_precision($table, $field, $continue=true, $feedback=true);
        }
        // block_news savepoint reached.
        upgrade_plugin_savepoint(true, 2019111811, 'block', 'news');
    }
    return true;
}

