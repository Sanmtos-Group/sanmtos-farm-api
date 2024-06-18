<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MergeStoreAddressableFilter
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
            
            $updated_filters = array_merge($filters, [
                'addressable_id' => $user->store->id?? null,
                'addressable_type'=> $user->store::class ?? null
            ]);

            $request->merge([
                'filter' => $updated_filters,
                'addressable_id' => $user->store->id?? null,
                'addressable_type'=> $user->store::class ?? null

            ]);
        }

        return $next($request);

    }
}
