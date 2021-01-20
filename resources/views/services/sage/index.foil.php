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

            <?php if( $t->ifnot( 'suser', false ) === false ): ?>
                User not set.
            <?php else: ?>
                <p>Token: <?= $t->suser->token ?></p>
                <p>Expires: <?= $t->suser->expiresIn ?></p>
                <p>ID: <?= $t->suser->id ?></p>
                <p>Email: <?= $t->suser->email ?></p>
            <?php endif; ?>


        </div>

    </div>

<?php $this->append() ?>

