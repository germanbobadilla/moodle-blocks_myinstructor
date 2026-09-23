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
 * Tests for the group based visibility rule.
 *
 * @package    block_myinstructor
 * @copyright  2026 German Bobadilla, MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \block_myinstructor\visibility
 */
final class visibility_test extends \advanced_testcase {
    /**
     * Create a course with a group and an instructor who belongs to that group.
     *
     * @return array Ordered list: course record, group record, instructor user record.
     */
    protected function create_course_with_instructor(): array {
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $group = $generator->create_group(['courseid' => $course->id, 'name' => 'Group A']);
        $instructor = $generator->create_and_enrol($course, 'editingteacher');
        groups_add_member($group, $instructor);

        return [$course, $group, $instructor];
    }

    /**
     * A student who shares the instructor's group can view the block.
     *
     * @covers ::can_view
     * @return void
     */
    public function test_student_in_same_group_can_view(): void {
        $this->resetAfterTest();
        [$course, $group, $instructor] = $this->create_course_with_instructor();

        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        groups_add_member($group, $student);

        $context = \context_course::instance($course->id);
        $this->assertTrue(visibility::can_view($course->id, $student->id, $instructor->id, $context));
    }

    /**
     * A student in a different group cannot view the block.
     *
     * @covers ::can_view
     * @return void
     */
    public function test_student_in_other_group_cannot_view(): void {
        $this->resetAfterTest();
        [$course, , $instructor] = $this->create_course_with_instructor();

        $othergroup = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        groups_add_member($othergroup, $student);

        $context = \context_course::instance($course->id);
        $this->assertFalse(visibility::can_view($course->id, $student->id, $instructor->id, $context));
    }

    /**
     * A student who is in no group cannot view the block.
     *
     * @covers ::can_view
     * @return void
     */
    public function test_student_without_group_cannot_view(): void {
        $this->resetAfterTest();
        [$course, , $instructor] = $this->create_course_with_instructor();

        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $context = \context_course::instance($course->id);
        $this->assertFalse(visibility::can_view($course->id, $student->id, $instructor->id, $context));
    }

    /**
     * When the instructor is in no group, no student can view the block.
     *
     * @covers ::can_view
     * @return void
     */
    public function test_instructor_without_group_hides_block(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $group = $generator->create_group(['courseid' => $course->id]);
        $instructor = $generator->create_and_enrol($course, 'editingteacher');

        $student = $generator->create_and_enrol($course, 'student');
        groups_add_member($group, $student);

        $context = \context_course::instance($course->id);
        $this->assertFalse(visibility::can_view($course->id, $student->id, $instructor->id, $context));
    }

    /**
     * A user holding block/myinstructor:viewall sees the block without sharing a group.
     *
     * @covers ::can_view
     * @return void
     */
    public function test_user_with_viewall_capability_can_view(): void {
        $this->resetAfterTest();
        [$course, , $instructor] = $this->create_course_with_instructor();

        // The non-editing teacher archetype is granted block/myinstructor:viewall.
        $viewer = $this->getDataGenerator()->create_and_enrol($course, 'teacher');

        $context = \context_course::instance($course->id);
        $this->assertTrue(visibility::can_view($course->id, $viewer->id, $instructor->id, $context));
    }
}
