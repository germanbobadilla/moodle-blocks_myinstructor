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

namespace block_myinstructor;

/**
 * Decides whether a given user may see the instructor block.
 *
 * @package    block_myinstructor
 * @copyright  2026 German Bobadilla, MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class visibility {

    /**
     * Whether the viewer may see the instructor block for a given instructor in a course.
     *
     * A viewer may see the block when they hold block/myinstructor:viewall in the course
     * context, or when they share at least one group with the instructor in that course.
     *
     * @param int $courseid The course the block lives in.
     * @param int $viewerid The user id of the person viewing the page.
     * @param int $instructorid The user id of the configured instructor.
     * @param \context_course $coursecontext The course context, passed in to avoid a repeated lookup.
     * @return bool True when the block should be shown to the viewer.
     */
    public static function can_view(int $courseid, int $viewerid, int $instructorid,
            \context_course $coursecontext): bool {

        if (has_capability('block/myinstructor:viewall', $coursecontext, $viewerid)) {
            return true;
        }

        $instructorgroups = groups_get_all_groups($courseid, $instructorid, 0, 'g.id');
        if (empty($instructorgroups)) {
            return false;
        }

        $viewergroups = groups_get_all_groups($courseid, $viewerid, 0, 'g.id');
        if (empty($viewergroups)) {
            return false;
        }

        $shared = array_intersect(array_keys($instructorgroups), array_keys($viewergroups));

        return !empty($shared);
    }
}
