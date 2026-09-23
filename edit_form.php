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
 * Instance configuration form for the myinstructor block.
 *
 * @package    block_myinstructor
 * @copyright  2026 German Bobadilla, MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Defines the instance settings shown when a myinstructor block is configured.
 *
 * @package    block_myinstructor
 * @copyright  2026 German Bobadilla, MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_myinstructor_edit_form extends block_edit_form {
    /**
     * Add the block specific settings to the configuration form.
     *
     * @param \MoodleQuickForm $mform The form being built.
     * @return void
     */
    protected function specific_definition($mform) {
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        $mform->addElement('text', 'config_title', get_string('configtitle', 'block_myinstructor'));
        $mform->setType('config_title', PARAM_TEXT);
        $mform->addHelpButton('config_title', 'configtitle', 'block_myinstructor');

        $options = $this->get_instructor_options();
        $options = ['' => get_string('choosedots')] + $options;

        $mform->addElement(
            'select',
            'config_instructor',
            get_string('configinstructor', 'block_myinstructor'),
            $options
        );
        $mform->addHelpButton('config_instructor', 'configinstructor', 'block_myinstructor');

        $mform->addElement(
            'textarea',
            'config_description',
            get_string('configdescription', 'block_myinstructor'),
            ['rows' => 4, 'cols' => 40]
        );
        $mform->setType('config_description', PARAM_TEXT);
        $mform->addHelpButton('config_description', 'configdescription', 'block_myinstructor');

        $mform->addElement(
            'advcheckbox',
            'config_showmessage',
            get_string('configshowmessage', 'block_myinstructor')
        );
        $mform->setDefault('config_showmessage', 1);

        $mform->addElement(
            'advcheckbox',
            'config_showgroups',
            get_string('configshowgroups', 'block_myinstructor')
        );
        $mform->setDefault('config_showgroups', 1);
    }

    /**
     * Validate the submitted configuration.
     *
     * @param array $data The submitted form data.
     * @param array $files The submitted files.
     * @return array A list of errors keyed by form element name.
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (empty($data['config_instructor'])) {
            $errors['config_instructor'] = get_string('required');
        }

        $description = isset($data['config_description']) ? trim($data['config_description']) : '';
        if ($description !== '') {
            $words = preg_split('/\s+/u', $description, -1, PREG_SPLIT_NO_EMPTY);
            if (count($words) > block_myinstructor::MAX_DESCRIPTION_WORDS) {
                $errors['config_description'] = get_string(
                    'descriptiontoolong',
                    'block_myinstructor',
                    block_myinstructor::MAX_DESCRIPTION_WORDS
                );
            }
        }

        return $errors;
    }

    /**
     * Build the list of users that may be selected as the instructor.
     *
     * Users who hold one of the configured instructor roles in the course are offered,
     * sorted by full name. When no user in the course holds such a role, every enrolled
     * user is offered instead so the block stays usable.
     *
     * @return array User ids mapped to full names.
     */
    protected function get_instructor_options(): array {
        $courseid = (int) $this->page->course->id;
        if (empty($courseid) || $courseid == SITEID) {
            return [];
        }

        $coursecontext = context_course::instance($courseid);

        $roleids = $this->get_instructor_role_ids();
        $users = [];
        if (!empty($roleids)) {
            $users = get_role_users($roleids, $coursecontext);
        }
        if (empty($users)) {
            $users = get_enrolled_users($coursecontext, '', 0, 'u.*', null, 0, 0, true);
        }

        $options = [];
        foreach ($users as $user) {
            $options[$user->id] = fullname($user);
        }
        core_collator::asort($options);

        return $options;
    }

    /**
     * Resolve the configured instructor role ids.
     *
     * Falls back to the roles with the editingteacher archetype when the site setting
     * has not been stored yet.
     *
     * @return int[] The role ids that identify an instructor.
     */
    protected function get_instructor_role_ids(): array {
        $configured = get_config('block_myinstructor', 'instructorroles');

        if ($configured === false) {
            $roleids = [];
            foreach (get_archetype_roles('editingteacher') as $role) {
                $roleids[] = (int) $role->id;
            }
            return $roleids;
        }

        if ($configured === '') {
            return [];
        }

        return array_values(array_filter(array_map('intval', explode(',', $configured))));
    }
}
