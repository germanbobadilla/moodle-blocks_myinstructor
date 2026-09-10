# Changelog

All notable changes to this plugin are documented here.

## 1.0.0 - 2026-09-09

* Initial release.
* Shows a configured course instructor: picture, full name, group(s) and a
  **Send message** button.
* Visible to a student only when the student shares a group with the instructor;
  `block/myinstructor:viewall` and page editing bypass the restriction.
* Site setting **Instructor roles** controls which roles may be picked as an
  instructor (defaults to the Teacher / `editingteacher` role).
* Per-instance **Description** field (plain text, up to 100 words) so the
  instructor can introduce themselves or list office hours.
* Supports Moodle 4.0 to 5.3.
