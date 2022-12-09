<?php $this->layout( 'layouts/ixpv4' ) ?>

<?php $this->section( 'page-header-preamble' ) ?>
    Forget Password
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

    <div class="row">
        <div class="col-12">
            <div class="tw-w-full tw-max-w-sm tw-mx-auto">
                <?= Former::open()->method( 'POST' )
                    ->action( route( 'forgot-password@reset-email' ) )
                    ->class( "tw-bg-white tw-shadow-md tw-rounded-sm tw-px-8 tw-pt-6 tw-pb-8 tw-mb-6" )
                ?>

                <p class="tw-mb-6 tw-text-grey-dark tw-font-bold">
                    Please enter your username and we will send you a password reset token by email.
                </p>

                <div class="tw-mb-16">
                    <label class="control-label" for="username">
                        Username
                    </label>
                    <input name="username" class="form-control" id="username" type="text" placeholder="Username" autofocus value="<?= $t->ee( old('username') ) ?>">
                    <?php foreach( $t->errors->get( 'username' ) as $err ): ?>
                        <p class="tw-text-red-500 tw-text-xs tw-italic tw-mt-2"><?= $t->ee( $err ) ?></p>
                    <?php endforeach; ?>
                </div>

                <div class="tw-flex tw-items-center tw-justify-between">
                    <a href="<?= route( "forgot-password@showUsernameForm" ) ?>">
                        Forgot Username?
                    </a>

                    <a class="btn btn-white" href="<?= route('login@login' ) ?>">
                        Cancel
                    </a>

                    <button class="btn btn-primary" type="submit">
                        Submit
                    </button>
                </div>
            </div>
            <?= Former::close() ?>
        </div>
    </div>
<?php $this->append() ?>