@block @block_myinstructor
Feature: The My instructor block is only visible to students in the instructor's group
  In order to reach the right instructor
  As a student
  I need to see the instructor block only when I share their group

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "users" exist:
      | username | firstname | lastname |
      | teacher1 | Teacher   | One      |
      | student1 | Student   | One      |
      | student2 | Student   | Two      |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group A | C1     | GA       |
    And the following "group members" exist:
      | group | user     |
      | GA    | teacher1 |
      | GA    | student1 |

  @javascript
  Scenario: A student in the instructor's group sees the instructor block
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "My instructor" block
    And I configure the "My instructor" block
    And I set the following fields to these values:
      | Instructor | Teacher One |
    And I press "Save changes"
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    Then I should see "Teacher One" in the "My instructor" "block"
    And I should see "Group A" in the "My instructor" "block"
    And "Send message" "link" should exist in the "My instructor" "block"

  @javascript
  Scenario: A student outside the instructor's group does not see the instructor block
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "My instructor" block
    And I configure the "My instructor" block
    And I set the following fields to these values:
      | Instructor | Teacher One |
    And I press "Save changes"
    And I log out
    When I log in as "student2"
    And I am on "Course 1" course homepage
    Then "My instructor" "block" should not exist
