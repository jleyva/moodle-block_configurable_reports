# Configurable Reports block for Moodle (`block_configurable_reports`)

A Moodle block plugin that lets administrators and teachers build custom
reports (SQL, courses, users, categories, timeline, …) and embed them on
the site front page, courses or the user dashboard. Reports are saved,
shareable and exportable, and SQL reports support placeholders so the
same query can be reused across contexts.

## Try in Moodle Playground

Click the badge below to open this plugin instantly in
[Moodle Playground](https://moodle-playground.com) — a full Moodle site
running in the browser, with no local install. The demo boots with the
plugin installed, a sample SQL report ready to run, and lands on the
reports management page.

<a href="https://moodle-playground.com/?blueprint-url=https://raw.githubusercontent.com/jleyva/moodle-block_configurable_reports/refs/heads/MOODLE_4x_STABLE/blueprint.json" target="_blank" rel="noopener"><img src="https://raw.githubusercontent.com/ateeducacion/action-moodle-playground-pr-preview/refs/heads/main/assets/playground-preview-button.svg" alt="Preview in Moodle Playground" width="200"></a>

## Installation

Install it like any other Moodle block, by copying this directory into
`blocks/configurable_reports` and visiting the notifications page. Usage
documentation, tutorials and the SQL placeholder reference are in the
[Moodle docs](https://docs.moodle.org/en/Configurable_reports).

Released versions are listed in [`CHANGELOG.md`](CHANGELOG.md).

## Security of SQL reports

SQL reports run their query on the Moodle database, so whoever writes them can
read any data in it, including session ids, web service tokens and password
hashes. Treat `block/configurable_reports:managesqlreports` as equivalent to
site administration:

- Only grant the capability to site administrators. No role has it by default.
- List the users allowed to write SQL reports in the *SQL report users*
  (`allowedsqlusers`) setting.
- The *SQL Security* setting refuses queries that are not `SELECT` or `WITH`
  statements, and a list of keywords and functions that change data. It is a
  safeguard, not a sandbox: queries run with the database account Moodle uses,
  which can change any data.

## Credits

Author: Juan Leyva (<https://twitter.com/jleyvadelgado>)

Thanks to:

- Sara Arjona for being a co-maintainer
- Nadav Kavalerchik for developing amazing new features
- Ivan Breziansky for translating the block to Slovak
- Iñaki Arenaza for translating the block documentation to Spanish
- Luis de Vasconcelos for testing the block
- Adam Olley and NetSpot Moodle Partner for improving parts of the Moodle 2 version

Some functionality uses code from
[Admin Report: Custom SQL queries](https://moodle.org/mod/data/view.php?d=13&rid=2884)
by Tim Hunt.

## Working on the plugin

The bundled third party libraries (CodeMirror, DataTables, TableSorter and
pChart) are listed in [`thirdpartylibs.xml`](thirdpartylibs.xml), and
[`readme_moodle.txt`](readme_moodle.txt) says how to import a new version of
each of them.
