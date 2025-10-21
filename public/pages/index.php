<?php
// This file is part of Moodle - http://moodle.org/

require(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/local/staticpages/locallib.php');

$slug = optional_param('slug', '', PARAM_RAW_TRIMMED);

if ($slug === '' && !empty($_SERVER['PATH_INFO'])) {
    $slug = ltrim($_SERVER['PATH_INFO'], '/');
}

$slug = trim($slug, '/');

if ($slug === '') {
    // Defer to the plugin index page when no slug provided.
    require($CFG->dirroot . '/local/staticpages/index.php');
    exit;
}

$slug = core_text::strtolower($slug);
$slug = preg_replace('/[^a-z0-9\-]+/u', '-', $slug);
$slug = trim($slug, '-');

if ($slug === '') {
    require($CFG->dirroot . '/local/staticpages/index.php');
    exit;
}

$pageurl = new moodle_url('/pages/' . $slug);
local_staticpages_render_page($slug, $pageurl);
