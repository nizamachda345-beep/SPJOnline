<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AjaxResponseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */

    public function handle(Request $request, Closure $next)
{
    $response = $next($request);

    if ($request->ajax() && $response instanceof \Illuminate\Http\Response) {
        $content = $response->getContent();
        if (preg_match('/<div id="main-content">(.*?)<\/div>/s', $content, $matches)) {
            return response()->json(['html' => $matches[1]]);
        }
    }

    return $response;
}
}
