<?php

namespace App\Http\Middleware;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Response;
use App\Laravue\JsonResponse;
use Illuminate\Support\Facades\Cookie;
use Closure;

class SessionTimeout
{
    /**
     * Instance of Session Store
     * @var session
     */
    protected $session;

    /**
     * Time for user to remain active, set to 900secs( 15minutes )
     * @var timeout
     */

    public function __construct(Store $session){
        $this->session        =  $session;
        $this->redirectUrl    = 'auth/login';
        $this->sessionLabel   = 'warning';
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
        $timeout = env('SESSTION_TIME_OUT',1800);
        if($request->path() != "api/auth/login")
        {
            if(!$this->session->has('lastActivityTime'))
            {
                $this->session->put('lastActivityTime', time());
            }
            else if(time() - $this->session->get('lastActivityTime') > $timeout)
            {
                $this->session->forget('lastActivityTime');
                Auth::guard('web')->logout();
                $this->DeleteCookies();
                return response()->json(["error" => "dashboard.message.timeout"],401);
            }
        }
        $this->session->put('lastActivityTime',time());
        return $next($request);
    }
    /**
     * Get timeout from laravel default's session lifetime, if it's not set/empty, set timeout to 15 minutes
     * @return int
     */
    private function DeleteCookies()
    {
        $cookies = collect(Cookie::Get());
        $keys = $cookies->keys();
        foreach ($keys as $key => $value) {
            if($value == "isLogged")
            {
                Cookie::queue(Cookie::forget($value));
            }
        }
    }
}
