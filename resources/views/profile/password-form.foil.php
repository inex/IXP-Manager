<div class="col-lg-6 col-md-12 mb-4">
    <h3>
        Change Your Password
    </h3>
    <hr>
    <div class="alert alert-info mt-4" role="alert">
        <div class="d-flex align-items-center">
            <div class="text-center">
                <i class="fa fa-info-circle fa-2x"></i>
            </div>
            <div class="col-sm-12">
                Passwords are stored in IXP Manager using <a href="https://en.wikipedia.org/wiki/Bcrypt">bcrypt</a>.
            </div>
        </div>
    </div>

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
            'confirm_password'   => 'required|max:255|same:new_password',
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
        Former::primary_submit( 'Update Password' )
    );
    ?>

    <?= Former::close() ?>
</div>