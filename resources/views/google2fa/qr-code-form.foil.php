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

            <div class="card">
                <div class="card-header">
                    <h3>
                        Two Factor Authentification
                    </h3>
                </div>
                <div class="card-body">
                    <p>
                        Two factor authentication (2FA) strengthens access security by requiring two methods (also referred to as factors) to verify your identity. Two factor authentication protects against phishing, social engineering and password brute force attacks and secures your logins from attackers exploiting weak or stolen credentials.
                    </p>
                    <p>
                        To Enable Two Factor Authentication on your Account, you need to do following steps.
                    </p>
                    <p>
                        <b>Step 1:</b> Set up your two factor authentication by scanning the barcode below. Alternatively, you can use the code: <b><?= $t->ps->getGoogle2faSecret() ?></b>
                    </p>
                    <p>
                        <img class="img-fluid mx-auto" src="<?= $t->qrCodeImg ?>">
                    </p>

                    <?php if( !$t->ps->isGoogle2faEnable() ): ?>
                        <p>
                            <b>Step 2:</b> Enter the 6-digit code you see in your authentificator app.
                        </p>
                        <br/>
                        <div class="col-lg-6 mx-auto">
                            <?= Former::open()
                                ->method( 'post' )
                                ->id( "password" )
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
                            ?>

                            <?= Former::checkbox( 'remember_me' )
                                ->label('&nbsp;')
                                ->text( 'Remember me' )
                                ->value( 1 )
                                ->check( Session::pull( "remember" ) )
                                ->inline()
                                ->blockHelp( '' );
                            ?>

                            <?= Former::hidden( 'ixp-2fa-token' )
                                ->value( $t->ixp2faToken )
                            ?>

                            <?= Former::actions(
                                Former::primary_submit( 'Enable 2FA' )
                            );
                            ?>

                            <?= Former::close() ?>
                        </div>

                    <?php else: ?>

                        <p>
                            Test your code enter the 6-digit code you see in your authentificator app.
                        </p>
                        <br/>
                        <div class="col-lg-6 mx-auto">
                            <?= Former::open()
                                ->method( 'post' )
                                ->id( "password" )
                                ->action( route ( "2fa@test-code" ) )
                                ->customInputWidthClass( 'col-sm-6' )
                                ->customLabelWidthClass( 'col-sm-3' )
                                ->actionButtonsCustomClass( "grey-box")
                                ->rules([
                                    'verify-code'  => 'required|max:6',
                                ])
                            ?>

                            <?= Former::text( 'one_time_password' )
                                ->label( 'Code' )
                            ?>

                            <?= Former::actions(
                                Former::primary_submit( 'Test my 2FA code' )
                            );
                            ?>

                            <?= Former::close() ?>
                        </div>
                        <div class="form-group col-sm-12">
                            <div class="bg-light p-4 mt-4 shadow-sm text-center col-lg-12">
                                <a href="<?= route( "profile@edit" ) ?>" class="mb-2 mb-sm-0 btn-secondary btn">Go Back</a>
                            </div>
                        </div>

                    <?php endif; ?>
                </div>
            </div>

        </div>

    </div>





<?php $this->append() ?>