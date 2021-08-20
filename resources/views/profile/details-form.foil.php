<div class="col-lg-6 col-md-12 mb-4">
    <h3>
        Update Your Profile
    </h3>
    <hr>
    <?= Former::open()
        ->populate( $t->details )
        ->method( 'post' )
        ->id( 'infos' )
        ->action( route ( 'profile@update-profile' ) )
        ->customInputWidthClass( 'col-xl-6 col-lg-8 col-sm-6' )
        ->customLabelWidthClass( 'col-sm-4' )
        ->actionButtonsCustomClass( 'grey-box' );
    ?>

    <?= Former::text( 'username' )
        ->label( 'Username' )
    ?>

    <?= Former::text( 'name' )
        ->label( 'Name' )
        ->placeholder( 'Firstname Lastname' )
    ?>

    <?= Former::text( 'email' )
        ->label( 'Email' )
    ?>

    <?= Former::text( 'authorisedMobile' )
        ->label( 'Mobile' )
        ->placeholder( config( 'ixp_fe.customer.form.placeholders.phone' ) )
    ?>

    <?= Former::password( 'actual_password' )
        ->label( 'Current Password' )
    ?>

    <?= Former::actions(
        Former::primary_submit( 'Update Profile' )
    );
    ?>
    <?= Former::close() ?>
</div>