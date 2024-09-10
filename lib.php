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
 * Callback implementations for Greetings doodle
 *
 * @package    local_greetings
 * @copyright  2024 DB 2024 db@example.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Get the greeting for the user
 *
 * @param stdClass $user
 *  The user object
 * @return string
 */
function local_greetings_get_greeting($user) {
    if ($user == null) {
        return get_string('greetinguser', 'local_greetings');
    }

    $country = $user->country;
    switch ($country) {
        case 'ES':
            $langstr = 'greetinguseres';
            break;
        case 'NZ':
            $langstr = 'greetingusernz';
            break;
        case 'FJ':
            $langstr = 'greetinguserfj';
            break;
        case 'AU':
            $langstr = 'greetinguserau';
            break;
        default:
            $langstr = 'greetinguserloggedin';
            break;
    }

    return get_string($langstr, 'local_greetings', fullname($user));
}

/**
 * Insert a link to index.php on the site front page navigation menu.
 *
 * @param navigation_node $frontpage
 *   Node representing the front page in the navigation tree.
 */
function local_greetings_extend_navigation_frontpage(navigation_node $frontpage) {
    $frontpage->add(
        get_string('pluginname', 'local_greetings') . ' (front page)',
        new moodle_url('/local/greetings/index.php'),
        navigation_node::TYPE_CUSTOM,
    );
}

/**
 * Insert a link to index.php on the site navigation menu.
 *
 * @param global_navigation $root
 *   Node representing the navigation tree.
 */
function local_greetings_extend_navigation(global_navigation $root) {
    $node = navigation_node::create(
        get_string('pluginname', 'local_greetings') . ' (site)',
        new moodle_url('/local/greetings/index.php'),
        navigation_node::TYPE_CUSTOM,
        null,
        null,
        new pix_icon('t/message', '')
    );

    $node->showinflatnavigation = true;
    $root->add_node($node);
}
