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

namespace local_greetings\form;

use moodleform;

defined('MOODLE_INTERNAL') || die();

// The class moodleform is defined in formslib.php.
require_once("$CFG->libdir/formslib.php");

/**
 * Class myform
 *
 * @package    local_greetings
 * @copyright  2024 DB 2024 db@example.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class myform extends moodleform {
    /**
     * Definition of the form elements.
     */
    public function definition() {
        // A reference to the form is stored in $this->_form.
        // A common convention is to store it in a variable, such as `$mform`.
        $mform = $this->_form; // Don't forget the underscore!
        // Add elements to your form.
        $mform->addElement('text', 'email', get_string('email'));
        // Set type of element.
        $mform->setType('email', PARAM_NOTAGS);
        // Default value.
        $mform->setDefault('email', 'Please enter email');
    }

    /**
     * Validation of the form data.
     *
     * @param array $data
     * @param array $files
     *
     * @return array
     */
    public function validation($data, $files): array {
        return [];
    }
}
