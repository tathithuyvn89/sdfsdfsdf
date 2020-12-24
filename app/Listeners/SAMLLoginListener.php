<?php

namespace App\Listeners;

use App\Events\Aacotroneo\Saml2\Events\Saml2LoginEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SAMLLoginListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Saml2LoginEvent  $event
     * @return void
     */
    public function handle(Saml2LoginEvent $event)
    {
        //
    }
}
