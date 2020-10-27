<?php

namespace App\Backport\Controllers\Settings;

use App\Http\Controllers\Controller;
use Wiledia\Backport\Controllers\Dashboard;
use Wiledia\Backport\Layout\Content;
use Wiledia\Backport\Settings\Setting as Setting;
use Theme;


class ThemeController extends Controller
{
    public function index(Content $content)
    {
        $this->data['themes'] = Theme::all();

        return $content
            ->header('Theme')
            ->body(view('backend.theme', $this->data));
    }

    public function update($slug)
    {
        // check if theme saved
        $theme_saved = false;
        // get all themes
        $themes = Theme::all();
        // get theme setting
        $setting = Setting::where('key', 'default_theme')->first();

        // check if theme exist and change setting
        foreach ($themes as $theme_check) {
            if ($theme_check['slug'] === $slug) {
                if ($theme_check['public']) {
                    $setting->value = $slug;
                    $setting->save();
                    $theme_saved = true;
                } else {
                    admin_toastr(ucfirst($slug) . ' Theme not public!', 'error');
                    return back();
                }
            }
        }

        if ($theme_saved) {
            // show a success message
            admin_toastr(ucfirst($slug) . ' Theme saved as default!');
        } else {
            // show a error message
            admin_toastr(ucfirst($slug) . ' Theme not found!', 'error');
        }

        return back();
    }
}
