<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class CheckUsername
{
    public function handle(Request $request, Closure $next)
    {
        $user = null;

        if (env('SAML_ENABLED', false)) {
            // Check for SAML username in request headers
            $username = $request->server('HTTP_SHIBBOLETH_USERNAME', env('TEST_USERNAME'));

            if (!$username) {
                return redirect()->route('unauthorized')->with('error', 'USERNAME not found.');
            }

            $user = User::where('username', $username)->first();
        } else {
            // Fallback to database authentication
            $user = auth()->user();
        }

        if (!$user) {
            return redirect()->route('unauthorized')->with('error', 'Access denied.');
        }

        // Log user authentication success
        Log::info('User authenticated:', ['user' => $user]);

        // Ensure the user is logged in for the session
        auth()->login($user);

        return $next($request);
    }
}
