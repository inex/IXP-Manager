<?php $this->layout( 'layouts/ixpv4' ) ?>

<?php $this->section( 'title' ) ?>
    Login to <?= config( "identity.legalname" ) ?>
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
            ->action( route( 'login@login' ) )
            ->customWidthClass( 'col-sm-4' )
            ->addClass( 'col-md-offset-4' );

        ?>

        <?= Former::text( 'username' )
            ->label( 'Username' )
            ->blockHelp( '' )
        ?>

        <?= Former::password( 'password' )
            ->label( 'Password' )
            ->blockHelp( '' );
        ?>

        <?= Former::actions( Former::primary_submit( 'Login' ),
            Former::default_link( 'Forgot Password' )->href( route( "forgot-password@show-form" ) ),
            Former::default_link( 'Forgot Username' )->href( route( "forgot-password@showUsernameForm" ) ),
            Former::success_button( 'Help' )->id( 'help-btn' )
        );?>


        <?= Former::close() ?>

    </div>

</div>



<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>

<?php $this->append() ?>