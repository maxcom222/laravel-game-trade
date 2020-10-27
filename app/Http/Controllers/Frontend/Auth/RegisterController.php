<?php
namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use App\Events\Frontend\Auth\UserRegistered;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Http\Requests\Frontend\Auth\RegisterRequest;
use App\Repositories\UserRepository;

/**
 * Class RegisterController
 * @package App\Http\Controllers\Frontend\Auth
 */
class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * @var UserRepository
     */
    protected $user;

    /**
     * RegisterController constructor.
     * @param UserRepository $user
     */
    public function __construct(UserRepository $user)
    {
        // Where to redirect users after registering
        $this->redirectTo = route('index');

        $this->user = $user;
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        return view('frontend.auth.register');
    }

    /**
     * @param RegisterRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function register(RegisterRequest $request)
    {
        if (config('settings.user_confirmation')) {
            $user = $this->user->create($request->all());
            event(new UserRegistered($user));
            $request->session()->flash('success', trans('auth.confirmation.created_confirm'));
            // return url on ajax request
            if ($request->ajax()) {
                return url('login');
            }
            return redirect()->route('frontend.auth.login');
        } else {
            auth()->login($this->user->create($request->all()));
            //event(new UserRegistered(access()->user()));
            // return url on ajax request
            if ($request->ajax()) {
                if (url()->previous() == url('login')) {
                    return url('dash');
                } else {
                    return url()->previous();
                }
            }
            return redirect($this->redirectPath());
        }
    }
}
