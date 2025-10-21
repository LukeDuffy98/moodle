<?php
// This file is part of Moodle - http://moodle.org/

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/staticpages/locallib.php');

/**
 * Install hook to seed the default static pages.
 */
function xmldb_local_staticpages_install(): void {
    local_staticpages_seed_default_pages();
}
