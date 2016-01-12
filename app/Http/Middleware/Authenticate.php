<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Routing\UrlGenerator;

class Authenticate
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * The URL generator instance.
     *
     * @var \Illuminate\Routing\UrlGenerator
     */
    protected $generator;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth, UrlGenerator $generator)
    {
        $this->auth = $auth;
        $this->generator = $generator;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $this->auth->user();

        // the user is not logged in, return an error or redirect him to the login page
        if ($user === null) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                $path = $this->generator->route('auth.login.show');
                return redirect()->guest($path);
            }
        }

        // the user is not active, log him out and return an error or redirect him to the login page
        if (isset($user->is_active) && !$user->is_active) {
            $this->auth->logout();

            if ($request->ajax()) {
                return response('Forbidden', 403);
            }
            else {
                $path = $this->generator->route('auth.login.show');
                return redirect()->guest($path)
                    ->withErrors('Your account is inactive or pending confirmation');
            }
        }

        return $next($request);
    }
}
