<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Block that shows a course instructor to the students who share their group.
 *
 * @package    block_myinstructor
 * @copyright  2026 German Bobadilla, MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use block_myinstructor\visibility;

/**
 * The myinstructor block.
 *
 * @package    block_myinstructor
 * @copyright  2026 German Bobadilla, MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_myinstructor extends block_base {

    /** @var int Maximum number of words allowed in the per-instance description. */
    const MAX_DESCRIPTION_WORDS = 100;

    /**
     * Initialise the block with its default title.
     *
     * @return void
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_myinstructor');
    }

    /**
     * Restrict where this block may be added.
     *
     * @return array The applicable formats for this block.
     */
    public function applicable_formats() {
        return [
            'course-view' => true,
            'mod' => true,
            'my' => false,
            'site' => false,
            'admin' => false,
        ];
    }

    /**
     * Allow more than one instance per page, one per instructor.
     *
     * @return bool Always true.
     */
    public function instance_allow_multiple() {
        return true;
    }

    /**
     * This block provides global settings (the instructor roles).
     *
     * @return bool Always true.
     */
    public function has_config() {
        return true;
    }

    /**
     * Apply the per-instance configuration, overriding the title when one was set.
     *
     * @return void
     */
    public function specialization() {
        if (!empty($this->config->title)) {
            $this->title = format_string($this->config->title, true, ['context' => $this->context]);
        } else {
            $this->title = get_string('pluginname', 'block_myinstructor');
        }
    }

    /**
     * Build the block content.
     *
     * The content is empty (and therefore the block is hidden) for any student who does
     * not share a group with the configured instructor. Users with
     * block/myinstructor:viewall, and users who are editing the page, always see it.
     *
     * @return stdClass|null The block content, or null when it cannot be built.
     */
    public function get_content() {
        global $CFG, $USER, $OUTPUT;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        $course = $this->page->course;
        if (empty($course->id) || $course->id == SITEID) {
            return $this->content;
        }

        $courseid = (int) $course->id;
        $coursecontext = context_course::instance($courseid);
        $isstaff = has_capability('block/myinstructor:viewall', $coursecontext);
        $canmanage = has_capability('block/myinstructor:addinstance', $this->context);
        $editing = $this->page->user_is_editing();

        // The block has not been configured with an instructor yet.
        if (empty($this->config) || empty($this->config->instructor)) {
            if ($canmanage || $editing) {
                $this->content->text = html_writer::div(
                    get_string('noinstructor', 'block_myinstructor'),
                    'block-myinstructor-notice text-muted'
                );
            }
            return $this->content;
        }

        $instructorid = (int) $this->config->instructor;
        $instructor = core_user::get_user($instructorid, '*', IGNORE_MISSING);

        if (empty($instructor) || !empty($instructor->deleted) || !empty($instructor->suspended)) {
            if ($isstaff || $editing) {
                $this->content->text = html_writer::div(
                    get_string('instructornotavailable', 'block_myinstructor'),
                    'block-myinstructor-notice text-muted'
                );
            }
            return $this->content;
        }

        // Enforce the group visibility rule for everyone who is not staff and is not editing.
        if (!$isstaff && !$editing
                && !visibility::can_view($courseid, (int) $USER->id, $instructorid, $coursecontext)) {
            return $this->content;
        }

        $instructorgroups = groups_get_all_groups($courseid, $instructorid, 0, 'g.id, g.name');

        if (empty($instructorgroups) && ($isstaff || $editing)) {
            // Let staff know why students will not see anything, then still show the details.
            $this->content->text = html_writer::div(
                get_string('notinanygroup', 'block_myinstructor'),
                'block-myinstructor-notice text-muted'
            );
        }

        $showmessage = !isset($this->config->showmessage) || !empty($this->config->showmessage);
        $showgroups = !isset($this->config->showgroups) || !empty($this->config->showgroups);

        $profileurl = new moodle_url('/user/view.php', ['id' => $instructorid, 'course' => $courseid]);

        $hasmessage = false;
        $messageurl = '';
        if ($showmessage && !empty($CFG->messaging) && $instructorid !== (int) $USER->id) {
            $hasmessage = true;
            $messageurl = (new moodle_url('/message/index.php', ['id' => $instructorid]))->out(false);
        }

        $groups = [];
        if ($showgroups) {
            foreach ($instructorgroups as $group) {
                $groups[] = ['name' => format_string($group->name, true, ['context' => $coursecontext])];
            }
        }

        $description = '';
        if (!empty($this->config->description)) {
            $description = format_text($this->config->description, FORMAT_PLAIN, ['context' => $this->context]);
        }

        $data = [
            'picturehtml' => $OUTPUT->user_picture($instructor, [
                'size' => 100,
                'courseid' => $courseid,
                'link' => true,
            ]),
            'fullname' => fullname($instructor),
            'profileurl' => $profileurl->out(false),
            'hasdescription' => $description !== '',
            'description' => $description,
            'hasmessage' => $hasmessage,
            'messageurl' => $messageurl,
            'messagelabel' => get_string('sendmessage', 'block_myinstructor'),
            'hasgroups' => !empty($groups),
            'grouplabel' => get_string(count($groups) > 1 ? 'groups' : 'group', 'block_myinstructor'),
            'groups' => $groups,
        ];

        $this->content->text .= $OUTPUT->render_from_template('block_myinstructor/content', $data);

        return $this->content;
    }
}
