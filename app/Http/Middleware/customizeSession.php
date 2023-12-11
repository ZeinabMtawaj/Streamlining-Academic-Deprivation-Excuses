<?php 

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;

class customizeSession {
    public function handle($request, Closure $next)
    {
        // dd(config('admin.route.prefix'));
        // if ($request->is(config('admin.route.prefix') . '/*')) {

        if (strpos($request->path(), 'admin') !== false) {

            // If the request is for the admin area
            Config::set('session.cookie', 'admin_session_cookie');
            
        } else {
            // For other areas (non-admin)
            Config::set('session.cookie', 'user_session_cookie');
        }

        return $next($request);
    }
}
