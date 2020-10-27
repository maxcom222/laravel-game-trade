<?php
namespace App\Observers;

use App\Models\Language;
use Cache;

class LanguageObserver
{
    /**
     * Listen to the Language created event.
     *
     * @param  Language  $language
     * @return void
     */
    public function created(Language $language)
    {
        Cache::forget('languages');

        return true;
    }

    /**
     * Listen to the Language updating event.
     *
     * @param  Language  $language
     * @return void
     */
    public function updated(Language $language)
    {
        Cache::forget('languages');

        return true;
    }

    /**
     * Listen to the Language deleted event.
     *
     * @param  Language  $language
     * @return void
     */
    public function deleted(Language $language)
    {
        Cache::forget('languages');

        return true;
    }
}
