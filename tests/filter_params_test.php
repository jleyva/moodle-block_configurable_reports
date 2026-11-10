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
 * Unit tests for the filter values of the request
 *
 * @package    block_configurable_reports
 * @copyright  2026 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_configurable_reports;

use advanced_testcase;
use PHPUnit\Framework\Attributes\CoversClass;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/blocks/configurable_reports/locallib.php');
require_once($CFG->dirroot . '/blocks/configurable_reports/report.class.php');
require_once($CFG->dirroot . '/blocks/configurable_reports/reports/sql/report.class.php');

/**
 * Tests for the filter values the report reads from the request and carries on in its links
 *
 * @package    block_configurable_reports
 * @covers     \report_base
 */
#[CoversClass(\report_base::class)]
final class filter_params_test extends advanced_testcase {

    /** @var string[] Request parameters set by the test, removed in tearDown() */
    private array $requestparams = [];

    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->setAdminUser();

        global $DB, $remotedb;
        $remotedb = $DB;
    }

    protected function tearDown(): void {
        foreach ($this->requestparams as $name) {
            unset($_GET[$name]);
        }
        parent::tearDown();
    }

    /**
     * Set request parameters, as the filter form or a report link submits them.
     *
     * @param array $request
     */
    private function set_request(array $request): void {
        foreach ($request as $name => $value) {
            $_GET[$name] = $value;
            $this->requestparams[] = $name;
        }
    }

    /**
     * Create a SQL report with a text and a date filter.
     *
     * @return \report_sql
     */
    private function create_report(): \report_sql {
        global $SITE, $USER;

        $components = [
            'customsql' => ['config' => (object) ['querysql' => 'SELECT id FROM prefix_user']],
            'filters' => [
                'elements' => [
                    [
                        'id' => 'f1',
                        'pluginname' => 'searchtext',
                        'formdata' => (object) ['idnumber' => 'name', 'label' => 'Name'],
                    ],
                    [
                        'id' => 'f2',
                        'pluginname' => 'startendtime',
                        'formdata' => new \stdClass(),
                    ],
                ],
            ],
        ];
        $report = (object) ['id' => 7, 'courseid' => $SITE->id, 'ownerid' => $USER->id, 'visible' => 1, 'type' => 'sql',
            'name' => 'Report', 'summary' => '', 'components' => cr_serialize($components), 'remote' => 0,
            'export' => 'csv,'];

        return new \report_sql($report);
    }

    /**
     * Only the values of the report filters, and filter_var, are read from the request.
     */
    public function test_the_filter_form_names_the_values_read(): void {
        global $PAGE;

        $PAGE->set_url('/blocks/configurable_reports/viewreport.php', ['id' => 7]);
        $starttime = ['day' => '2', 'month' => '3', 'year' => '2026', 'hour' => '10', 'minute' => '0'];
        $this->set_request([
            'filter_searchtext_name' => 'a+b',
            'filter_starttime' => $starttime,
            'filter_var' => '5',
            'filter_unknown' => 'x',
            'debug' => '1',
        ]);

        $report = $this->create_report();
        $this->assertSame(['filter_var' => '5'], $report->get_filter_params());

        $report->check_filters_request();
        $this->assertSame([
            'filter_searchtext_name' => 'a+b',
            'filter_starttime' => $starttime,
            'filter_var' => '5',
        ], $report->get_filter_params());

        $export = $report->print_export_options(true);
        $this->assertStringContainsString('viewreport.php?id=7&amp;courseid=' . SITEID .
            '&amp;filter_searchtext_name=a%2Bb&amp;filter_starttime[day]=2', $export);
        $this->assertStringContainsString('&amp;filter_var=5&amp;download=1&amp;format=csv', $export);
        $this->assertStringNotContainsString('filter_unknown', $export);
        $this->assertStringNotContainsString('debug', $export);
    }
}
