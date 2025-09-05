<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use App\Services\LdapAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    protected LdapAuthService $ldapAuthService;

    public function __construct(LdapAuthService $ldapAuthService)
    {
        $this->ldapAuthService = $ldapAuthService;
    }

    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // First try LDAP authentication
        $user = $this->ldapAuthService->authenticate(
            $request->input('email'), // Can be username or email
            $request->input('password')
        );

        if ($user) {
            // LDAP authentication successful
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        // Fallback to standard Laravel authentication for non-LDAP users
        $request->authenticate();
        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
