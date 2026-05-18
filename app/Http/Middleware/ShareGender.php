<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ShareGender
{
    public function handle(Request $request, Closure $next): Response
    {
        $gender = session('gender', 'men');
        View::share('activeGender', $gender);

        return $next($request);
    }
}
