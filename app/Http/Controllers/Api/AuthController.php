<?php
/**
 * File AuthController.php
 *
 * @author Tuan Duong <bacduong@gmail.com>
 * @package Laravue
 * @version 1.0
 */
namespace App\Http\Controllers\Api;

use App\Http\Resources\UserResource;
use App\Laravue\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Laravue\Models\User;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use function GuzzleHttp\json_decode;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

/**
 * Class AuthController
 *
 * @package App\Http\Controllers\Api
 */
class AuthController extends BaseController
{
    use ThrottlesLogins, AuthenticatesUsers;
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $this->DeleteCookies();
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return response()->json(new JsonResponse([], 'This account has been blocked in 30 minutes'), Response::HTTP_UNAUTHORIZED);
        }

        $credentials = $request->only('email', 'password');
        if (!Auth::attempt($credentials)) {
            $this->incrementLoginAttempts($request);
            return response()->json(new JsonResponse([], 'login_error'), Response::HTTP_UNAUTHORIZED);
        }

        UserResource::withoutWrapping();
        $user = $request->user();
        $token = $user->createToken('auth-token');
        $this->clearLoginAttempts($request);
        return response()->json(new JsonResponse(["User" => new UserResource($user),"token" => $token->plainTextToken]), Response::HTTP_OK);
    }

    public function loginAAD(Request $request)
    {
        return json_encode(app('auth')->user());
    }
    public function loginWithAzure(Request $request)
    {
        $this->DeleteCookies();
        $email = "";
        $token = "";
        try {
            $decrypted = Crypt::decrypt($request->auth);
            $userInfor = json_decode($decrypted);
            $email = base64_decode($userInfor->email);
            $token = $userInfor->token;
            $tokenID = explode("|",$token);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return response()->json(["message" => "Invalid authenticate"],403);
        }
        $isTokenValid = "";
        $user = User::where('email',$email)->first();
        $isTokenValid = $user->tokens()->where('id', $tokenID[0])->first('id');
        if($isTokenValid != "")
        {
            Auth::login($user);
            $user->tokens()->delete();
            UserResource::withoutWrapping();
            return response()->json(new JsonResponse(["User" => new UserResource($user)]), Response::HTTP_OK);
        }
        return response()->json(["message" => "Invalid authenticate"],403);
    }
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
       // $logout  = Auth::guard('web');
        Auth::guard('web')->logout();
        $this->DeleteCookies();
        return response()->json((new JsonResponse())->success([]), Response::HTTP_OK);
    }
    private function DeleteCookies()
    {
        $cookies = collect(Cookie::Get());
        $keys = $cookies->keys();
        foreach ($keys as $key => $value) {
            if($value!= "sidebarStatus" && $value != "language" && $value!= "XSRF-TOKEN" && $value != "laravel_session")
            {
                Cookie::queue(Cookie::forget($value));
            }
        }
    }

    private function hasTooManyLoginAttempts(Request $request)
    {
        $this->attempts = 5;
        $this->decayMinutes = 30;
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request), $this->attempts, $this->decayMinutes
        );
    }

}
