<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePaydayScheduleIsSet
{
    public function handle(Request $request, Closure $next): Response
    {
        if (
            $request->user()
            && $request->user()->application_mode === 'kos'
            && (! $request->user()->payday_interval || ! $request->user()->payday_anchor_date)
            && ! $request->routeIs('budget.schedule.setup', 'budget.schedule.setup.update')
        ) {
            return redirect()->route('budget.schedule.setup');
        }

        return $next($request);
    }
}
