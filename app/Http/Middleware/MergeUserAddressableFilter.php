<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MergeUserAddressableFilter
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
                'addressable_id' => $user->id,
                'addressable_type'=> $user::class
            ]);

            $request->merge([
                'filter' => $updated_filters,
                'addressable_id' => $user->id,
                'addressable_type'=> $user::class

            ]);
        }

        return $next($request);

    }
}
