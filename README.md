# My instructor

**My instructor** is a Moodle block that shows a course's instructor — their
profile picture, name, group membership, an optional self-description and a
**Send message** button — but only to the students who share a group with that
instructor.

## Description

The block is added inside a course and configured with a single **instructor**
(any user who holds one of the configured instructor roles in that course). It
then shows, to the students who share at least one group with that instructor:

* the instructor's profile picture and name, linking to their profile;
* the group or groups the instructor belongs to in the course;
* an optional free-text description (up to 100 words) where the instructor can
  introduce themselves or list their office hours;
* a **Send message** button that opens a conversation with the instructor.

Students who do not share a group with the instructor do not see the block at
all. Users who hold `block/myinstructor:viewall` (non-editing teachers, editing
teachers and managers by default) always see it, and so does anyone while
editing is turned on, so the block can be configured. If the instructor belongs
to no group, no student can see the block and staff are shown a notice
explaining why.

Which roles count as an "instructor" is a site setting, defaulting to the
**Teacher** (`editingteacher`) role. More than one instance is allowed per
course, so a course with several groups can show one instructor block per group.

The block stores no personal data of its own; it only displays profile
information of an existing user chosen in the block configuration.

## Requirements

* Moodle 4.0 to 5.3 (`$plugin->requires = 2022041900`,
  `$plugin->supported = [400, 503]`).

## Installation

Install the plugin like any other Moodle block plugin, in `blocks/myinstructor`
(`public/blocks/myinstructor` on Moodle 5.1 and later):

```sh
git clone https://github.com/germanbobadilla/moodle-block_myinstructor.git blocks/myinstructor
```

Then log in as an admin and visit *Site administration > Notifications* to
complete the installation, or run:

```sh
php admin/cli/upgrade.php
```

See <https://docs.moodle.org/en/Installing_plugins> for more general
installation help.

## Usage

Under *Site administration > Plugins > Blocks > My instructor*, set
**Instructor roles** — the roles that identify a user as an instructor. Only
users who hold one of these roles in a course can be chosen in that course's
instructor blocks. It defaults to the **Teacher** role; if no user in the course
holds one of the selected roles, every enrolled user is offered instead.

In a course, turn editing on and add the **My instructor** block, then open its
actions menu and choose *Configure My instructor block*:

| Setting | Description |
| --- | --- |
| Block title | Optional title that replaces the default "My instructor". |
| Instructor | The user the block shows. Required. |
| Description | Optional plain-text description, up to 100 words. |
| Show the "Send message" button | On by default; hidden automatically when site messaging is disabled or the viewer is the instructor. |
| Show the instructor's group(s) | On by default. |

## Capabilities

| Capability | Description |
| --- | --- |
| `block/myinstructor:addinstance` | Add a My instructor block to a course. |
| `block/myinstructor:myaddinstance` | Add a My instructor block to the Dashboard. |
| `block/myinstructor:viewall` | See the block without sharing a group with the instructor. |

## Support

Please use the [GitHub issue tracker](https://github.com/germanbobadilla/moodle-block_myinstructor/issues)
to report bugs or request features.

## License

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE. See the
[GNU General Public License](https://www.gnu.org/licenses/gpl-3.0.html) for more
details.
