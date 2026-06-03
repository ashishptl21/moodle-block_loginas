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
 * @module     block_loginas/form-search-users
 * @copyright  2026 Treesha Infotech <info@treeshainfotech.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as Ajax from 'core/ajax';
import * as Templates from 'core/templates';


export const processResults = (selector, results) => {
    return results.map((user) => ({
        value: user.id,
        label: user._label
    }));
};

export const transport = (selector, query, success, failure) => {
    const args = {
        query: query,
        courseid: 0
    };
    const select = document.querySelector(selector);
    const courseid = select.getAttribute('data-withincourseid');
    if (typeof courseid !== 'undefined') {
        args.courseid = parseInt(courseid);
    }

    const promise = Ajax.call([{
        methodname: 'block_loginas_get_users',
        args
    }]);

    promise[0].then((results) => {
        const promises = results.map((user) => {
            return Templates.render('block_loginas/form-user-selector-suggestion', user);
        });

        // Apply the label to the results.
        return Promise.all(promises).then((args) => {
            results.forEach((user, index) => {
                user._label = args[index];
            });
            success(results);
            return;
        });

    }).fail(failure);
};
