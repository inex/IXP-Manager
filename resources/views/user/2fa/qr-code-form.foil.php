<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' )
?>


<?php $this->section( 'page-header-preamble' ) ?>
    Security / Two Factor Authentication
<?php $this->append() ?>


<?php $this->section('content') ?>
    <div class="row">

        <div class="col-sm-12">

            <?= $t->alerts() ?>

            <div class="card">
                <div class="card-header">
                    <h3>
                        Two Factor Authentication
                    </h3>
                </div>
                <div class="card-body">
                    <p>
                        Two factor authentication (2FA) strengthens access security by requiring two authentication methods to verify your identity. Two factor authentication protects against phishing, social engineering and password brute force attacks and secures your logins from attackers exploiting weak or stolen credentials.
                    </p>
                    <p class="tw-pb-8 tw-border-b-2">
                        <b>IXP Manager</b> supports a Google Authenticator compatible HMAC-Based One-time Password (HOTP) algorithm as specified in <a href="https://tools.ietf.org/html/rfc4226">RFC 4226</a>
                        and the Time-based One-time Password (TOTP) algorithm specified in <a href="https://tools.ietf.org/html/rfc6238">RFC 6238</a>.
                    </p>
                    <p class="tw-pt-4">
                        To enable two factor authentication on your account, you need to do the following steps.
                    </p>
                    <p>
                        <b>Step 1:</b> Set up your two factor authentication by scanning the barcode below.
                    </p>
                    <div class="tw-mx-auto">
                        <?= $t->qrCodeImg ?>
                    </div>
                    <p>
                        Alternatively, you can enter this code manually into your authenticator application: <b class="tw-font-mono"><?= $t->ps->getSecret() ?></b>
                    </p>
                    <?php if( !$t->ps->enabled() ): ?>
                        <p>
                            <b>Step 2:</b> Enter the 6-digit code you see in your authenticator app.
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

                            <?= Former::password( 'pass' )
                                ->label( 'Password' )
                                ->required( true )
                            ?>

                            <input type="hidden" name="ixp-2fa-token" value="<?= $t->ixp2faToken ?>">

                            <?= Former::actions(
                                Former::primary_submit( 'Enable 2FA' )
                            );
                            ?>

                            <?= Former::close() ?>
                        </div>

                    <?php else: ?>

                        <p>
                            <b>Step 2:</b> Test your code - enter the 6-digit code you see in your authenticator app.
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