<?php $this->layout( 'layouts/ixpv4' ) ?>

<?php $this->section( 'title' ) ?>
    Forgot Username
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
                ->action( route( 'forgot-password@username-email' ) )
                ->customInputWidthClass( 'col-sm-5' )
                ->addClass( 'col-md-offset-4' );

            ?>

            <div>
                Please enter your email address and we will send you any related username(s) by email.
            </div>

            <br />

            <?= Former::text( 'email' )
                ->label( 'Email' )
                ->required()
                ->blockHelp( '' )
            ?>

            <?= Former::actions( Former::primary_submit( 'Find Username(s)' ),
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