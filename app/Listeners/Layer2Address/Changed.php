<?php

namespace IXP\Listeners\Layer2Address;

use Mail;

use IXP\Events\Layer2Address\Deleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use IXP\Mail\Layer2Address\Email as EmailLayer2Address;

class Changed
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
     * @param  Deleted  $event
     * @return void
     */
    public function handle( $event)
    {
        if( config( 'ixp_fe.layer2-addresses.email_on_superuser_change' ) || config( 'ixp_fe.layer2-addresses.email_on_customer_change' ) ){
            $mailable = new EmailLayer2Address( $event->vli );
            try {
                $view = $mailable->view( "layer2-address/emails/changed" )->with( ['user' => $event->auth, 'vli' => $event->vli, "added" => $event->action == "add" ? true : false , "mac" => $event->mac ] )->render();
                $mailable->prepareBody( $view );

                Mail::send( $mailable );

            } catch( MailableException $e ) {
                AlertContainer::push( $e->getMessage(), Alert::DANGER );

            }

        }

    }
}
