<?php
/** @var Foil\Template\Template $t */

$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    SAGE Accounting
<?php $this->append() ?>

<?php $this->section('content') ?>

    <div class="row">

        <div class="col-sm-12">

            <p>
                <a href="<?= route('sage/login') ?>">SAGE OAuth Login</a>.
            </p>

            <?php if( isset( $t->user ) ): ?>
                <p>Token: <?= $user->token ?></p>
                <p>Expires: <?= $user->expiresIn ?></p>
                <p>ID: <?= $user->id ?></p>
                <p>Email: <?= $user->email ?></p>
            <?php endif; ?>


        </div>

    </div>

<?php $this->append() ?>

