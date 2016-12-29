<?php

/*
 * This file is part of jwt-auth.
 *
 * (c) Sean Tymon <tymon148@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tymon\JWTAuth\Middleware;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class GetUserFromToken extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        if (! $token = $this->auth->setRequest($request)->getToken()) {
            //mine
            $response = [
                'error' => true,
                'message' => 'No authorization token provided'
            ];

//            return $this->respond('tymon.jwt.absent', 'token_not_provided', 400);
            return $this->respond('tymon.jwt.absent', $response, 400);
        }

        try {
            $user = $this->auth->authenticate($token);
        } catch (TokenExpiredException $e) {
            $response = [
                'error' => true,
                'message' => 'Token expired'
            ];
            return $this->respond('tymon.jwt.expired', $response, $e->getStatusCode(), [$e]);
//            return $this->respond('tymon.jwt.expired', 'token_expired', $e->getStatusCode(), [$e]);
        } catch (JWTException $e) {
            $response = [
                'error' => true,
                'message' => 'Token invalid signature could not be verified'
            ];
            return $this->respond('tymon.jwt.expired', $response, $e->getStatusCode(), [$e]);
//            return $this->respond('tymon.jwt.invalid', 'token_invalid', $e->getStatusCode(), [$e]);
        }

        if (! $user) {
            $response = [
                'error' => true,
                'message' => 'User not found'
            ];
            return $this->respond('tymon.jwt.expired', $response,404);
//            return $this->respond('tymon.jwt.user_not_found', 'user_not_found', 404);
        }

        $this->events->fire('tymon.jwt.valid', $user);

        return $next($request);
    }
}
