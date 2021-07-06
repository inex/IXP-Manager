<?php if( config( 'google2fa.enabled' ) ): ?>
    <div class="col-lg-6 col-md-12">
        <h3>
            Two Factor Authentication
        </h3>
        <hr>
        <p>
            <b>IXP Manager</b> supports two factor authentication (2FA) which strengthens access security by requiring two authentication methods
            to verify your identity. Two factor authentication protects against phishing, social
            engineering and password brute force attacks and secures your logins from attackers exploiting weak or stolen credentials.
        </p>

        <p>
            <?php if( !Auth::getUser()->user2FA || !Auth::getUser()->user2FA->enabled ): ?>
                You do not have 2fa enabled. To enable it, click here:
            <?php else: ?>
                You have 2fa enabled. To manage it, click here:
            <?php endif ?>
        </p>

        <div class="form-group col-sm-12">
            <div class="bg-light p-4 mt-4 shadow-sm text-center col-lg-12">
                <a id="configue-2fa" class="btn btn-primary" href="<?= route('2fa@configure')?>">
                    Configure 2FA
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>