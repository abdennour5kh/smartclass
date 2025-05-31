<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ForceStudentPasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if($user && strtolower($user->role) === 'student') {
            $student = $user->student;

            if($student->created_at->eq($student->updated_at)) {
                if($request->routeIs('force_password_change') || $request->routeIs('global_update_password')) {
                    return $next($request);
                }

                return redirect()->route('force_password_change');
            }
        }
        return $next($request);
    }
}
