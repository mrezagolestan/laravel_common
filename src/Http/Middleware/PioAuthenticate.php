<?php

namespace Piod\LaravelCommon\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Piod\LaravelCommon\Models\Pio\User;
use Piod\LaravelCommon\Repositories\UserRepository;

class PioAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (Auth::check()) {
            return $next($request);
        }


        if (! ($request->bearerToken() && ! empty($request->bearerToken()))) {
            return response()->custom(null, __("http.unauthorized"), Response::HTTP_UNAUTHORIZED);
        }


        $userToken = UserRepository::checkAuthentication($request->bearerToken());
        if (!$userToken) {
            return response()->custom(null, __("http.unauthorized"), Response::HTTP_UNAUTHORIZED);
        }

        $user = User::query()->findOrFail($userToken->user_id);
        Auth::login($user);

        return $next($request);
    }
}
