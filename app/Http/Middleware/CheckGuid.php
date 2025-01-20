<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class CheckGuid
{
    public function handle(Request $request, Closure $next)
    {
        $guid = $request->server('HTTP_SHIBBOLETH_GUID', env('TEST_GUID'));

        if (!$guid) {
            return redirect()->route('unauthorized')->with('error', 'GUID not found.');
        }

        $user = User::where('guid', $guid)->first();

        if (!$user) {
            return redirect()->route('unauthorized')->with('error', 'Access denied. User not found.');
        }

        auth()->login($user);

        Log::info('User authenticated:', ['user' => $user]);

        return $next($request);
    }
}
