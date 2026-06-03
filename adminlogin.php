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
 * TODO describe file adminlogin
 *
 * @package    block_loginas
 * @copyright  2026 Treesha Infotech <dev@treeshainfotech.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\session\manager;

require_once(dirname(__FILE__, 3) . '/config.php');

require_login();

$loginwithrealuser = required_param('loginwithrealuser', PARAM_INT);
$id = optional_param('id', SITEID, PARAM_INT);

$redirecturl = new moodle_url('/my');
if ($id > SITEID) {
    $redirecturl = new moodle_url('/course/view.php', ['id' => $id]);
}

$url = new moodle_url('/blocks/loginas/adminlogin.php', []);
$context = context_system::instance();

$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_title(get_string('loginas', 'block_loginas'));
$PAGE->set_heading(get_string('loginas', 'block_loginas'));

if (WS_SERVER || AJAX_SCRIPT) {
    redirect($redirecturl);
    return;
}

if (manager::is_loggedinas() && $loginwithrealuser && confirm_sesskey()) {
    $realuser = manager::get_realuser();
     // phpcs:ignore moodle.NamingConventions.ValidVariableName.VariableNameLowerCase
    $GLOBALS['SESSION'] = $_SESSION['REALSESSION'];
    $_SESSION = [];
     // phpcs:ignore moodle.NamingConventions.ValidVariableName.VariableNameLowerCase
    $_SESSION['SESSION'] =& $GLOBALS['SESSION'];
    $user = get_complete_user_data('id', $realuser->id);
    manager::set_user($user);
}

redirect($redirecturl);
