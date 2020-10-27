<?php
namespace App\Http\Controllers\Frontend\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Notifications\Auth\UserNeedsConfirmation;

/**
 * Class ConfirmAccountController
 * @package App\Http\Controllers\Frontend\Auth
 */
class ConfirmAccountController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $user;

    /**
     * ConfirmAccountController constructor.
     * @param UserRepository $user
     */
    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }

    /**
     *
     * Confirm user account
     *
     * @param $token
     * @return mixed
     */
    public function confirm($token)
    {
        return $this->user->confirmAccount($token);
    }

    /**
     *
     * Resend confirmation mail
     *
     * @param $user
     * @return mixed
     */
    public function sendConfirmationEmail(User $user)
    {
        $user->notify(new UserNeedsConfirmation($user->confirmation_code));
        return redirect()->route('frontend.auth.login')->withSuccess(trans('auth.confirmation.resent'));
    }
}
