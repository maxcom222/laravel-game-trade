<?php
namespace App\Http\Controllers\Frontend\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use App\Repositories\UserRepository;
use Theme;
use SEO;

/**
 * Class ResetPasswordController
 * @package App\Http\Controllers\Frontend\Auth
 */
class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    /**
     * @var UserRepository
     */
    protected $user;

    /**
     * ChangePasswordController constructor.
     * @param UserRepository $user
     */
    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }

    /**
     * Where to redirect users after resetting password
     *
     * @return string
     */
    public function redirectPath()
    {
        return url('dash');
    }

    /**
     * Get the response for a successful password reset.
     *
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetResponse($response)
    {
        \Alert::success(trans('auth.reset.reset'))->flash();
        // Ajax, so return just the url
        return $this->redirectPath();
    }

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  string|null  $token
     * @return \Illuminate\Http\Response
     */
    public function showResetForm($token = null)
    {
        // Title
        SEO::setTitle(trans('auth.reset.reset_button') . ' - ' . config('settings.page_name'));

        return view('frontend.auth.reset')
            ->withToken($token)
            ->withEmail($this->user->getEmailForPasswordToken($token));
    }
}
