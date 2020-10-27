<?php

namespace App\Backport\Controllers\Translations;

use App\Http\Controllers\Controller;
use App\Backport\Controllers\Translations\LangFiles;
use App\Models\Language;
use Wiledia\Backport\Controllers\Dashboard;
use Wiledia\Backport\Layout\Content;
use Wiledia\Backport\Settings\Setting as Setting;
use Illuminate\Http\Request;


class TextController extends Controller
{
    public function index(Content $content, LangFiles $langfile, Language $languages, $lang = '', $file = 'auth')
    {
        if ($lang) {
            $langfile->setLanguage($lang);
        } else {
            $langfile->setLanguage(config('app.locale'));
        }
        $langfile->setFile($file);
        $this->data['currentFile'] = $file;
        $this->data['currentLang'] = $lang ?: config('app.locale');
        $this->data['currentLangObj'] = Language::where('abbr', '=', $this->data['currentLang'])->first();
        $this->data['browsingLangObj'] = Language::where('abbr', '=', config('app.locale'))->first();
        $this->data['languages'] = $languages->orderBy('name')->where('active', 1)->get();
        $this->data['langFiles'] = $langfile->getlangFiles();
        $this->data['fileArray'] = $langfile->getFileContent();
        $this->data['langfile'] = $langfile;

        return $content
            ->header('Site Texts')
            ->body(view('backend.translation', $this->data));
    }

    public function update(LangFiles $langfile, Request $request, $lang = '', $file = 'site')
    {
        // SECURITY
        // check if that file isn't forbidden in the config file
        if (in_array($file, $langfile->language_ignore)) {
            abort('403', trans('backport.cant_edit_online'));
        }
        $message = trans('error.error_general');
        $status = false;
        if ($lang) {
            $langfile->setLanguage($lang);
        }
        $langfile->setFile($file);
        $fields = $langfile->testFields($request->all());
        if (empty($fields)) {
            if ($langfile->setFileContent($request->all())) {
                admin_toastr(trans('admin.save_succeeded'));
                $status = true;
            }
        } else {
            $message = trans('admin.language.fields_required');
            admin_toastr(trans('backport.please_fill_all_fields'), 'error');
        }

        admin_toastr(trans('admin.save_succeeded'));

        return back();
    }

    public function showTexts(LangFiles $langfile, Language $languages, $lang = '', $file = 'site')
    {
        // SECURITY
        // check if that file isn't forbidden in the config file
        if (in_array($file, $langfile->language_ignore)) {
            abort('403', trans('backport.cant_edit_online'));
        }
        if ($lang) {
            $langfile->setLanguage($lang);
        }
        $langfile->setFile($file);
        $this->data['crud'] = $this->crud;
        $this->data['currentFile'] = $file;
        $this->data['currentLang'] = $lang ?: config('app.locale');
        $this->data['currentLangObj'] = Language::where('abbr', '=', $this->data['currentLang'])->first();
        $this->data['browsingLangObj'] = Language::where('abbr', '=', config('app.locale'))->first();
        $this->data['languages'] = $languages->orderBy('name')->where('active', 1)->get();
        $this->data['langFiles'] = $langfile->getlangFiles();
        $this->data['fileArray'] = $langfile->getFileContent();
        $this->data['langfile'] = $langfile;
        return view('langfilemanager::translations', $this->data);
    }
    public function updateTexts(LangFiles $langfile, Request $request, $lang = '', $file = 'site')
    {
        // SECURITY
        // check if that file isn't forbidden in the config file
        if (in_array($file, $langfile->language_ignore)) {
            abort('403', trans('backport.cant_edit_online'));
        }
        $message = trans('error.error_general');
        $status = false;
        if ($lang) {
            $langfile->setLanguage($lang);
        }
        $langfile->setFile($file);
        $fields = $langfile->testFields($request->all());
        if (empty($fields)) {
            if ($langfile->setFileContent($request->all())) {
                admin_toastr(trans('admin.save_succeeded'));
                $status = true;
            }
        } else {
            $message = trans('admin.language.fields_required');
            admin_toastr(trans('backport.please_fill_all_fields'), 'error');
        }

        admin_toastr(trans('admin.save_succeeded'));

        return redirect()->back();
    }
}
