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
 * This file contains general functions for the course format Topic
 *
 * @since 2.0
 * @package moodlecore
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Indicates this format uses sections.
 *
 * @return bool Returns true
 */
function callback_topsearch_uses_sections() {
    return true;
}

/**
 * Used to display the course structure for a course where format=topic
 *
 * This is called automatically by {@link load_course()} if the current course
 * format = weeks.
 *
 * @param array $path An array of keys to the course node in the navigation
 * @param stdClass $modinfo The mod info object for the current course
 * @return bool Returns true
 */
function callback_topsearch_load_content(&$navigation, $course, $coursenode) 
{
    return $navigation->load_generic_course_sections($course, $coursenode, 'topsearch');
}

/**
 * The string that is used to describe a section of the course
 * e.g. Topic, Week...
 *
 * @return string
 */
function callback_topsearch_definition() {
    return get_string('topic');
}

/**
 * The GET argument variable that is used to identify the section being
 * viewed by the user (if there is one)
 *
 * @return string
 */
function callback_topsearch_request_key() {
    return 'topic';
}

function callback_topsearch_get_section_name($course, $section) 
{
    // We can't add a node without any text
    if (!empty($section->name)) {
        return format_string($section->name, true, array('context' => get_context_instance(CONTEXT_COURSE, $course->id)));
    } else if ($section->section == 0) {
        return get_string('section0name', 'format_topics');
    } else {
        return get_string('topic').' '.$section->section;
    }
}

/**
 * Declares support for course AJAX features
 *
 * @see course_format_ajax_support()
 * @return stdClass
 */
function callback_topsearch_ajax_support() {
    $ajaxsupport = new stdClass();
    $ajaxsupport->capable = true;
    $ajaxsupport->testedbrowsers = array('MSIE' => 6.0, 'Gecko' => 20061111, 'Safari' => 531, 'Chrome' => 6.0);
    return $ajaxsupport;
}

/**
 * Returns a URL to arrive directly at a section
 *
 * @param int $courseid The id of the course to get the link for
 * @param int $sectionnum The section number to jump to
 * @return moodle_url
 */
function callback_topsearch_get_section_url($courseid, $sectionnum) {
    return new moodle_url('/course/view.php', array('id' => $courseid, 'topic' => $sectionnum));
}

/**
 * Prints the menus to add activities and resources.
 */
function ajax_section_add_menus($course, $modnames, $menuName='addContextMenu', $return=false) {
    global $CFG, $OUTPUT;

    $section = 0;

    //if the user can't manage activities, don't create the ajax menu
    if (!has_capability('moodle/course:manageactivities', get_context_instance(CONTEXT_COURSE, $course->id))) 
        return false;
    
    //start empty arrays of resources and activities
    $resource_objects = array();
    $activity_objects = array();

    //and iterate through each module name provided 
    foreach($modnames as $modname=>$modnamestr) 
    {

        //if the course is not allowed the given module, skip it
        if (!course_allowed_module($course, $modname)) 
            continue;
        

        //try to get the library file for each module
        $libfile = "$CFG->dirroot/mod/$modname/lib.php";

        //if we can't, continue without throwing an error
        if (!file_exists($libfile))
            continue;
        
        //otherwise, include the relevant library
        include_once($libfile);
        $gettypesfunc =  $modname.'_get_types';

        //only allow modules which _don't_ have subtypes to be added to the quick menu
        if (!function_exists($gettypesfunc)) 
        {
            //get the "archetype" for the module
            $archetype = plugin_supports('mod', $modname, FEATURE_MOD_ARCHETYPE, MOD_ARCHETYPE_OTHER);

            //if the module describes itself as a resource, add it to the resources cluster
            if($archetype == MOD_ARCHETYPE_RESOURCE)
                $resource_objects[] = (object)array('name' => $modnamestr, 'icon' => $modname, 'shortname' => $modname);
            else
                $activity_objects[] = (object)array('name' => $modnamestr, 'icon' => $modname, 'shortname' => $modname);

           
        }
    }

    //start a new HTML5 context menu
    $output = html_writer::start_tag('menu', array('type' => 'context', 'id' => $menuName, 'style' => 'display:none'));


    //populate it with our resources
    if(!empty($resource_objects))
    { 
        foreach($resource_objects as $resource)
        {
            $output .= html_writer::start_tag('command', array('label' => $resource->name, 'onclick' => 'handleMenu(\''.$resource->shortname.'\', this);', 'icon' => $resource->icon));
            $output .= html_writer::end_tag('command');
        }
    }

    //add a divider between the resources and assignments
    $output .= html_writer::empty_tag('hr');

    //and populate it with our activities
    if(!empty($activity_objects))
    { 
        foreach($activity_objects as $resource)
        {
            $output .= html_writer::start_tag('command', array('label' => $resource->name, 'onclick' => 'handleMenu(\''.$resource->shortname.'\', this);', 'icon' => $resource->icon));
            $output .= html_writer::end_tag('command');
        }
    }

    //end the menu
    $output .= html_writer::end_tag('menu');

    //if the return flag is set, return the output; otherwise, echo it directly 
    if ($return) 
        return $output;
    else 
        echo $output;
}


