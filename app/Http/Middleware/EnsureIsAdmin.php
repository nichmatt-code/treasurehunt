<?php

namespace App\Http\Middleware;

use App\Models\Participant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->cookie('participant_token');
        $participant = $token ? Participant::where('session_token', $token)->first() : null;

        if (! $participant || ! $participant->isAdmin()) {
            abort(403, 'Halaman ini khusus admin.');
        }

        $request->attributes->set('participant', $participant);

        return $next($request);
    }
}
