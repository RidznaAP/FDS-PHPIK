<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Show the application's login form.
     * Overrides default trait behavior to include captcha.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        $num1 = rand(1, 9);
        $num2 = rand(1, 9);
        session()->put('math_captcha_result', $num1 + $num2);

        return view('auth.login', compact('num1', 'num2'));
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(\Illuminate\Http\Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
            'captcha' => ['required', 'numeric', function ($attribute, $value, $fail) use ($request) {
                if ($value != $request->session()->get('math_captcha_result')) {
                    $fail('Hasil penjumlahan tidak sama atau sesi telah kedaluwarsa.');
                }
            }],
        ]);
    }
}
