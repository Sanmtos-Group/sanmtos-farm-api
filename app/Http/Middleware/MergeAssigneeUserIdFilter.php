<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MergeAssigneeUserIdFilter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $filters = $request->input('filter', []);
        $updated_filters = array_merge($filters, ['assignee_user_id'=> $request->user()->id ?? null]);
        $request->merge(['filter' => $updated_filters]);
    
        return $next($request);
    }
}
