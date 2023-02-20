<?php $this->layout( 'layouts/ixpv4' ) ?>

<?php $this->section( 'page-header-preamble' ) ?>
    Password Reset
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
                    ->class( "tw-bg-white tw-shadow-md tw-rounded-sm tw-px-8 tw-pt-6 tw-pb-8 tw-mb-6" )
                ?>

                <p class="tw-mb-6 tw-text-grey-dark tw-font-bold">
                    Please enter your username, the token that was emailed to you and a new password below.
                </p>

                 <div class="tw-mb-6">
                     <label class="control-label" for="username">
                         Username
                     </label>

                    <input name="username" class="form-control" id="username" type="text" placeholder="Username" value="<?= $t->username ? $t->ee( $t->username ) : $t->ee( old('username') ) ?>">
                    <?php foreach( $t->errors->get( 'username' ) as $err ): ?>
                        <p class="tw-text-red-500 tw-text-xs tw-italic tw-mt-2"><?= $t->ee( $err ) ?></p>
                    <?php endforeach; ?>
                </div>

                 <div class="tw-mb-6">
                     <label class="control-label" for="token">
                         Token
                     </label>
                    <input name="token" class="form-control" id="token" type="text" placeholder="" value="<?= $t->token ? $t->ee( $t->token ) : $t->ee( old('token') ) ?>">
                    <?php foreach( $t->errors->get( 'token' ) as $err ): ?>
                        <p class="tw-text-red-500 tw-text-xs tw-italic tw-mt-2"><?= $t->ee( $err ) ?></p>
                    <?php endforeach; ?>
                </div>

                 <div class="tw-mb-6">
                     <label class="control-label" for="password">
                         Password
                     </label>
                    <input name="password" class="form-control" id="password" type="password" autofocus placeholder="******************">
                    <?php foreach( $t->errors->get( 'password' ) as $err ): ?>
                        <p class="tw-text-red-500 tw-text-xs tw-italic tw-mt-2"><?= $t->ee( $err ) ?></p>
                    <?php endforeach; ?>
                 </div>

                 <div class="tw-mb-6">
                     <label class="control-label" for="password_confirmation">
                         Confirm Password
                     </label>
                    <input name="password_confirmation" class="form-control" id="password_confirmation" type="password" placeholder="******************">
                    <?php foreach( $t->errors->get( 'password_confirmation' ) as $err ): ?>
                        <p class="tw-text-red-500 tw-text-xs tw-italic tw-mt-2"><?= $t->ee( $err ) ?></p>
                    <?php endforeach; ?>
                </div>

                <div class="tw-flex tw-items-center tw-justify-between">
                    <a href="<?= route( "login@login" ) ?>">
                        Return to Login
                    </a>
                    <button class="btn btn-primary" type="submit">
                        Reset
                    </button>
                </div>
            </div>
            <?= Former::close() ?>
        </div>
    </div>
<?php $this->append() ?>