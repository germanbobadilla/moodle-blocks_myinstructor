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
 * English strings for the myinstructor block.
 *
 * @package    block_myinstructor
 * @copyright  2026 German Bobadilla, MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['configdescription'] = 'Description';
$string['configdescription_help'] = 'An optional free-text description shown in the block, for example an introduction, office hours or how best to get in touch. Plain text only, maximum 100 words.';
$string['configinstructor'] = 'Instructor';
$string['configinstructor_help'] = 'The user shown in this block. Only users who hold one of the configured instructor roles in this course are listed. The block is shown to a student only when that student shares at least one group with the selected instructor.';
$string['configinstructorroles'] = 'Instructor roles';
$string['configinstructorroles_desc'] = 'The roles that identify a user as an instructor. Only users who hold one of these roles in a course can be chosen in that course\'s instructor blocks. If no user in the course holds one of these roles, every enrolled user is offered instead.';
$string['configshowgroups'] = 'Show the instructor\'s group(s)';
$string['configshowmessage'] = 'Show the "Send message" button';
$string['configtitle'] = 'Block title';
$string['configtitle_help'] = 'An optional title that replaces the default block title.';
$string['descriptiontoolong'] = 'The description must not be longer than {$a} words.';
$string['group'] = 'Group';
$string['groups'] = 'Groups';
$string['instructornotavailable'] = 'The selected instructor is no longer available in this course.';
$string['myinstructor:addinstance'] = 'Add a new instructor block';
$string['myinstructor:myaddinstance'] = 'Add a new instructor block to the Dashboard';
$string['myinstructor:viewall'] = 'View the instructor block without sharing a group with the instructor';
$string['noinstructor'] = 'No instructor has been selected yet. Turn editing on and configure this block.';
$string['notinanygroup'] = 'The selected instructor does not belong to any group in this course, so no students can see this block.';
$string['pluginname'] = 'My instructor';
$string['privacy:metadata'] = 'The My instructor block does not store any personal data. It only displays profile information of a user chosen in the block configuration.';
$string['sendmessage'] = 'Send message';
