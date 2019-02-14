<?php $this->layout( 'layouts/ixpv4' ) ?>

<?php $this->section( 'page-header-preamble' ) ?>
    Login to <?= config( "identity.sitename" ) ?>
<?php $this->append() ?>


<?php $this->section( 'content' ) ?>
<div class="row">
    <div class="col-lg-12">

        <?= $t->alerts() ?>

        <div class="text-center">
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
                <?= Former::open()->method( 'POST' )
                    ->action( route( 'login@login' ) )
                    ->customInputWidthClass( 'col-sm-auto col-md-auto col-lg-auto ' )
                    ->customLabelWidthClass( 'col-lg-4 col-md-4 col-sm-4 text-sm-right' )
                    ->actionButtonsCustomClass( 'text-center col-sm-12 col-md-12 col-lg-12' )

                ?>

                <?= Former::text( 'username' )
                    ->label( 'Username' )
                    ->required()
                    ->class( "align-items-center" )
                    ->autofocus( old( 'username' ) ? false : true )
                ?>

                <?= Former::password( 'password' )
                    ->label( 'Password' )
                    ->required()
                    ->blockHelp( '' )
                    ->autofocus( old( 'username' ) ? true : false );
                ?>

                <?= Former::checkbox( 'remember' )
                    ->label( '&nbsp;' )
                    ->text( 'Remember Me' )
                    ->value( 1 )
                    ->inline()
                    ->blockHelp( "" );
                ?>


                <?= Former::actions( Former::primary_submit( 'Login' ) )?>


                <div class="text-center">
                    <a href="<?= route( "forgot-password@show-form" ) ?>"        class="btn-info btn mt-2">
                        Forgot Password
                    </a>
                    <a href="<?= route( "forgot-password@showUsernameForm" ) ?>" class="btn-info btn mt-2">
                        Forgot Username
                    </a>
                </div>



            <?= Former::close() ?>

            </div>
        </div>



    </div>

</div>



<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>

<?php $this->append() ?>