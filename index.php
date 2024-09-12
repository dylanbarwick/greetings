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
 * TODO describe file index
 *
 * @package    local_greetings
 * @copyright  2024 YOUR NAME <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot. '/local/greetings/lib.php');
$context = \core\context\system::instance();
$PAGE->set_context($context);

$url = new moodle_url('/local/greetings/index.php', []);
$PAGE->set_url($url);

$PAGE->set_pagelayout('standard');

$PAGE->set_title(get_string('pluginname', 'local_greetings'));

require_login();

$PAGE->set_heading(get_string('pluginname', 'local_greetings'));

$messageform = new \local_greetings\form\message_form();

if ($data = $messageform->get_data()) {
    $message = required_param('message', PARAM_TEXT);

    if (!empty($message)) {
        $record = new stdClass;
        $record->message = $message;
        $record->timecreated = time();
        $record->userid = $USER->id;

        $DB->insert_record('local_greetings_messages', $record);
    }
}

echo $OUTPUT->header();
if (isloggedin()) {
    echo '<h2>' . get_string('greetinguserloggedin', 'local_greetings', fullname($USER)) . '</h2>';
    echo '<div>' . local_greetings_get_greeting($USER) . '</div>';
    $now = time();
    echo '<div>' . userdate($now) . '</div>';
    echo '<div>' . userdate(time(), get_string('strftimedaydate', 'core_langconfig')) . '</div>';
    $date = new DateTime("tomorrow", core_date::get_user_timezone_object());
    $date->setTime(0, 0, 0);
    echo '<div>' . userdate($date->getTimestamp(), get_string('strftimedatefullshort', 'core_langconfig')) . '</div>';
    $grade = 20.00 / 3;
    echo '<div>' . format_float($grade, 2) . '</div>';
} else {
    echo '<h2>' . get_string('greetinguseranon', 'local_greetings') .'</h2>';
}

$messageform->display();

$userfields = \core_user\fields::for_name()->with_identity($context);
$userfieldssql = $userfields->get_sql('u');

$sql = "SELECT m.id, m.message, m.timecreated, m.userid {$userfieldssql->selects}
          FROM {local_greetings_messages} m
          LEFT JOIN {user} u ON u.id = m.userid
          ORDER BY timecreated DESC";

$messages = $DB->get_records_sql($sql);

echo $OUTPUT->box_start('card-columns');

foreach ($messages as $m) {
    echo html_writer::start_tag('div', ['class' => 'card', 'id' => 'message-' . $m->id, 'data-messageid' => $m->id]);
    echo html_writer::start_tag('div', ['class' => 'card-body']);
    echo html_writer::tag('p', $m->message, ['class' => 'card-text']);
    echo html_writer::start_tag('p', ['class' => 'card-text']);
    echo html_writer::tag(
        'p',
        get_string(
            'postedby',
            'local_greetings',
            $m->firstname . ' ' . $m->lastname
          ), ['class' => 'card-text']);
    echo html_writer::tag('small', userdate($m->timecreated), ['class' => 'text-muted']);
    echo html_writer::end_tag('p');
    echo html_writer::end_tag('div');
    echo html_writer::end_tag('div');
}

echo $OUTPUT->box_end();
echo $OUTPUT->footer();
