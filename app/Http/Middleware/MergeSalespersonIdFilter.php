<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MergeSalespersonIdFilter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        $filters = $request->input('filter', []);
        $updated_filters = array_merge($filters, ['salesperson_id'=>$user->id?? null]);
        $request->merge([
            'filter' => $updated_filters,
            'salesperson_id' => $user->id,
        ]);

        return $next($request);

    }
}
