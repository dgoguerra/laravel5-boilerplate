<?php

namespace App\Http\Middleware;

use Closure;

class CorsRequest
{
    protected $corsHeaders = [
        'Access-Control-Allow-Origin'  => '*',
        'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS, PUT, DELETE',
        'Access-Control-Allow-Headers' => 'Origin, X-Requested-With, Content-Type, Accept, Authorization'
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = null;

        // if its a preflight request it's only checking for the Access-Control headers
        // before making the actual call. In that case, stop executing the middlewares chain
        // and just answer with a successful response with the headers.
        if ($request->getMethod() === 'OPTIONS') {
            return response('', 200, $this->corsHeaders);
        }

        // continue request lifecycle.
        $response = $next($request);

        // if the response is a string, an array, etc. convert it to a Response object before.
        if (!($response instanceof \Symfony\Component\HttpFoundation\Response)) {
            $response = response($response);
        }

        // add the CORS headers to the response.
        foreach($this->corsHeaders as $key => $val) {
            $response->headers->set($key, $val);
        }

        return $response;
    }
}
