<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MergeWorkStoresIdFilter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        $store_id =null;

        if(!is_null($user) && $user->owns_a_store)
        {
            $store_id = $user->store->id;
        }

        $filters = $request->input('filter', []);
        $updated_filters = array_merge($filters, ['workStoresId'=>$store_id?? null]);
        $request->merge([
            'filter' => $updated_filters,
            'workStoresId' => $store_id,
        ]);

        return $next($request);

    }
}
