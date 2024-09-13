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

$PAGE->set_heading(get_string('pluginname', 'local_greetings'));

// No anons allowed.
require_login();

// No guests allowed.
if (isguestuser()) {
    throw new moodle_exception('noguest');
}

// Declare capabilities.
$allowpost = has_capability('local/greetings:postmessages', $context);
$allowview = has_capability('local/greetings:viewmessages', $context);
$deleteanypost = has_capability('local/greetings:deleteanymessage', $context);
$deleteownpost = has_capability('local/greetings:deleteownmessage', $context);

// Delete a message.
$action = optional_param('action', '', PARAM_TEXT);

if ($action == 'del') {
    $id = required_param('id', PARAM_TEXT);

    if ($deleteanypost) {
        $params = ['id' => $id];
        $DB->delete_records('local_greetings_messages', $params);
    }
    else if ($deleteownpost) {
        $params = ['id' => $id];
        $message = $DB->get_record('local_greetings_messages', $params);

        if ($message->userid == $USER->id) {
            $DB->delete_records('local_greetings_messages', $params);
        }
    }
}

// Declare message form.
$messageform = new \local_greetings\form\message_form();

// Have we just submitted the form?
if ($data = $messageform->get_data()) {
    require_capability('local/greetings:postmessages', $context);
    $message = required_param('message', PARAM_TEXT);

    if (!empty($message)) {
        $record = new stdClass;
        $record->message = $message;
        $record->timecreated = time();
        $record->userid = $USER->id;

        $DB->insert_record('local_greetings_messages', $record);
        redirect($PAGE->url);
    }
}

echo $OUTPUT->header();
// Print out a bunch of stuff from the user's account.
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

// Display the form if the user is allowed to post stuff.
if ($allowpost) {
    $messageform->display();
}

// Display the messages if the user is allowed to view them.
if ($allowview) {
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
        echo html_writer::tag('p', format_text($m->message, FORMAT_PLAIN), ['class' => 'card-text']);
        echo html_writer::start_tag('p', ['class' => 'card-text']);
        echo html_writer::tag(
            'p',
            get_string(
                'postedby',
                'local_greetings',
                $m->firstname . ' ' . $m->lastname
              ), ['class' => 'card-text']);
        echo html_writer::tag('small', userdate($m->timecreated), ['class' => 'text-muted']);
        // Display a delete link if the user can delete this message.
        if ($deleteanypost || ($deleteownpost && $m->userid == $USER->id)) {
            echo html_writer::start_tag('p', ['class' => 'card-footer text-center']);
            echo html_writer::link(
                new moodle_url(
                    '/local/greetings/index.php',
                    [
                        'action' => 'del',
                        'id' => $m->id,
                    ]
                ),
                $OUTPUT->pix_icon('t/delete', '') . get_string('delete')
            );
            echo html_writer::end_tag('p');
        }
        echo html_writer::end_tag('p');
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('div');
    }

    echo $OUTPUT->box_end();
}

echo $OUTPUT->footer();
