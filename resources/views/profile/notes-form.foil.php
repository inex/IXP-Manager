<?php if( Auth::getUser()->isSuperUser() ): ?>
    <div class="col-lg-6 col-md-12">
        <h3>
            <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?> Notes
        </h3>
        <hr>
        <?= Former::open()
            ->populate( $t->notesNotifications )
            ->method( 'post' )
            ->id( 'note' )
            ->action( route ( 'profile@update-notification-preference' ) )
            ->customInputWidthClass( 'col-sm-10' )
            ->actionButtonsCustomClass( 'grey-box' );
        ?>

        <?=  Former::radios('')
            ->radios([
                '&nbsp;&nbsp;&nbsp;Disable all email notifications'                                                                   => [ 'name' => 'notify', 'id' => 'notify-none',     'value' => 'none'    ],
                '&nbsp;&nbsp;&nbsp;Email me on changes to only watched ' . config( 'ixp_fe.lang.customer.many' ) . ' and notes'   => [ 'name' => 'notify', 'id' => 'notify-watched',  'value' => 'watched' ],
                '&nbsp;&nbsp;&nbsp;Email me on any change to any ' . config( 'ixp_fe.lang.customer.one' ) . ' note'               => [ 'name' => 'notify', 'id' => 'id="notify-all"', 'value' => 'all'     ],
            ])
            ->name( 'notify' )
            ->label( '' )
            ->setValue( 'all' );
        ?>

        <?= Former::actions(
            Former::primary_submit( 'Set Notification Preference' )
        );
        ?>

        <?= Former::close() ?>
    </div>
<?php endif; ?>