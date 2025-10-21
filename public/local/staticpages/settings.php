<?php
// This file is part of Moodle - http://moodle.org/

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $ADMIN->add('localplugins', new admin_externalpage(
        'local_staticpages_manage',
        get_string('managepages', 'local_staticpages'),
        new moodle_url('/local/staticpages/manage.php'),
        ['local/staticpages:manage']
    ));
}
