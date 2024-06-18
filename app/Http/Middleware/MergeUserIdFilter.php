<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MergeUserIdFilter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if(!is_null($user))
        {
            $filters = $request->input('filter', []);
            $updated_filters = array_merge($filters, ['user_id'=>$user->id]);
            $request->merge(['filter' => $updated_filters]);
        }

        return $next($request);

    }
}
