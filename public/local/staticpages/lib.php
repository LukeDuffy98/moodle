<?php
// This file is part of Moodle - http://moodle.org/

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/locallib.php');

/**
 * Extend the global navigation with static page links.
 */
function local_staticpages_extend_navigation(global_navigation $nav): void {
    if (!local_staticpages_table_exists()) {
        return;
    }

    $context = context_system::instance();
    if (!has_capability('local/staticpages:view', $context, null, false)) {
        return;
    }

    $pages = local_staticpages_get_navigation_pages();
    if (empty($pages)) {
        return;
    }

    $key = 'local_staticpages_root';
    $existing = $nav->find($key, navigation_node::TYPE_CUSTOM);
    if ($existing) {
        $existing->remove();
    }

    $rootnode = $nav->add(
        get_string('pluginname', 'local_staticpages'),
        new moodle_url('/local/staticpages/index.php'),
        navigation_node::TYPE_CUSTOM,
        null,
        $key
    );

    foreach ($pages as $page) {
        $rootnode->add(
            format_string($page->title, true, ['context' => $context]),
            new moodle_url('/local/staticpages/view.php', ['slug' => $page->slug])
        );
    }
}
