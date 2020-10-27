<?php

/**
 * backport - admin builder based on Laravel.
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Wiledia\Backport\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Wiledia\Backport\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */

use App\Backport\Extensions\PageLink;
use Wiledia\Backport\Form;

Form::extend('pagelink', PageLink::class);

Form::forget(['map']);
