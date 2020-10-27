<?php
namespace App\Http\Controllers\Frontend\Auth;

use Illuminate\Http\Request;
use App\Exceptions\GeneralException;
use Laravel\Socialite\Facades\Socialite;
use App\Events\Frontend\Auth\UserLoggedIn;
use App\Helpers\Socialite as SocialiteHelper;
use App\Repositories\UserRepository;

/**
 * Class SocialLoginController
 * @package App\Http\Controllers\Auth
 */
class SocialLoginController
{
    /**
     * @var UserRepository
     */
    protected $user;

    /**
     * @var SocialiteHelper
     */
    protected $helper;

    /**
     * SocialLoginController constructor.
     * @param UserRepository $user
     * @param SocialiteHelper $helper
     */
    public function __construct(UserRepository $user, SocialiteHelper $helper)
    {
        $this->user = $user;
        $this->helper = $helper;
    }

    /**
     * @param Request $request
     * @param $provider
     * @return \Illuminate\Http\RedirectResponse|mixed
     * @throws GeneralException
     */
    public function login(Request $request, $provider)
    {
        //If the provider is not an acceptable third party than kick back
        if (! in_array($provider, $this->helper->getAcceptedProviders())) {
            return redirect()->route('frontend.index')->withFlashDanger(trans('auth.socialite.unacceptable', ['provider' => $provider]));
        }


        // Set provider config from database
        config(['services.'. $provider .'.client_id' => config('settings.'. $provider .'_client_id')]);
        config(['services.'. $provider .'.client_secret' => config('settings.'. $provider .'_client_secret')]);
        config(['services.'. $provider .'.redirect' => url('login/' . $provider)]);

        /**
         * The first time this is hit, request is empty
         * It's redirected to the provider and then back here, where request is populated
         * So it then continues creating the user
         */
        if (! $request->all()) {
            return $this->getAuthorizationFirst($provider);
        }

        /**
         *
         *
         *
         */
        if (!($provider == 'steam') && !($provider == 'twitter') && ! $request->has('code') || $request->has('denied')) {
            return redirect()->intended(route('frontend.auth.login'));
        }

        /**
         * Create the user if this is a new social account or find the one that is already there
         */
        $user = $this->user->findOrCreateSocial($this->getSocialUser($provider), $provider);

        /**
         * User has been successfully created or already exists
         * Log the user in
         */
        auth()->login($user, true);

        /**
         * User authenticated, check to see if they are active.
         */
         // check if user account is active
        if (! auth()->user()->isActive()) {
            auth()->logout();
            $request->session()->flash('error', trans('auth.deactivated'));
            return redirect()->intended(route('frontend.auth.login'));
        }

        /**
         * Throw an event in case you want to do anything when the user logs in
         */
        event(new UserLoggedIn($user));

        /**
         * Set session variable so we know which provider user is logged in as, if ever needed
         */
        session([config('access.socialite_session_name') => $provider]);

        // show a success message
        \Alert::success('<i class="fa fa-smile-o m-r-5"></i> ' . trans('auth.welcome_back', ['user_name' => $user->name]))->flash();

        /**
         * Return to the intended url or default to the class property
         */
        return redirect()->intended(route('frontend.dash'));
    }

    /**
     * @param  $provider
     * @return mixed
     */
    private function getAuthorizationFirst($provider)
    {
        $socialite = Socialite::driver($provider);
        $scopes = null !== config("services.{$provider}.scopes") && count(config("services.{$provider}.scopes")) ? config("services.{$provider}.scopes") : false;
        $with = null !== config("services.{$provider}.with") && count(config("services.{$provider}.with")) ? config("services.{$provider}.with") : false;
        $fields = null !== config("services.{$provider}.fields") && count(config("services.{$provider}.fields")) ? config("services.{$provider}.fields") : false;

        if ($scopes) {
            $socialite->scopes($scopes);
        }

        if ($with) {
            $socialite->with($with);
        }

        if ($fields) {
            $socialite->fields($fields);
        }

        return $socialite->redirect();
    }

    /**
     * @param $provider
     * @return mixed
     */
    private function getSocialUser($provider)
    {
        return Socialite::driver($provider)->user();
    }
}
