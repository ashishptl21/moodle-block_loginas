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

namespace block_loginas\external;

if (!class_exists(\core_external\external_api::class)) {
    class_alias(\external_api::class, \core_external\external_api::class);
    class_alias(\external_function_parameters::class, \core_external\external_function_parameters::class);
    class_alias(\external_multiple_structure::class, \core_external\external_multiple_structure::class);
    class_alias(\external_single_structure::class, \core_external\external_single_structure::class);
    class_alias(\external_value::class, \core_external\external_value::class);
}

use context_course;
use context_system;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use core_user;
use core_user\external\user_summary_exporter;

/**
 * Class get_users
 *
 * @package    block_loginas
 * @copyright  2026 Treesha Infotech <dev@treeshainfotech.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_users extends external_api {
    /**
     * Parameters structure for web service
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'query' => new external_value(PARAM_RAW, 'Query string (full or partial user full name or other details)'),
            'courseid' => new external_value(PARAM_INT, 'Course id (0 if none)', VALUE_DEFAULT, 0),
            'limit' => new external_value(PARAM_INT, 'Results limit', VALUE_DEFAULT, 30),
        ]);
    }

    /**
     * Web service execution function
     * @param string $query Search query
     * @param int $courseid Course id if provided or 0
     * @param int $limit Search resluts limit
     */
    public static function execute($query, $courseid, $limit) {
        global $CFG, $PAGE;

        $params = self::validate_parameters(
            self::execute_parameters(),
            [
                'query' => $query,
                'courseid' => $courseid,
                'limit' => $limit,
            ]
        );

        $systemcontext = context_system::instance();
        self::validate_context($systemcontext);

        $query = $params['query'];
        $courseid = $params['courseid'];
        $limit = $params['limit'];

        if ($courseid) {
            $coursecontext = context_course::instance($courseid);
            require_capability('moodle/user:loginas', $coursecontext);
        } else {
            $coursecontext = null;
            require_capability('moodle/user:loginas', $systemcontext);
        }

        if (!empty($CFG->forceloginforprofiles)) {
            if (!isloggedin() || isguestuser()) {
                return [];
            }
        }

        $users = core_user::search($query, $coursecontext, $limit);
        $users = array_filter($users, function ($user) {
            global $USER;
            return $user->id != $USER->id && !is_siteadmin($user->id);
        });

        $result = [];
        $renderer = $PAGE->get_renderer('core');
        foreach ($users as $user) {
            $fulldetails = (new user_summary_exporter($user))->export($renderer);
            $result[] = [
                'id' => $fulldetails->id,
                'fullname' => $fulldetails->fullname,
                'email' => $fulldetails->email,
                'profileimageurlsmall' => $fulldetails->profileimageurlsmall,
            ];
        }

        return $result;
    }

    /**
     * Parameters structure for web service
     * @return external_multiple_structure
     */
    public static function execute_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'id' => new external_value(PARAM_INT, 'User id'),
                'fullname' => new external_value(PARAM_RAW, 'Full name as text'),
                'email' => new external_value(PARAM_EMAIL, 'User email'),
                'profileimageurlsmall' => new external_value(PARAM_URL, 'URL to small profile image'),
            ])
        );
    }
}
