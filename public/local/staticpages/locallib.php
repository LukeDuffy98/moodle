<?php
// This file is part of Moodle - http://moodle.org/

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/moodlelib.php');

use core_text;
use moodle_url;
use moodle_exception;
use stdClass;

/**
 * Ensure the static pages table is available.
 */
function local_staticpages_table_exists(): bool {
    global $DB;

    static $available = null;

    if ($available === null) {
        $available = $DB->get_manager()->table_exists('local_staticpages');
    }

    return $available;
}

/**
 * Retrieve the database record for a static page.
 */
function local_staticpages_get_page_definition(string $slug): ?stdClass {
    global $DB;

    if (!local_staticpages_table_exists()) {
        return null;
    }

    return $DB->get_record('local_staticpages', ['slug' => $slug], '*', IGNORE_MISSING) ?: null;
}

/**
 * Determine whether the current user can manage static pages.
 */
function local_staticpages_can_manage(): bool {
    return has_capability('local/staticpages:manage', \context_system::instance());
}

/**
 * Generate a slug from a source string.
 */
function local_staticpages_generate_slug(string $source): string {
    $slug = core_text::strtolower(trim($source));
    $slug = preg_replace('/[^a-z0-9]+/u', '-', $slug);
    $slug = trim($slug ?? '', '-');

    if ($slug === '') {
        try {
            $slug = bin2hex(random_bytes(4));
        } catch (\Throwable $e) {
            $slug = uniqid('', true);
        }
    }

    return $slug;
}

/**
 * Resolve a unique slug by appending an increment when needed.
 */
function local_staticpages_resolve_unique_slug(string $slug, ?int $ignoreid = null): string {
    global $DB;

    $base = $slug;
    $counter = 1;

    while (true) {
        $params = ['slug' => $slug];
        $sql = 'slug = :slug';

        if ($ignoreid) {
            $sql .= ' AND id <> :id';
            $params['id'] = $ignoreid;
        }

        if (!$DB->record_exists_select('local_staticpages', $sql, $params)) {
            return $slug;
        }

        $slug = $base . '-' . $counter;
        $counter++;
    }
}

/**
 * Return all static pages ordered for navigation lists.
 */
function local_staticpages_get_all_pages(bool $includehidden = false): array {
    global $DB;

    if (!local_staticpages_table_exists()) {
        return [];
    }

    $conditions = null;
    if (!$includehidden) {
        $conditions = ['visible' => 1];
    }

    return $DB->get_records('local_staticpages', $conditions, 'sortorder ASC, id ASC');
}

/**
 * Return all pages marked for navigation.
 */
function local_staticpages_get_navigation_pages(): array {
    return array_filter(local_staticpages_get_all_pages(false), static function(stdClass $page): bool {
        return !empty($page->showinnav);
    });
}

/**
 * Fetch a page by id.
 */
function local_staticpages_get_page_by_id(int $id): ?stdClass {
    global $DB;

    if (!local_staticpages_table_exists()) {
        return null;
    }

    return $DB->get_record('local_staticpages', ['id' => $id]) ?: null;
}

/**
 * Insert or update a static page record.
 */
function local_staticpages_save_page(stdClass $data): int {
    global $DB, $USER;

    if (!local_staticpages_table_exists()) {
        throw new moodle_exception('tablestructurenotready', 'local_staticpages');
    }

    $now = time();
    $userid = $USER->id ?? 0;

    $record = clone $data;
    $record->slug = trim($record->slug ?? '');
    if ($record->slug === '') {
        throw new moodle_exception('error:slugmissing', 'local_staticpages');
    }
    $record->summary = $record->summary ?? '';
    $record->content = $record->content ?? '';
    $record->title = $record->title ?? $record->slug;
    $record->heading = (isset($record->heading) && $record->heading !== '') ? $record->heading : $record->title;
    $record->sortorder = (int) ($record->sortorder ?? 0);
    $record->visible = (int) !empty($record->visible);
    $record->showinnav = (int) !empty($record->showinnav);
    $record->timemodified = $now;
    $record->usermodified = $userid;

    if (!empty($record->id)) {
        $existing = local_staticpages_get_page_by_id((int) $record->id);
        $record->timecreated = $existing->timecreated ?? $now;
        $DB->update_record('local_staticpages', $record);
        return (int) $record->id;
    }

    $record->timecreated = $now;
    $record->id = $DB->insert_record('local_staticpages', $record);

    return (int) $record->id;
}

/**
 * Delete a static page by id.
 */
function local_staticpages_delete_page(int $id): void {
    global $DB;

    if (!local_staticpages_table_exists()) {
        return;
    }

    $DB->delete_records('local_staticpages', ['id' => $id]);
}

/**
 * Seed the default pages if they do not exist already.
 */
function local_staticpages_seed_default_pages(): void {
    global $DB;

    if (!local_staticpages_table_exists()) {
        return;
    }

    $now = time();
    $pages = [
        [
            'slug' => 'about',
            'title' => get_string('about_title', 'local_staticpages'),
            'heading' => get_string('about_heading', 'local_staticpages'),
            'summary' => get_string('about_summary', 'local_staticpages'),
            'content' => get_string('about_content', 'local_staticpages'),
            'sortorder' => 10,
            'visible' => 1,
            'showinnav' => 1,
        ],
        [
            'slug' => 'contact',
            'title' => get_string('contact_title', 'local_staticpages'),
            'heading' => get_string('contact_heading', 'local_staticpages'),
            'summary' => get_string('contact_summary', 'local_staticpages'),
            'content' => get_string('contact_content', 'local_staticpages'),
            'sortorder' => 20,
            'visible' => 1,
            'showinnav' => 1,
        ],
        [
            'slug' => 'privacy',
            'title' => get_string('privacy_title', 'local_staticpages'),
            'heading' => get_string('privacy_heading', 'local_staticpages'),
            'summary' => get_string('privacy_summary', 'local_staticpages'),
            'content' => get_string('privacy_content', 'local_staticpages'),
            'sortorder' => 30,
            'visible' => 1,
            'showinnav' => 0,
        ],
        [
            'slug' => 'terms',
            'title' => get_string('terms_title', 'local_staticpages'),
            'heading' => get_string('terms_heading', 'local_staticpages'),
            'summary' => get_string('terms_summary', 'local_staticpages'),
            'content' => get_string('terms_content', 'local_staticpages'),
            'sortorder' => 40,
            'visible' => 1,
            'showinnav' => 0,
        ],
    ];

    foreach ($pages as $page) {
        if ($DB->record_exists('local_staticpages', ['slug' => $page['slug']])) {
            continue;
        }

        $record = (object) array_merge($page, [
            'timecreated' => $now,
            'timemodified' => $now,
            'usermodified' => 0,
        ]);

        $DB->insert_record('local_staticpages', $record);
    }
}

/**
 * Require the current user to have access to the static pages.
 */
function local_staticpages_require_access() {
    $context = \context_system::instance();

    if (isloggedin()) {
        require_capability('local/staticpages:view', $context);
        return $context;
    }

    $guest = guest_user();
    if (!has_capability('local/staticpages:view', $context, $guest, false)) {
        require_login();
        require_capability('local/staticpages:view', $context);
    }

    return $context;
}

/**
 * Render a static page response.
 */
function local_staticpages_render_page(string $slug, ?moodle_url $pageurl = null): void {
    global $PAGE, $OUTPUT, $SITE;

    $context = local_staticpages_require_access();

    $definition = local_staticpages_get_page_definition($slug);
    if (!$definition) {
        throw new moodle_exception('invalidrecord', 'error');
    }

    if (empty($definition->visible) && !local_staticpages_can_manage()) {
        throw new moodle_exception('pagehidden', 'local_staticpages');
    }

    $pageurl = $pageurl ?? new moodle_url('/local/staticpages/view.php', ['slug' => $slug]);

    $PAGE->set_context($context);
    $PAGE->set_url($pageurl);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title(format_string($definition->title));
    $PAGE->set_heading(format_string($SITE->fullname));
    $PAGE->navbar->add(format_string($definition->title));

    echo $OUTPUT->header();

    $pagetitle = $definition->heading !== '' ? $definition->heading : $definition->title;

    $templatecontext = [
        'title' => format_string($pagetitle),
        'content' => format_text($definition->content, FORMAT_HTML, [
            'context' => $context,
            'trusted' => true,
            'noclean' => true,
        ]),
    ];

    echo $OUTPUT->render_from_template('local_staticpages/static_page', $templatecontext);
    echo $OUTPUT->footer();
}
