<?php $this->layout( 'layouts/ixpv4' ) ?>

<?php $this->section( 'page-header-preamble' ) ?>
    Password Reset
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>

<?php $this->append() ?>


<?php $this->section( 'content' ) ?>
    <div class="row">
        <div class="col-lg-12">

            <?= $t->alerts() ?>

            <div class="text-center my-4">
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

        </div>
    </div>

    <div class="row"
         <div class="col-12">

            <div class="w-full max-w-sm mx-auto">

                <?= Former::open()->method( 'POST' )
                    ->action( route( 'reset-password@reset' ) )
                    ->class( "bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4" )
                ?>


                <p class="mb-4 text-grey-dark font-bold">
                    Please enter your username, the token that was emailed to you and a new password below.
                </p>

                <div class="mb-4">
                    <label class="block text-grey-darker text-sm font-bold mb-2" for="username">
                        Username
                    </label>
                    <input name="username" class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker leading-tight focus:outline-none focus:shadow-outline"
                           id="username" type="text" placeholder="Username" value="<?= $t->username ?? old('username') ?>">
                    <?php foreach( $t->errors->get( 'username' ) as $err ): ?>
                        <p class="text-red text-xs italic mt-2"><?= $err ?></p>
                    <?php endforeach; ?>
                </div>

                <div class="mb-4">
                    <label class="block text-grey-darker text-sm font-bold mb-2" for="token">
                        Token
                    </label>
                    <input name="token" class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker leading-tight focus:outline-none focus:shadow-outline"
                           id="token" type="text" placeholder="" value="<?= $t->token ?? old('token') ?>">
                    <?php foreach( $t->errors->get( 'token' ) as $err ): ?>
                        <p class="text-red text-xs italic mt-2"><?= $err ?></p>
                    <?php endforeach; ?>

                </div>


                <div class="mb-6">
                    <label class="block text-grey-darker text-sm font-bold mb-2" for="password">
                        Password
                    </label>
                    <input name="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker leading-tight focus:outline-none focus:shadow-outline"
                            id="password" type="password" autofocus placeholder="******************">
                    <?php foreach( $t->errors->get( 'password' ) as $err ): ?>
                        <p class="text-red text-xs italic mt-2"><?= $err ?></p>
                    <?php endforeach; ?>
                </div>

                <div class="mb-6">
                    <label class="block text-grey-darker text-sm font-bold mb-2" for="password_confirmation">
                        Confirm Password
                    </label>
                    <input name="password_confirmation" class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker leading-tight focus:outline-none focus:shadow-outline" id="password_confirmation" type="password" placeholder="******************">
                    <?php foreach( $t->errors->get( 'password_confirmation' ) as $err ): ?>
                        <p class="text-red text-xs italic mt-2"><?= $err ?></p>
                    <?php endforeach; ?>
                </div>


                <div class="flex items-center justify-between">
                    <a class="inline-block align-baseline font-bold text-sm text-blue-light
                            hover:no-underline  hover:text-blue-dark" href="<?= route( "login@login" ) ?>">
                        Return to Login
                    </a>
                    <button class="bg-blue hover:bg-blue-dark text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                        Reset
                    </button>
                </div>

            </div>

            <?= Former::close() ?>
        </div>
    </div>




<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>

<?php $this->append() ?>