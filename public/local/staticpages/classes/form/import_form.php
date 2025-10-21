<?php
// This file is part of Moodle - http://moodle.org/

namespace local_staticpages\form;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/formslib.php');

use moodleform;

/**
 * Form for importing static pages via JSON.
 */
class import_form extends moodleform {
    public function definition(): void {
        $mform = $this->_form;

        $mform->addElement('textarea', 'jsondata', get_string('import:json', 'local_staticpages'), ['rows' => 15, 'cols' => 80]);
        $mform->setType('jsondata', PARAM_RAW);
        $mform->addRule('jsondata', null, 'required');

        $mform->addElement('select', 'importmode', get_string('import:mode', 'local_staticpages'), [
            'append' => get_string('import:mode_append', 'local_staticpages'),
            'replace' => get_string('import:mode_replace', 'local_staticpages'),
        ]);
        $mform->setDefault('importmode', 'append');

        $this->add_action_buttons(true, get_string('importpages', 'local_staticpages'));
    }
}
