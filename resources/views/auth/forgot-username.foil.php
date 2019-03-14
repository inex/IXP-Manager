<?php $this->layout( 'layouts/ixpv4' ) ?>

<?php $this->section( 'page-header-preamble' ) ?>
    Forgot Username
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>

<?php $this->append() ?>


<?php $this->section( 'content' ) ?>
    <div class="row">
        <div class="col-sm-12">

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

        </div>
    </div>




    <div class="row"
        <div class="col-12">

            <div class="w-full max-w-sm mx-auto">

                <?= Former::open()->method( 'POST' )
                    ->action( route( 'forgot-password@username-email' ) )
                    ->class( "bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4" )
                ?>

                <p class="mb-4 text-grey-dark font-bold">
                    Please enter your email address and we will send you any related username(s) by email.
                </p>

                <div class="mb-16">
                    <label class="block text-grey-darker text-sm font-bold mb-2" for="email">
                        Email
                    </label>
                    <input name="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker leading-tight focus:outline-none focus:shadow-outline"
                           id="email" type="text" placeholder="name@example.com" autofocus value="<?= old('email') ?>">
                    <?php foreach( $t->errors->get( 'email' ) as $err ): ?>
                        <p class="text-red text-xs italic mt-2"><?= $err ?></p>
                    <?php endforeach; ?>

                </div>



                <div class="flex items-center justify-between">
                    <a class="inline-block align-baseline font-bold text-sm text-blue-light
                            hover:no-underline  hover:text-blue-dark" href="<?= route( "forgot-password@show-form" ) ?>">
                        Forgot Password?
                    </a>

                    <a class="hover:no-underline bg-transparent hover:bg-blue text-blue-light font-semibold hover:text-blue-dark py-1 px-4 border border-blue hover:border-transparent rounded"
                       href="<?= route('login@login' ) ?>">
                        Cancel
                    </a>

                    <button class="bg-blue hover:bg-blue-dark text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                        Submit
                    </button>
                </div>

            </div>

            <?= Former::close() ?>
        </div>
    </div>



<?php $this->append() ?>