<?php

namespace App\Providers;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Laravue\Models\User;
use App\Laravue\Acl;
use Illuminate\Support\Facades\Crypt;
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        Event::listen('Aacotroneo\Saml2\Events\Saml2LoginEvent', function (\Aacotroneo\Saml2\Events\Saml2LoginEvent $event) {
            $user = $event->getSaml2User();
            $userData = [
                'id' => $user->getUserId(),
                'attributes' => $user->getAttributes(),
            ];
            $unique_value = $user->getUserId();
            if(isset($userData['attributes']['uniqueid'][0]))
            $mail = hash('sha256',$userData['attributes']['uniqueid'][0]);//don't save user private information
            else
            $mail = hash('sha256',$userData['attributes']['http://schemas.microsoft.com/identity/claims/objectidentifier'][0]);//don't save user private information
            $name = $mail;
            $user = User::where('email',$mail)->first();
            if ($user) { //if the account exits
                $user->update([
                    "name" => $name, //update the name if display name on azure change
                    "password" => \Illuminate\Support\Facades\Hash::make($unique_value)
                ]);
            } else { //if not exits, create new user azure in db
                $user = User::create( [
                    "email" =>$mail,
                    "name" => $name,
                    'password' => \Illuminate\Support\Facades\Hash::make($unique_value),
                    'avatar' => 'default-avatar.jpg' //hash unique value to record in db
                ]);
                $user->syncRoles(Acl::ROLE_USER);// set role for this user is user.
            }
            $user->tokens()->delete();
            $userInfor = [
                "email" =>  base64_encode($mail), //account is email azure of user
                "token" => $user->createToken('authToken')->plainTextToken
            ];
            $encode = Crypt::encrypt(json_encode($userInfor));
            $redirect_uri = "/#/azurelogin?m=".$encode;
            abort(redirect($redirect_uri));
        });
        Event::listen('Aacotroneo\Saml2\Events\Saml2LogoutEvent', function ($event) {
            Auth::logout();
            Session::save();
        });
    }
}
