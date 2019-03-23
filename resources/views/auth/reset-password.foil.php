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

            <div class="tw-text-center tw-my-6">
                <?php if( config( "identity.biglogo" ) ) :?>
                    <img class="tw-inline img-fluid" src="<?= config( "identity.biglogo" ) ?>" />
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
                    ->action( route( 'reset-password@reset' ) )
                    ->class( "tw-bg-white tw-shadow-md tw-rounded tw-px-8 tw-pt-6 tw-pb-8 tw-mb-6" )
                ?>


                <p class="tw-mb-6 tw-text-grey-dark tw-font-bold">
                    Please enter your username, the token that was emailed to you and a new password below.
                </p>

                 <div class="tw-mb-6">
                    <label class="tw-block tw-text-grey-darker tw-text-sm tw-font-bold tw-mb-2" for="username">
                        Username
                    </label>
                    <input name="username" class="tw-shadow-md tw-appearance-none tw-border tw-rounded tw-w-full tw-py-2 tw-px-4 tw-text-grey-darker tw-leading-tight focus:tw-outline-none focus:tw-shadow-outline"
                           id="username" type="text" placeholder="Username" value="<?= $t->username ?? old('username') ?>">
                    <?php foreach( $t->errors->get( 'username' ) as $err ): ?>
                        <p class="tw-text-red-500 tw-text-xs tw-italic tw-mt-2"><?= $err ?></p>
                    <?php endforeach; ?>
                </div>

                 <div class="tw-mb-6">
                    <label class="tw-block tw-text-grey-darker tw-text-sm tw-font-bold tw-mb-2" for="token">
                        Token
                    </label>
                    <input name="token" class="tw-shadow-md tw-appearance-none tw-border tw-rounded tw-w-full tw-py-2 tw-px-4 tw-text-grey-darker tw-leading-tight focus:tw-outline-none focus:tw-shadow-outline"
                           id="token" type="text" placeholder="" value="<?= $t->token ?? old('token') ?>">
                    <?php foreach( $t->errors->get( 'token' ) as $err ): ?>
                        <p class="tw-text-red-500 tw-text-xs tw-italic tw-mt-2"><?= $err ?></p>
                    <?php endforeach; ?>

                </div>


                 <div class="tw-mb-6">
                    <label class="tw-block tw-text-grey-darker tw-text-sm tw-font-bold tw-mb-2" for="password">
                        Password
                    </label>
                    <input name="password" class="tw-shadow-md tw-appearance-none tw-border tw-rounded tw-w-full tw-py-2 tw-px-4 tw-text-grey-darker tw-leading-tight focus:tw-outline-none focus:tw-shadow-outline"
                            id="password" type="password" autofocus placeholder="******************">
                    <?php foreach( $t->errors->get( 'password' ) as $err ): ?>
                        <p class="tw-text-red-500 tw-text-xs tw-italic tw-mt-2"><?= $err ?></p>
                    <?php endforeach; ?>
                </div>

                 <div class="tw-mb-6">
                    <label class="tw-block tw-text-grey-darker tw-text-sm tw-font-bold tw-mb-2" for="password_confirmation">
                        Confirm Password
                    </label>
                    <input name="password_confirmation" class="tw-shadow-md tw-appearance-none tw-border tw-rounded tw-w-full tw-py-2 tw-px-4 tw-text-grey-darker tw-leading-tight focus:tw-outline-none focus:tw-shadow-outline" id="password_confirmation" type="password" placeholder="******************">
                    <?php foreach( $t->errors->get( 'password_confirmation' ) as $err ): ?>
                        <p class="tw-text-red-500 tw-text-xs tw-italic tw-mt-2"><?= $err ?></p>
                    <?php endforeach; ?>
                </div>


                <div class="tw-flex tw-items-center tw-justify-between">
                    <a class="tw-inline-block tw-align-baseline tw-font-bold tw-text-sm tw-text-blue-light
                        hover:tw-no-underline  hover:tw-text-blue-dark" href="<?= route( "login@login" ) ?>">
                        Return to Login
                    </a>
                    <button class="tw-bg-blue-500 hover:tw-bg-blue-500-dark tw-text-white tw-font-bold tw-py-2 tw-px-6 tw-rounded focus:tw-outline-none focus:tw-shadow-outline" type="submit">
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