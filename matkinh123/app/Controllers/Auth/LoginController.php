<?php

namespace Matkinh123\Controllers\Auth;

use Matkinh123\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admincp';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('matkinh123::auth.login');
    }

    public function username()
    {
        return 'username';
    }

    /*public function authenticated(Request $request)
    {
        $res = $request->only('email', 'username', 'password');

        if(filter_var($credentials, FILTER_VALIDATE_EMAIL)) {
            //user sent their email
            Auth::attempt(['email' => $credentials, 'password' => $password]);
        } else {
            //they sent their username instead
            Auth::attempt(['username' => $credentials, 'password' => $password]);
        }

        // was any of those correct ?
        if ( Auth::check() ) {
            //send them where they are going
            return redirect()->intended('dashboard');
        }

        // Nope, something wrong during authentication
        return redirect()->back()->withErrors([
            'credentials' => 'Please, check your credentials'
        ]);
    }*/
}
