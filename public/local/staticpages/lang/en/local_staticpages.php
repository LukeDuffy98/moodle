<?php
// This file is part of Moodle - http://moodle.org/

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Static Pages';
$string['privacy:metadata'] = 'The Static Pages plugin does not store personal data.';
$string['staticpages:view'] = 'View static pages';
$string['staticpages:manage'] = 'Manage static pages';
$string['index_title'] = 'Information Pages';
$string['index_description'] = 'Browse the static information pages available on this site.';
$string['nolistitems'] = 'No static pages are available yet.';

$string['about_title'] = 'About Us';
$string['about_heading'] = 'About Us';
$string['about_summary'] = 'Learn how Quill Learning – MS Exam Prep empowers professionals pursuing Microsoft certifications.';
$string['about_content'] = '<p>Welcome to <strong>Quill Learning &ndash; MS Exam Prep</strong>. We specialize in helping learners achieve Microsoft certification success through curated study guides, practice tests, and expert-led resources.</p>'
    . '<p>As part of Quill Learning, our mission is to empower professionals with practical, exam-focused learning that drives career growth.</p>';

$string['contact_title'] = 'Contact';
$string['contact_heading'] = 'Contact';
$string['contact_summary'] = 'Get in touch with the MS Exam Prep support team via email, phone, or our Hobart office.';
$string['contact_content'] = '<p>Need assistance or have questions about our MS Exam Prep materials?</p>'
    . '<ul>'
    . '<li><strong>Email:</strong> <a href="mailto:ms-exam-support@quilllearning.com">ms-exam-support@quilllearning.com</a></li>'
    . '<li><strong>Phone:</strong> +61 3 1234 5678</li>'
    . '<li><strong>Address:</strong> Hobart, Tasmania, Australia</li>'
    . '<li><strong>Website:</strong> <a href="https://www.quilllearning.com/ms-exam-prep" target="_blank" rel="noopener">www.quilllearning.com/ms-exam-prep</a></li>'
    . '</ul>';

$string['privacy_title'] = 'Privacy Policy';
$string['privacy_heading'] = 'Privacy Policy';
$string['privacy_summary'] = 'Understand the privacy principles that govern data across Quill Learning\'s MS Exam Prep platform.';
$string['privacy_content'] = '<p>Your privacy matters to us.</p>'
    . '<p><strong>Scope:</strong> Applies to all users of Quill Learning&#39;s MS Exam Prep subsite.</p>'
    . '<p><strong>Key principles:</strong></p>'
    . '<ul>'
    . '<li>We collect only essential data for account creation, progress tracking, and certification support.</li>'
    . '<li>Your information will never be sold or shared without consent.</li>'
    . '<li>You have full rights to access, update, or delete your data.</li>'
    . '<li>We comply with Australian Privacy Principles and global data protection standards.</li>'
    . '</ul>'
    . '<p>For concerns, contact our <strong>Privacy Officer</strong> at <a href="mailto:privacy@quilllearning.com">privacy@quilllearning.com</a>.</p>';

$string['terms_title'] = 'Terms of Service';
$string['terms_heading'] = 'Terms of Service';
$string['terms_summary'] = 'Review the usage guidelines, payment expectations, and refund policy for MS Exam Prep resources.';
$string['terms_content'] = '<p>By using our MS Exam Prep resources, you agree to:</p>'
    . '<ul>'
    . '<li><strong>Access:</strong> Materials are for personal, non-commercial use only.</li>'
    . '<li><strong>Payments:</strong> Subscription fees must be paid before accessing premium content.</li>'
    . '<li><strong>Cancellations:</strong> Refunds are available within 7 days of purchase if less than 20% of content has been accessed.</li>'
    . '<li><strong>Content updates:</strong> We may update or retire exam prep materials to align with Microsoft\'s latest certification changes.</li>'
    . '<li><strong>Prohibited use:</strong> Sharing login credentials or redistributing content is strictly forbidden.</li>'
    . '</ul>'
    . '<p>Continued use of this subsite indicates acceptance of these terms.</p>';

$string['managepages'] = 'Manage static pages';
$string['addpage'] = 'Add page';
$string['editpage'] = 'Edit "{$a}"';
$string['deletepage'] = 'Delete page';
$string['confirmdelete'] = 'Are you sure you want to delete "{$a}"? This cannot be undone.';
$string['pagedeleted'] = 'Static page deleted.';
$string['pagesaved'] = 'Static page saved.';
$string['exportpages'] = 'Export pages';
$string['importpages'] = 'Import pages';
$string['importsuccess'] = 'Static pages imported successfully.';
$string['error:pagenotfound'] = 'The requested page could not be found.';
$string['error:invalidjson'] = 'The provided JSON could not be processed. Please check the syntax and try again.';
$string['error:slugexists'] = 'This slug is already in use. Please choose another.';
$string['error:contentrequired'] = 'Content cannot be empty.';
$string['error:slugmissing'] = 'A slug is required to save the page.';
$string['tablestructurenotready'] = 'The static pages table is not ready yet. Please complete the plugin installation.';
$string['pagehidden'] = 'This page is not currently visible.';
$string['untitledpage'] = 'Untitled page';

$string['field:title'] = 'Title';
$string['field:slug'] = 'Slug';
$string['field:slug_help'] = 'Leave blank to generate a slug automatically from the title. Only lowercase letters, numbers, and hyphens are kept.';
$string['field:heading'] = 'Heading';
$string['field:summary'] = 'Summary';
$string['field:content'] = 'Content';
$string['field:visible'] = 'Visible';
$string['field:showinnav'] = 'Show in navigation';
$string['field:sortorder'] = 'Sort order';
$string['modifiedinfo'] = 'Last updated';
$string['actions'] = 'Actions';
$string['addnav'] = 'Show in navigation';
$string['removenav'] = 'Remove from navigation';
$string['savepage'] = 'Save page';
$string['draft'] = 'Draft';

$string['import:json'] = 'JSON payload';
$string['import:mode'] = 'Import mode';
$string['import:mode_append'] = 'Append to existing pages';
$string['import:mode_replace'] = 'Replace existing pages';
