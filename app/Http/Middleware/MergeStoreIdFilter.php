<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MergeStoreIdFilter
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
            $updated_filters = array_merge($filters, ['store_id'=>$user->store->id?? null]);
            $request->merge(['filter' => $updated_filters]);
        }

        return $next($request);

    }
}
