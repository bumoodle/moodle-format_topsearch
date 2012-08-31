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
 * Renderer for outputting the topics course format.
 *
 * @package format_topics
 * @copyright 2012 Dan Poltawski
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.3
 */


defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/course/format/renderer.php');
require_once($CFG->dirroot.'/course/format/topics/renderer.php');

/**
 * Basic renderer for topics format.
 *
 * @copyright 2012 Dan Poltawski
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_topsearch_renderer extends format_topics_renderer {

   /**
     * Output the html for a multiple section page
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections The course_sections entries from the DB
     * @param array $mods used for print_section()
     * @param array $modnames used for print_section()
     * @param array $modnamesused used for print_section()
     */
  public function print_multiple_section_page($course, $sections, $mods, $modnames, $modnamesused) {

        //use the global PAGE object
        global $PAGE;

        //import jQuery, which we use for searching, and the search script
        //TODO: replace with YUI?
        $PAGE->requires->js('/course/format/topsearch/javascript/jquery-1.7.2.min.js');
        $PAGE->requires->js('/course/format/topsearch/javascript/jquery.scrollTo-1.4.2-min.js');
        $PAGE->requires->js('/course/format/topsearch/javascript/jquery.contextMenu.js');
        $PAGE->requires->js('/course/format/topsearch/javascript/topsearch.js');

        //render the course title, like in the old version
        echo html_writer::tag('h2', $course->fullname, array('class' => 'headingblock header outline'));

        //render the search area
        echo html_writer::start_tag('div', array('id' => 'topicsearchsizer'));
        echo html_writer::empty_tag('input', array('id' => 'topicsearch', 'name' => 'topicsearch'));
        echo html_writer::end_tag('div');

        //and print the rest of the page in the normal topic format
        parent::print_multiple_section_page($course, $sections, $mods, $modnames, $modnamesused);
    }
}
