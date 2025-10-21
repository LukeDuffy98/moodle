<?php
// This file is part of Moodle - http://moodle.org/

require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/locallib.php');

$context = local_staticpages_require_access();

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/staticpages/index.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('index_title', 'local_staticpages'));
$PAGE->set_heading(format_string($SITE->fullname));

$includehidden = local_staticpages_can_manage();
$pages = [];
foreach (local_staticpages_get_all_pages($includehidden) as $page) {
    if (!$page->visible && !$includehidden) {
        continue;
    }

    $pageurl = new moodle_url('/pages/' . $page->slug);

    $pages[] = [
        'url' => $pageurl->out(false),
        'title' => format_string($page->title),
        'summary' => format_text($page->summary ?? '', FORMAT_HTML, [
            'context' => $context,
            'trusted' => true,
            'noclean' => true,
        ]),
        'draft' => empty($page->visible),
    ];
}

echo $OUTPUT->header();

echo $OUTPUT->render_from_template('local_staticpages/index', [
    'title' => get_string('index_title', 'local_staticpages'),
    'description' => get_string('index_description', 'local_staticpages'),
    'pages' => $pages,
    'haspages' => !empty($pages),
]);

echo $OUTPUT->footer();
