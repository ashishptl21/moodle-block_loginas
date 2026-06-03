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
 *
 * @module     block_loginas/confirm
 * @copyright  2026 Treesha Infotech <info@treeshainfotech.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['core/notification', 'core/str'], function(Notification, Str) {

    return {
        init: function() {

            document.addEventListener('submit', function(e) {

                const form = e.target;

                if (!form.closest('.block_loginas')) {
                    return;
                }

                const userselect = form.elements.user;

                if (!userselect || !userselect.value) {
                    return;
                }

                e.preventDefault();

                Promise.all([
                    Str.get_string('confirmloginas', 'block_loginas'),
                    Str.get_string('confirm'),
                    Str.get_string('continue'),
                    Str.get_string('cancel')
                ]).then(function(strings) {

                    Notification.confirm(
                        strings[1],
                        strings[0],
                        strings[2],
                        strings[3],
                        function() {
                            form.submit();
                        }
                    );

                }).catch(Notification.exception);
            });
        }
    };
});