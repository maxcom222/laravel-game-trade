<?php
namespace App\Http\Controllers;

use Theme, Auth;

/**
 * Class ThemeController
 * @package App\Http\Controllers
 */
class ThemeController extends Controller
{
    /**
     * @param $lang
     * @return \Illuminate\Http\RedirectResponse
     */
    public function swap($theme)
    {
        // Check if theme selector is enable or the user have access to the settings
        if (config('settings.theme_selector') || (Auth::check() && Auth::user()->can('edit_settings'))) {
            // get all themes
            $themes = Theme::all();

            // check if theme exist and change session setting
            foreach ($themes as $theme_check) {
                if ($theme_check['slug'] == $theme) {
                    // Check if theme is public or the user have access to the settings
                    if ($theme_check['public'] || (Auth::check() && Auth::user()->can('edit_settings'))) {
                        session()->put('theme', $theme);
                    }
                }
            }
        }

        // redirect back to last page
        return url()->current() == url()->previous() ? redirect()->route('index') : redirect()->back();
    }
}
