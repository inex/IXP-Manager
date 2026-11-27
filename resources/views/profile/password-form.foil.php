<?php

use Illuminate\Support\Js;

?>
<div class="col-lg-6 col-md-12 mb-4">
    <h3>
        Change Your Password
    </h3>
    <hr>

    <?= Former::open()
        ->method( 'post' )
        ->id( 'password' )
        ->action( route ( 'profile@update-password' ) )
        ->customInputWidthClass( 'col-xl-6 col-lg-8 col-sm-6' )
        ->customLabelWidthClass( 'col-sm-4' )
        ->actionButtonsCustomClass( 'grey-box' )
        ->rules([
            'current_password'   => 'required|max:255',
            'new_password'       => 'required|max:255',
            'confirm_password'   => 'required|max:255',
        ])
    ?>

    <?= Former::password( 'current_password' )
        ->label( 'Current Password' )
    ?>

    <?= Former::password( 'new_password' )
        ->label( 'New Password' )
    ?>

    <?= Former::password( 'confirm_password' )
        ->label( 'Confirm Password' )
    ?>

    <?= Former::actions(
            Former::secondary_link( 'Password Policy' )->href("#")->class( "btn-password-policy" )->data_policy( Js::encode( $this->passwordPolicy ) ),
            Former::primary_submit( 'Update Password' ),
    );
    ?>

    <?= Former::close() ?>
</div>