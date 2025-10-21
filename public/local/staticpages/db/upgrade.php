<?php
// This file is part of Moodle - http://moodle.org/

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/staticpages/locallib.php');

/**
 * Upgrade hook for local_staticpages.
 */
function xmldb_local_staticpages_upgrade(int $oldversion): bool {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2025102101) {
        $file = $CFG->dirroot . '/local/staticpages/db/install.xml';
        $dbman->install_from_xmldb_file($file);

        local_staticpages_seed_default_pages();

        upgrade_plugin_savepoint(true, 2025102101, 'local', 'staticpages');
    }

    if ($oldversion < 2025102200) {
        $table = new xmldb_table('local_staticpages');

        $visible = new xmldb_field('visible', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'sortorder');
        if (!$dbman->field_exists($table, $visible)) {
            $dbman->add_field($table, $visible);
        }

        $showinnav = new xmldb_field('showinnav', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'visible');
        if (!$dbman->field_exists($table, $showinnav)) {
            $dbman->add_field($table, $showinnav);
        }

        $DB->execute('UPDATE {local_staticpages} SET visible = 1 WHERE visible IS NULL');
        $DB->execute('UPDATE {local_staticpages} SET showinnav = 0 WHERE showinnav IS NULL');

        local_staticpages_seed_default_pages();

        upgrade_plugin_savepoint(true, 2025102200, 'local', 'staticpages');
    }

    return true;
}
