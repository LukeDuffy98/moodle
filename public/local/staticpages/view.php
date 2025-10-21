<?php
// This file is part of Moodle - http://moodle.org/

require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/locallib.php');

$slug = required_param('slug', PARAM_ALPHANUMEXT);

local_staticpages_render_page($slug);
