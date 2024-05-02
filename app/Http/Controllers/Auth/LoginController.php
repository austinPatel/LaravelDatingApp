<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember_me = $request->has('remember');

        $credentials = $request->only('email', 'password');
        $user = User::with('roles')->where('email', $request->email)->first();
        $roles_array = $user ? $user->roles->pluck('name')->toArray() : [];

        if (!$user) {
            return redirect()->back()->withErrors(['email' => ['Opps! You have entered invalid credentials']]);
        }

        if ($user && !empty($user->roles) && !in_array(User::ROLE_SUPER_ADMIN, $roles_array)) {
            return redirect()->back()->withErrors(['email' => ['Invalid user']]);
        }

        if ($user && !$user->hasVerifiedEmail()) {
            return redirect()->back()->withErrors(['email' => ['Please verify your email address by clicking the link in the email sent.']]);
        }

        if (!Hash::check($request->password, $user->password)) {
            return redirect()->back()->withErrors(['email' => ['Opps! You have entered invalid credentials']]);
        }

        if (Auth::attempt($credentials, $remember_me)) {
            return redirect()->route('admin.dashboard');
        }
    }
}
