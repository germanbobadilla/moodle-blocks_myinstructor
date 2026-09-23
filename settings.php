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
 * Global settings for the myinstructor block.
 *
 * @package    block_myinstructor
 * @copyright  2026 German Bobadilla, MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $roles = role_fix_names(get_all_roles(), null, ROLENAME_ORIGINAL);
    $roleoptions = [];
    foreach ($roles as $role) {
        $roleoptions[$role->id] = $role->localname;
    }

    $defaultroleids = [];
    foreach (get_archetype_roles('editingteacher') as $role) {
        $defaultroleids[] = $role->id;
    }

    $settings->add(new admin_setting_configmultiselect(
        'block_myinstructor/instructorroles',
        get_string('configinstructorroles', 'block_myinstructor'),
        get_string('configinstructorroles_desc', 'block_myinstructor'),
        $defaultroleids,
        $roleoptions
    ));
}
