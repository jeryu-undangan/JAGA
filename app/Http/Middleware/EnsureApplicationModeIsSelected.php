<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApplicationModeIsSelected
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if ($request->routeIs('application-mode.update', 'logout')) {
            return $next($request);
        }

        if (! in_array($user->application_mode, ['kos', 'salary'], true)) {
            if ($request->routeIs('application-mode.choose')) {
                return $next($request);
            }

            return redirect()->route('application-mode.choose');
        }

        if ($request->routeIs('application-mode.choose')) {
            if ($user->application_mode === 'salary') {
                return redirect()->route('salary.index');
            }

            if (! $user->payday_interval || ! $user->payday_anchor_date) {
                return redirect()->route('budget.schedule.setup');
            }

            return redirect()->route('budget.index');
        }

        if (
            $user->application_mode === 'salary'
            && ! $request->routeIs(
                'salary.*',
                'budget.settings',
                'profile.edit',
                'profile.update',
                'preferences.update',
                'theme.update',
                'password.update',
            )
        ) {
            return redirect()->route('salary.index');
        }

        if ($user->application_mode === 'kos' && $request->routeIs('salary.*')) {
            return redirect()->route('budget.index');
        }

        return $next($request);
    }
}
