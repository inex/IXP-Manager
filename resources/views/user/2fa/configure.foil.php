<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Security / Two Factor Authentication
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">
        <div class="col-sm-12">
            <?= $t->alerts() ?>
            <div class="card">
                <div class="card-header">
                    <h3>
                        Two Factor Authentication
                    </h3>
                </div>
                <div class="card-body">
                    <p>
                        Two factor authentication (2FA) strengthens access security by requiring two authentication methods
                        to verify your identity. Two-factor authentication protects against phishing, social
                        engineering and password brute force attacks and secures your logins from attackers exploiting weak
                        or stolen credentials.
                    </p>
                    <p class="tw-pb-8 tw-border-b-2">
                        <b>IXP Manager</b> supports a Google Authenticator compatible HMAC-Based One-time Password (HOTP)
                        algorithm as specified in <a href="https://tools.ietf.org/html/rfc4226">RFC 4226</a>
                        and the Time-based One-time Password (TOTP) algorithm specified in
                        <a href="https://tools.ietf.org/html/rfc6238">RFC 6238</a>.
                    </p>

                    <?php if( !$t->user->user2FA || !$t->user->user2FA->enabled ): ?>
                        <?= $t->insert( 'user/2fa/configure/enable' ) ?>
                    <?php else: ?>
                        <?= $t->insert( 'user/2fa/configure/manage' ) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php $this->append() ?>