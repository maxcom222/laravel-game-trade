<?php

namespace App\Backport\Controllers\Settings;

use App\Http\Controllers\Controller;
use Wiledia\Backport\Settings;
use Wiledia\Backport\Layout\Content;
use Illuminate\Http\Request;

class LocalizationController extends Controller
{

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Localization')
            ->body($this->form()->render());
    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        $settingModel = 'Wiledia\Backport\Settings\Setting';

        $settings = new Settings(new $settingModel());

        $settings_model = \Wiledia\Backport\Settings\Setting::where('category','localization')->orderBy('reorder')->get();

        foreach ($settings_model as $setting) {


            if ($setting->key == 'currency') {
                $settings->select($setting->key)->value($setting->value)->options(function () {
                    $options = array();
                    $currencies = Currency('EUR')->getCurrencies();
                    foreach ($currencies as $key => $currency) {
                        $options[$key] = $currency['name'] . " (" . $currency['symbol'] . ")";
                    }
                    return $options;
                });
            } elseif ($setting->key == 'default_locale') {
                $settings->select($setting->key)->value($setting->value)->options(function () {
                    $options = array();
                    $languages = \App\Models\Language::all();
                    foreach ($languages as $key => $language) {
                        $options[$language['abbr']] = $language['name'] . " (" . $language['native'] . ")";
                    }
                    return $options;
                });
            } elseif ($setting->key == 'location_api') {
                $settings->select($setting->key)->value($setting->value)->help($setting->field['hint'])->options(function () {
                    $options = array();
                    if (file_exists(public_path('themes') . '/default/views/frontend/user/location/zippopotam.blade.php')) {
                        $options['zippopotam'] ="Zippopotam (Selected countries)";
                    }

                    if (file_exists(public_path('themes') . '/default/views/frontend/user/location/googlemaps.blade.php')) {
                        $options['googlemaps'] = "Google Maps (Worldwide)";
                    }

                    if (file_exists(public_path('themes') . '/default/views/frontend/user/location/openstreetmap.blade.php')) {
                        $options['openstreetmap'] = "OpenStreetMap (Worldwide)";
                    }

                    return $options;
                });
            } else {
                if (isset($setting->field['hint'])) {
                    $settings->__call($setting->field['type'], [$setting->key, $setting->name])->help($setting->field['hint'])->value($setting->value);
                } else {
                    $settings->__call($setting->field['type'], [$setting->key, $setting->name])->value($setting->value);
                }
            }

        }


        return $settings;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        return $this->form()->update("general", $request->all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return mixed
     */
    public function store()
    {
        return $this->form()->store();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->form()->destroy($id)) {
            $data = [
                'status'  => true,
                'message' => trans('admin.delete_succeeded'),
            ];
        } else {
            $data = [
                'status'  => false,
                'message' => trans('admin.delete_failed'),
            ];
        }

        return response()->json($data);
    }

}
