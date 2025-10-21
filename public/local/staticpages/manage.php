<?php
// This file is part of Moodle - http://moodle.org/

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->dirroot . '/local/staticpages/locallib.php');
require_once($CFG->dirroot . '/local/staticpages/classes/form/page_form.php');
require_once($CFG->dirroot . '/local/staticpages/classes/form/import_form.php');

use local_staticpages\form\import_form;
use local_staticpages\form\page_form;

$action = optional_param('action', 'list', PARAM_ALPHA);
$id = optional_param('id', 0, PARAM_INT);

$context = context_system::instance();
require_login();
require_capability('local/staticpages:manage', $context);

admin_externalpage_setup('local_staticpages_manage');

$redirecturl = new moodle_url('/local/staticpages/manage.php');
$PAGE->set_context($context);

switch ($action) {
    case 'export':
        require_sesskey();
        $pages = array_values(local_staticpages_get_all_pages(true));
        $payload = [];
        foreach ($pages as $page) {
            $payload[] = [
                'slug' => $page->slug,
                'title' => $page->title,
                'heading' => $page->heading,
                'summary' => $page->summary,
                'content' => $page->content,
                'sortorder' => (int) $page->sortorder,
                'visible' => (int) $page->visible,
                'showinnav' => (int) $page->showinnav,
            ];
        }

        $json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $filename = 'staticpages-' . gmdate('Ymd-His') . '.json';
        send_file_from_string($json, $filename, 0, 0, true, [
            'contenttype' => 'application/json',
            'filename' => $filename,
        ]);
        exit;

    case 'delete':
        $page = local_staticpages_get_page_by_id($id);
        if (!$page) {
            redirect($redirecturl, get_string('error:pagenotfound', 'local_staticpages'), 0, \core\output\notification::NOTIFY_WARNING);
        }

        if (optional_param('confirm', 0, PARAM_BOOL) && confirm_sesskey()) {
            local_staticpages_delete_page($page->id);
            redirect($redirecturl, get_string('pagedeleted', 'local_staticpages'), 0, \core\output\notification::NOTIFY_SUCCESS);
        }

        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('deletepage', 'local_staticpages'));
        $message = get_string('confirmdelete', 'local_staticpages', format_string($page->title));
        $yesurl = new moodle_url('/local/staticpages/manage.php', ['action' => 'delete', 'id' => $id, 'confirm' => 1, 'sesskey' => sesskey()]);
        $nourl = $redirecturl;
        echo $OUTPUT->confirm($message, $yesurl, $nourl);
        echo $OUTPUT->footer();
        exit;

    case 'togglevisibility':
    case 'togglenav':
        require_sesskey();
        $page = local_staticpages_get_page_by_id($id);
        if ($page) {
            if ($action === 'togglevisibility') {
                $page->visible = $page->visible ? 0 : 1;
            } else {
                $page->showinnav = $page->showinnav ? 0 : 1;
            }
            local_staticpages_save_page($page);
        }
        redirect($redirecturl);
        break;

    case 'import':
        $form = new import_form($redirecturl);
        if ($form->is_cancelled()) {
            redirect($redirecturl);
        }

        if ($data = $form->get_data()) {
            require_sesskey();
            try {
                $decoded = json_decode($data->jsondata, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                redirect($redirecturl, get_string('error:invalidjson', 'local_staticpages'), 0, \core\output\notification::NOTIFY_ERROR);
            }

            if (!is_array($decoded)) {
                redirect($redirecturl, get_string('error:invalidjson', 'local_staticpages'), 0, \core\output\notification::NOTIFY_ERROR);
            }

            if ($data->importmode === 'replace') {
                foreach (local_staticpages_get_all_pages(true) as $existing) {
                    local_staticpages_delete_page($existing->id);
                }
            }

            $orderslot = 10;
            foreach ($decoded as $entry) {
                if (!is_array($entry)) {
                    continue;
                }

                $record = (object) [
                    'id' => 0,
                    'title' => $entry['title'] ?? $entry['slug'] ?? get_string('untitledpage', 'local_staticpages'),
                    'heading' => $entry['heading'] ?? ($entry['title'] ?? ''),
                    'summary' => $entry['summary'] ?? '',
                    'content' => $entry['content'] ?? '',
                    'sortorder' => isset($entry['sortorder']) ? (int) $entry['sortorder'] : $orderslot,
                    'visible' => array_key_exists('visible', $entry) ? (int) !empty($entry['visible']) : 1,
                    'showinnav' => array_key_exists('showinnav', $entry) ? (int) !empty($entry['showinnav']) : 0,
                ];

                $slugsource = $entry['slug'] ?? $record->title;
                $slug = local_staticpages_generate_slug((string) $slugsource);
                $record->slug = local_staticpages_resolve_unique_slug($slug);

                local_staticpages_save_page($record);
                $orderslot += 10;
            }

            redirect($redirecturl, get_string('importsuccess', 'local_staticpages'), 0, \core\output\notification::NOTIFY_SUCCESS);
        }

        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('importpages', 'local_staticpages'));
        $form->display();
        echo $OUTPUT->footer();
        exit;

    case 'edit':
        $page = $id ? local_staticpages_get_page_by_id($id) : null;
        $form = new page_form(null, ['page' => $page]);

        if ($form->is_cancelled()) {
            redirect($redirecturl);
        }

        if ($data = $form->get_data()) {
            require_sesskey();

            $record = new stdClass();
            $record->id = $data->id ?? 0;
            $record->title = $data->title;
            $record->heading = $data->heading ?: $data->title;
            $record->summary = $data->summary ?? '';
            $record->content = $data->content['text'] ?? '';
            $record->visible = !empty($data->visible) ? 1 : 0;
            $record->showinnav = !empty($data->showinnav) ? 1 : 0;
            $record->sortorder = isset($data->sortorder) ? (int) $data->sortorder : 0;

            $slugsource = trim($data->slug ?? '');
            if ($slugsource === '') {
                $slugsource = $record->title;
            }
            $slug = local_staticpages_generate_slug($slugsource);
            $record->slug = local_staticpages_resolve_unique_slug($slug, $record->id ?: null);

            local_staticpages_save_page($record);

            redirect($redirecturl, get_string('pagesaved', 'local_staticpages'), 0, \core\output\notification::NOTIFY_SUCCESS);
        }

        if ($page) {
            if (empty($page->content)) {
                $page->content = '';
            }
            $page->visible = (int) $page->visible;
            $page->showinnav = (int) $page->showinnav;
            $form->set_data($page);
        } else {
            $defaults = new stdClass();
            $defaults->visible = 1;
            $defaults->showinnav = 0;
            $defaults->sortorder = 0;
            $form->set_data($defaults);
        }

        echo $OUTPUT->header();
        echo $OUTPUT->heading($page ? get_string('editpage', 'local_staticpages', format_string($page->title)) : get_string('addpage', 'local_staticpages'));
        $form->display();
        echo $OUTPUT->footer();
        exit;
}

$pages = local_staticpages_get_all_pages(true);

$table = new html_table();
$table->head = [
    get_string('field:title', 'local_staticpages'),
    get_string('field:slug', 'local_staticpages'),
    get_string('field:visible', 'local_staticpages'),
    get_string('field:showinnav', 'local_staticpages'),
    get_string('field:sortorder', 'local_staticpages'),
    get_string('modifiedinfo', 'local_staticpages'),
    get_string('actions', 'local_staticpages'),
];
$table->data = [];

foreach ($pages as $page) {
    $row = [];
    $row[] = format_string($page->title);
    $row[] = s($page->slug);
    $row[] = $page->visible ? get_string('yes') : get_string('no');
    $row[] = $page->showinnav ? get_string('yes') : get_string('no');
    $row[] = (int) $page->sortorder;

    $modified = userdate($page->timemodified) . '<br>';
    if ($page->usermodified) {
        $user = core_user::get_user($page->usermodified, 'id, firstname, lastname, alternatename, firstnamephonetic, lastnamephonetic, middlename');
        if ($user) {
            $modified .= fullname($user);
        }
    }
    $row[] = $modified;

    $links = [];
    $links[] = html_writer::link(new moodle_url('/local/staticpages/manage.php', ['action' => 'edit', 'id' => $page->id]), get_string('edit'));
    $links[] = html_writer::link(new moodle_url('/local/staticpages/manage.php', ['action' => 'togglevisibility', 'id' => $page->id, 'sesskey' => sesskey()]), $page->visible ? get_string('hide') : get_string('show'));
    $links[] = html_writer::link(new moodle_url('/local/staticpages/manage.php', ['action' => 'togglenav', 'id' => $page->id, 'sesskey' => sesskey()]), $page->showinnav ? get_string('removenav', 'local_staticpages') : get_string('addnav', 'local_staticpages'));
    $links[] = html_writer::link(new moodle_url('/local/staticpages/manage.php', ['action' => 'delete', 'id' => $page->id]), get_string('delete'));
    $row[] = implode(' | ', $links);

    $table->data[] = $row;
}

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('managepages', 'local_staticpages'));

echo $OUTPUT->single_button(new moodle_url('/local/staticpages/manage.php', ['action' => 'edit']), get_string('addpage', 'local_staticpages'), 'get');

echo html_writer::table($table);

$exporturl = new moodle_url('/local/staticpages/manage.php', ['action' => 'export', 'sesskey' => sesskey()]);
$importurl = new moodle_url('/local/staticpages/manage.php', ['action' => 'import']);

echo html_writer::start_div('local-staticpages-manage-actions mt-4 d-flex gap-2 flex-wrap');
echo html_writer::link($exporturl, get_string('exportpages', 'local_staticpages'), ['class' => 'btn btn-secondary']);
echo html_writer::link($importurl, get_string('importpages', 'local_staticpages'), ['class' => 'btn btn-secondary']);
echo html_writer::end_div();

echo $OUTPUT->footer();
