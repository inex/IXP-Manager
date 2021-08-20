<div class="alert alert-success tw-my-8" role="alert">
    You have two factor authentication enabled.
</div>

<p>
    You can use the QR image or the code below to (re)configure your current or a new 2fa code generator.
</p>

<div class="tw-mx-auto">
    <?= $t->qrcode ?>
</div>

<p>
    Code: <b class="tw-font-mono"><?= $t->user->user2FA->secret ?></b>
</p>

<hr class="tw-my-8">

<p>
    If you wish to disable two-factor authentication, please enter your password below.
</p>
<p>
    <em>Note that if you disable 2fa but the administrator requires its usage, then you will be immediately asked to re-enable it. This can be used as a mechanism for resetting your 2fa secret.</em>
</p>
<br/>

<div class="col-lg-6 mx-auto">
    <?= Former::open()
        ->method( 'post' )
        ->action( route ( "2fa@disable" ) )
        ->customInputWidthClass( 'col-sm-6' )
        ->customLabelWidthClass( 'col-sm-3' )
        ->actionButtonsCustomClass( "grey-box")
    ?>

    <?= Former::password( 'password' )
        ->label( 'Password' )
        ->required( true )
    ?>

    <?= Former::actions(
        Former::danger_submit( 'Disable 2FA' )
    );
    ?>

    <?= Former::close() ?>
</div>