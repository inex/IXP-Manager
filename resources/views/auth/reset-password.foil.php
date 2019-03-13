<?php $this->layout( 'layouts/ixpv4' ) ?>

<?php $this->section( 'page-header-preamble' ) ?>
    Reset Password
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>

<?php $this->append() ?>


<?php $this->section( 'content' ) ?>
    <div class="row">
        <div class="col-lg-12">

            <?= $t->alerts() ?>

            <div class="text-center mt-16 mb-16">

                <?php if( config( "identity.biglogo" ) ) :?>
                    <img class="img-fluid" src="<?= config( "identity.biglogo" ) ?>" />
                <?php else: ?>
                    <h2>
                        [Your Logo Here]
                    </h2>
                    <div>
                        Configure <code>IDENTITY_BIGLOGO</code> in <code>.env</code>.
                    </div>
                <?php endif; ?>
            </div>

            <div class="row">
                <div class="col-lg-8 mt-4 mx-auto text-center">

                    <div class="mb-4">
                        Please enter your username, the token that was emailed to you and a new password:
                    </div>

                    <?= Former::open()->method( 'POST' )
                        ->action( route( 'reset-password@reset' ) )
                        ->customInputWidthClass( 'col-sm-auto col-md-auto col-lg-auto ' )
                        ->customLabelWidthClass( 'col-lg-4 col-md-4 col-sm-4 text-sm-right' )
                        ->actionButtonsCustomClass( 'text-center col-sm-12 col-md-12 col-lg-12' )

                    ?>

                        <?= Former::text( 'username' )
                            ->label( 'Username' )
                            ->required()
                            ->blockHelp( '' )
                        ?>

                        <?= Former::text( 'token' )
                            ->label( 'Token' )
                            ->required()
                            ->blockHelp( '' )
                        ?>

                        <?= Former::password( 'password' )
                            ->label( 'Password' )
                            ->required()
                            ->blockHelp( '' )
                        ?>

                        <?= Former::password( 'password_confirmation' )
                            ->label( 'Confirm Password' )
                            ->required()
                            ->blockHelp( '' )
                        ?>


                        <?= Former::actions( Former::primary_submit( 'Reset Password' )->class( 'mt-2' ),
                            '<a href="' . route( "login@showForm" ) . '"  class="btn btn-secondary mt-2">Return to Login</a>'
                        );?>



                        <p class="text-center">
                            For help please contact <a href="<?= route( 'public-content', [ 'page' => 'support' ] ) ?>"><?= config( "identity.legalname" ) ?></a>
                        </p>

                    <?= Former::close() ?>
                </div>
            </div>

        </div>

    </div>



<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>

<?php $this->append() ?>