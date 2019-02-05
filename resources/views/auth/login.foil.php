<?php $this->layout( 'layouts/ixpv4' ) ?>

<?php $this->section( 'page-header-preamble' ) ?>
    Login to <?= config( "identity.sitename" ) ?>
<?php $this->append() ?>


<?php $this->section( 'content' ) ?>
<div class="row">
    <div class="col-sm-12">

        <?= $t->alerts() ?>

        <div class="text-center mt-4">

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
        <div class="col-sm-8 mt-4 ml-auto">
            <?= Former::open()->method( 'POST' )
                ->action( route( 'login@login' ) )
                ->customInputWidthClass( 'col-sm-4' )
                ->addClass( 'text-center' );
            ?>

            <?= Former::text( 'username' )
                ->label( 'Username' )
            ?>

            <?= Former::password( 'password' )
                ->label( 'Password' )
                ->blockHelp( '' );
            ?>

            <?= Former::checkbox( 'remember' )
                ->label( '&nbsp;' )
                ->text( 'Remember Me' )
                ->value( 1 )
                ->blockHelp( "" );
            ?>


        </div>

        <div class="col-sm-12 text-center">
            <div style="margin-left: 20%">
                <?= Former::actions( Former::primary_submit( 'Login' ) )?>
            </div>

            <br><br>

            <a href="<?= route( "forgot-password@show-form" ) ?>"        class="btn-info btn">Forgot Password</a>
            <a href="<?= route( "forgot-password@showUsernameForm" ) ?>" class="btn-info btn">Forgot Username</a>

            <br><br>

            <?= Former::close() ?>
        </div>




    </div>

</div>



<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>

<?php $this->append() ?>