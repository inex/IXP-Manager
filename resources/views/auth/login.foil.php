<?php $this->layout( 'layouts/ixpv4' ) ?>

<?php $this->section( 'page-header-preamble' ) ?>
    Login to <?= config( "identity.sitename" ) ?>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="row">
        <div class="col-12">
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
                    ->action( route( 'login@login' ) )
                    ->class( "tw-bg-white tw-shadow-md tw-rounded-sm tw-px-8 tw-pt-6 tw-pb-8 tw-mb-6" )
                ?>
                <div class="tw-mb-6">
                    <label class="control-label" for="username">
                        Username
                    </label>
                    <input name="username" class="form-control" id="username" type="text" placeholder="Username" autofocus value="<?= $t->ee( old('username') ) ?>">
                    <?php foreach( $t->errors->get( 'username' ) as $err ): ?>
                        <p class="tw-text-red-500 tw-text-xs tw-italic tw-mt-2"><?= $t->ee( $err ) ?></p>
                    <?php endforeach; ?>
                </div>

                <div class="tw-mb-6">
                    <label class="control-label" for="password">
                        Password
                    </label>
                    <input name="password" class="form-control" id="password" type="password" placeholder="...">
                    <?php foreach( $t->errors->get( 'password' ) as $err ): ?>
                        <p class="tw-text-red-500 tw-text-xs tw-italic tw-mt-2"><?= $t->ee( $err ) ?></p>
                    <?php endforeach; ?>
                </div>

                <div class="tw-mb-6">
                    <label class="tw-block tw-text-grey-dark tw-font-bold">
                        <input class="tw-mr-2 tw-leading-tight" type="checkbox" name="remember" id="remember-me" value="1">
                        <span class="tw-text-sm">
                            Remember me
                        </span>
                    </label>
                </div>

                <div class="tw-flex tw-items-center tw-justify-between">
                    <a href="<?= route( "forgot-password@show-form" ) ?>">
                        Forgot Password?
                    </a>

                    <button id="login-btn" class="btn btn-primary" type="submit">
                        Sign In
                    </button>
                </div>

                <?php if( config( 'auth.peeringdb.enabled' ) ): ?>
                    <hr class="tw-my-4">
                    <p class="tw-text-center tw-text-lg tw-italic tw-text-grey-dark">or login with</p>
                    <p class="tw-text-center">
                        <a href="<?= route('auth:login-peeringdb') ?>">
                            <img class="tw-inline" width="60%" src="<?= asset( 'images/pdb-logo-coloured.png' ) ?>">
                        </a>
                    </p>
                <?php endif; ?>

                <?= Former::close() ?>

            </div>
        </div>
    </div>
<?php $this->append() ?>
