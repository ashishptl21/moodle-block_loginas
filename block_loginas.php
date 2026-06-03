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

use block_loginas\form\loginas;
use core\session\manager;

/**
 * Block block_loginas
 *
 * Documentation: {@link https://moodledev.io/docs/apis/plugintypes/blocks}
 *
 * @package    block_loginas
 * @copyright  2026 Treesha Infotech <dev@treeshainfotech.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_loginas extends block_base {
    /**
     * Block initialisation
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_loginas');
    }

    /**
     * Get content
     *
     * @return stdClass
     */
    public function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }
        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        if (!isloggedin() || isguestuser()) {
            return $this->content;
        }

        $renderer = $this->page->get_renderer('core');
        $context = context_system::instance();
        if ($this->page->context->contextlevel >= CONTEXT_COURSE) {
            $context = $this->page->context->get_course_context();
        }

        if (manager::is_loggedinas()) {
            $returnurl = new moodle_url(
                '/blocks/loginas/adminlogin.php',
                [
                    'sesskey' => sesskey(),
                    'loginwithrealuser' => 1,
                ]
            );

            if ($context instanceof context_course) {
                $returnurl->param('id', $context->instanceid);
            }

            $button = $renderer->single_button(
                $returnurl,
                get_string('returntorealuser', 'block_loginas')
            );

            $this->content->text = $button;

            return $this->content;
        }

        if (get_config('block_loginas', 'showonlysiteadmin') && !is_siteadmin()) {
            return $this->content;
        }

        if (!has_capability('moodle/user:loginas', $context)) {
            return $this->content;
        }

        $this->page->requires->js_call_amd('block_loginas/confirm', 'init');

        $mform = new loginas(new moodle_url('/course/loginas.php'));
        $mform->set_data(['id' => $this->page->course->id]);
        $this->content->text = $mform->render();

        return $this->content;
    }

    /**
     * Config for blocks
     * @return bool
     */
    public function has_config() {
        return true;
    }

    /**
     * All Formate.
     * @return array{all: bool}
     */
    public function applicable_formats() {
        return [
            'all' => true,
        ];
    }
}
