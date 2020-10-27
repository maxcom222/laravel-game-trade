<?php
namespace App\Http\Controllers\Frontend\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exceptions\GeneralException;
use App\Helpers\Frontend\Auth\Socialite;
use App\Events\Frontend\Auth\UserLoggedIn;
use App\Events\Frontend\Auth\UserLoggedOut;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use SEO;
use Theme;

/**
 * Class LoginController
 * @package App\Http\Controllers\Auth
 */
class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login
     * @return string
     */
    public function redirectPath(Request $request)
    {
        return route('frontend.dash');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        // Title
		    SEO::setTitle(trans('general.title.sign_in', ['page_name' => config('settings.page_name'), 'sub_title' => config('settings.sub_title')]));

        //return view('frontend.auth.login')
        //	->withSocialiteLinks((new Socialite)->getSocialLinks());
        if (!auth()->user()) {
            return view('frontend.auth.login');
        } else {
            return redirect()->route('frontend.dash');
        }
    }


    /**
     * Get the failed login response instance.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        return abort(406);
    }

    /**
     * @param Request $request
     * @param $user
     * @return \Illuminate\Http\RedirectResponse
     * @throws GeneralException
     */
    protected function authenticated(Request $request, $user)
    {
        /**
         * Check to see if the users account is confirmed and active
         */
        event(new UserLoggedIn($user));

        // check if user confirmed email
        if (! $user->isConfirmed()) {
            auth()->logout();
            $request->session()->flash('error', trans('auth.confirmation.resend', ['user_id' => $user->id]));
            return url('login');
        }

        // check if user account is active
        if (! $user->isActive()) {
            auth()->logout();
            $request->session()->flash('error', trans('auth.deactivated'));
            return url('login');
        }

        // show a success message
        \Alert::success('<i class="far fa-smile m-r-5"></i> ' . trans('auth.welcome_back', ['user_name' => $user->name]))->flash();

        // return link to dashboard or previous url (on modal login only)
        if ($request->ajax()) {
            if (url()->previous() == url('login')) {
                return url('dash');
            } else {
                return url()->previous();
            }
        }

        return redirect()->intended($this->redirectPath());
    }

    /**
     * Log the user out of the application.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        /**
         * Boilerplate needed logic
         */

        $theme = session()->get('theme');
        $locale = session()->get('locale');

        /**
         * Laravel specific logic
         */
        $this->guard()->logout();
        $request->session()->flush();
        $request->session()->regenerate();

        session()->put('theme', $theme);
        session()->put('locale', $locale);



        // show a success message
        \Alert::error('<i class="fa fa-sign-out m-r-5"></i> ' . trans('auth.see_you'))->flash();

        return redirect('/');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logoutAs()
    {
        //If for some reason route is getting hit without someone already logged in
        if (! access()->user()) {
            return redirect()->route("frontend.auth.login");
        }

        //If admin id is set, relogin
        if (session()->has("admin_user_id") && session()->has("temp_user_id")) {
            //Save admin id
            $admin_id = session()->get("admin_user_id");

            app()->make(Auth::class)->flushTempSession();

            //Re-login admin
            access()->loginUsingId((int)$admin_id);

            //Redirect to backend user page
            return redirect()->route("admin.access.user.index");
        } else {
            app()->make(Auth::class)->flushTempSession();

            //Otherwise logout and redirect to login
            access()->logout();
            return redirect()->route("frontend.auth.login");
        }
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('backport');
    }

}
