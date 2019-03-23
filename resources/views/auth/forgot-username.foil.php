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

            <div class="tw-text-center tw-my-6">
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

            <div class="tw-w-full tw-max-w-sm tw-mx-auto">

                <?= Former::open()->method( 'POST' )
                    ->action( route( 'forgot-password@username-email' ) )
                    ->class( "tw-bg-white tw-shadow-md tw-rounded tw-px-8 tw-pt-6 tw-pb-8 tw-mb-6" )
                ?>

                <p class="tw-mb-6 tw-text-grey-dark tw-font-bold">
                    Please enter your email address and we will send you any related username(s) by email.
                </p>

                <div class="tw-mb-16">
                    <label class="tw-block tw-text-grey-darker tw-text-sm tw-font-bold tw-mb-2" for="email">
                        Email
                    </label>
                    <input name="email" class="tw-shadow-md tw-appearance-none tw-border tw-rounded tw-w-full tw-py-2 tw-px-4 tw-text-grey-darker tw-leading-tight focus:tw-outline-none focus:tw-shadow-outline"
                           id="email" type="text" placeholder="name@example.com" autofocus value="<?= old('email') ?>">
                    <?php foreach( $t->errors->get( 'email' ) as $err ): ?>
                        <p class="tw-text-red tw-text-xs tw-italic tw-mt-2"><?= $err ?></p>
                    <?php endforeach; ?>

                </div>



                <div class="tw-flex tw-items-center tw-justify-between">
                    <a class="tw-inline-block tw-align-baseline tw-font-bold tw-text-sm tw-text-blue-light
                        hover:tw-no-underline  hover:tw-text-blue-dark" href="<?= route( "forgot-password@show-form" ) ?>">
                        Forgot Password?
                    </a>

                    <a class="hover:tw-no-underline tw-bg-transparent hover:tw-bg-blue tw-text-blue-light tw-font-semibold hover:tw-text-blue-dark tw-py-1 tw-px-6 tw-border tw-border-blue-lighter hover:tw-border-transparent tw-rounded"
                       href="<?= route('login@login' ) ?>">
                        Cancel
                    </a>

                    <button class="tw-bg-blue hover:tw-bg-blue-dark tw-text-white tw-font-bold tw-py-2 tw-px-6 tw-rounded focus:tw-outline-none focus:tw-shadow-outline" type="submit">
                        Submit
                    </button>
                </div>

            </div>

            <?= Former::close() ?>
        </div>
    </div>



<?php $this->append() ?>