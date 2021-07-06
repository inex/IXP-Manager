<?php if( Auth::getUser()->is2faEnforced() ): ?>
    <div class="alert alert-warning tw-my-8" role="alert">
        You do not have two-factor authentication enabled but it is compulsory for your user account. Please configure and enable 2fa below to proceed.
    </div>
<?php endif; ?>
<p>
    To enable two factor authentication on your account, you need to perform the following steps.
</p>

<p>
    <b>Step 1:</b> Set up your two factor authentication by scanning the barcode below.
</p>

<div class="tw-mx-auto">
    <?= $t->qrcode ?>
</div>

<p>
    Alternatively, you can enter this code manually into your authenticator application: <b class="tw-font-mono"><?= $t->user->user2FA->secret ?></b>
</p>

<p>
    <b>Step 2:</b> Enter the 6-digit code you see in your authenticator app and your password below.
</p>
<br/>

<div class="col-lg-6 mx-auto">
    <?= Former::open()
        ->method( 'post' )
        ->action( route ( "2fa@enable" ) )
        ->customInputWidthClass( 'col-sm-6' )
        ->customLabelWidthClass( 'col-sm-3' )
        ->actionButtonsCustomClass( "grey-box")
        ->rules([
            'verify-code'  => 'required|max:6',
        ])
    ?>

    <?= Former::text( 'one_time_password' )
        ->label( 'Code' )
        ->autocomplete( 'off' )
        ->required( true )
    ?>

    <?= Former::password( 'password' )
        ->label( 'Password' )
        ->required( true )
    ?>

    <?= Former::actions(
        Former::primary_submit( 'Enable 2FA' )
    );
    ?>

    <?= Former::close() ?>
</div>
