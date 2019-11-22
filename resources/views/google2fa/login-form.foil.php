<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Security / Two Factor Authentification
<?php $this->append() ?>


<?php $this->section('content') ?>
<div class="row">

    <div class="col-sm-12">

        <?= $t->alerts() ?>

        <?php if( request()->exists( "one_time_password" ) ): ?>
            <div class="alert alert-danger alert-dismissible mb-16" role="alert">
                <div class="d-flex align-items-center">
                    <div class="text-center">
                        <i class="fa fa-exclamation-triangle fa-2x "></i>
                    </div>
                    <div class="col-sm-12">
                        <?= config( "google2fa.error_messages.wrong_otp" ) ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class=" tw-w-full tw-max-w-md tw-mx-auto">
            <div class="card">
                <div class="card-header">
                    <h3>
                        Two Factor Authentification
                    </h3>
                </div>
                <div class="card-body">
                    <p>Two factor authentication (2FA) strengthens access security by requiring two methods (also referred to as factors) to verify your identity. Two factor authentication protects against phishing, social engineering and password brute force attacks and secures your logins from attackers exploiting weak or stolen credentials.</p>
                    <strong>Enter the pin from Google Authenticator Enable 2FA</strong><br/><br/>
                    <?= Former::open()
                        ->method( 'post' )
                        ->action( route ( "2fa@authenticate" ) )
                        ->customInputWidthClass( 'col-sm-6' )
                        ->customLabelWidthClass( 'col-sm-4' )
                        ->actionButtonsCustomClass( "grey-box")
                    ?>

                        <?= Former::text( 'one_time_password' )
                            ->id( "one_time_password" )
                            ->required( true )
                            ->label( 'One Time Password' )
                            ->forceValue( '' )
                            ->autofocus( true )
                        ?>

                        <?= Former::checkbox( 'remember_me' )
                            ->label('&nbsp;')
                            ->text( 'Remember me' )
                            ->value( 1 )
                            ->check( Session::pull( "remember" ) )
                            ->inline()
                            ->blockHelp( '' );
                        ?>

                        <?= Former::actions(
                            Former::primary_submit( 'Authenticate' )
                        );
                        ?>

                    <?= Former::close() ?>
                </div>
            </div>
        </div>


    </div>
</div>

<?php $this->append() ?>
