<?php
// This file is part of Moodle - http://moodle.org/

namespace local_staticpages\form;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/local/staticpages/locallib.php');

use moodleform;
use stdClass;

/**
 * Form for creating and editing static pages.
 */
class page_form extends moodleform {
    public function definition(): void {
        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('text', 'title', get_string('field:title', 'local_staticpages'), ['size' => 64]);
        $mform->setType('title', PARAM_TEXT);
        $mform->addRule('title', null, 'required');

        $mform->addElement('text', 'slug', get_string('field:slug', 'local_staticpages'), ['size' => 64]);
        $mform->setType('slug', PARAM_RAW);
        $mform->addHelpButton('slug', 'field:slug', 'local_staticpages');

        $mform->addElement('text', 'heading', get_string('field:heading', 'local_staticpages'), ['size' => 64]);
        $mform->setType('heading', PARAM_TEXT);

        $mform->addElement('textarea', 'summary', get_string('field:summary', 'local_staticpages'), ['rows' => 4, 'cols' => 60]);
        $mform->setType('summary', PARAM_RAW);

        $editoroptions = ['maxfiles' => 0, 'trusttext' => true];
        $mform->addElement('editor', 'content', get_string('field:content', 'local_staticpages'), null, $editoroptions);
        $mform->setType('content', PARAM_RAW);
        $mform->addRule('content', null, 'required');

        $mform->addElement('advcheckbox', 'visible', get_string('field:visible', 'local_staticpages'));
        $mform->setType('visible', PARAM_BOOL);
        $mform->setDefault('visible', 1);

        $mform->addElement('advcheckbox', 'showinnav', get_string('field:showinnav', 'local_staticpages'));
        $mform->setType('showinnav', PARAM_BOOL);
        $mform->setDefault('showinnav', 0);

        $mform->addElement('text', 'sortorder', get_string('field:sortorder', 'local_staticpages'), ['size' => 6]);
        $mform->setType('sortorder', PARAM_INT);
        $mform->setDefault('sortorder', 0);

        $this->add_action_buttons(true, get_string('savepage', 'local_staticpages'));
    }

    public function set_data($default_values): void {
        if ($default_values instanceof stdClass) {
            if (!empty($default_values->content)) {
                $default_values->content = [
                    'text' => $default_values->content,
                    'format' => FORMAT_HTML,
                ];
            }
        }

        parent::set_data($default_values);
    }

    public function validation($data, $files): array {
        global $DB;

        $errors = parent::validation($data, $files);

        $title = trim($data['title'] ?? '');
        $slugsource = trim($data['slug'] ?? '');
        if ($slugsource === '') {
            $slugsource = $title;
        }

    $slug = \local_staticpages_generate_slug($slugsource);
        $params = ['slug' => $slug];
        $sql = 'slug = :slug';

        $id = !empty($data['id']) ? (int) $data['id'] : 0;
        if ($id) {
            $sql .= ' AND id <> :id';
            $params['id'] = $id;
        }

        if ($DB->record_exists_select('local_staticpages', $sql, $params)) {
            $errors['slug'] = get_string('error:slugexists', 'local_staticpages');
        }

        $content = $data['content']['text'] ?? '';
        if (trim($content) === '') {
            $errors['content'] = get_string('error:contentrequired', 'local_staticpages');
        }

        return $errors;
    }
}
