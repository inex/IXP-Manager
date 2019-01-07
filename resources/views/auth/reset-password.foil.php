<?php $this->layout( 'layouts/ixpv4' ) ?>

<?php $this->section( 'title' ) ?>
    Reset Password
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>

<?php $this->append() ?>


<?php $this->section( 'content' ) ?>
    <div class="row">
        <div class="col-sm-12">

            <?= $t->alerts() ?>

            <div align="center">

                <?php if( config( "identity.biglogo" ) ) :?>
                    <img src="<?= config( "identity.biglogo" ) ?>" />
                <?php else: ?>
                    <h2>
                        [Your Logo Here]
                    </h2>
                    <div>
                        Configure <code>IDENTITY_BIGLOGO</code> in <code>.env</code>.
                    </div>
                <?php endif; ?>
            </div>

            <br /><br />

            <?= Former::open()->method( 'POST' )
                ->action( route( 'reset-password@reset' ) )
                ->customWidthClass( 'col-sm-5' )
                ->addClass( 'col-md-offset-4' );

            ?>

            <div>
                Please enter your username, the token that was emailed to you and a new password:
            </div>

            <br />

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


            <?= Former::actions( Former::primary_submit( 'Reset Password' ),
                Former::default_link( 'Return to login' )->href( route( "login@showForm" ) )
            );?>

            <br />

            <div>
                For help please contact <a href="<?= route( 'public-content', [ 'page' => 'support' ] ) ?>"><?= config( "identity.legalname" ) ?></a>
            </div>

            <?= Former::close() ?>

        </div>

    </div>



<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>

<?php $this->append() ?>