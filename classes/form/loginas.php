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

namespace block_loginas\form;

use Override;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Class loginas
 *
 * @package    block_loginas
 * @copyright  2026 Treesha Infotech <dev@treeshainfotech.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class loginas extends \moodleform {
    /**
     * form definition.
     * @return void
     */
    public function definition() {
        $form = $this->_form;

        $form->addElement('hidden', 'id');
        $form->setType('id', PARAM_INT);

        $form->addElement(
            'autocomplete',
            'user',
            get_string('selectuser', 'block_loginas'),
            [],
            [
                'ajax' => 'block_loginas/form-search-users',
            ]
        );
        $form->addRule('user', get_string('required', 'core'), 'required', null, 'client');
        $form->setType('user', PARAM_INT);

        $form->addElement('submit', 'submitbutton', get_string('loginas', 'block_loginas'));

        $form->setRequiredNote('');
        $this->set_display_vertical();
    }

    /**
     * Set search within page course id
     * @return void
     */
    public function definition_after_data() {
        $form = $this->_form;
        $courseid = $form->exportValue('id');
        if ($courseid > get_site()->id) {
            $form->getElement('user')->updateAttributes([
                'data-withincourseid' => $courseid,
            ]);
        }
    }
}
