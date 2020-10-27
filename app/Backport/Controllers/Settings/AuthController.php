<?php

namespace App\Backport\Controllers\Settings;

use App\Http\Controllers\Controller;
use Wiledia\Backport\Settings;
use Wiledia\Backport\Layout\Content;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Authentication')
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

        $settings_model = \Wiledia\Backport\Settings\Setting::where('category','auth')->orderBy('reorder')->get();

        foreach ($settings_model as $setting) {
            if (isset($setting->field['hint'])) {
                $settings->__call($setting->field['type'], [$setting->key, $setting->name])->help($setting->field['hint'])->value($setting->value);
            } else {
                $settings->__call($setting->field['type'], [$setting->key, $setting->name])->value($setting->value);
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
