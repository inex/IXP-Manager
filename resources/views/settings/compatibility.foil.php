<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
IXP Manager Settings
<?php $this->append() ?>

<?php $this->section('content') ?>
<div class="row">
    <div class="col-12">

        <?= $t->alerts() ?>

        <h3>Welcome to IXP Manager's UI frontend for the <code>.env</code> configuration file.</h3>

        <p>
            You are seeing this page because we have found element(s) in your <code>.env</code> file that are not compatible with the IXP Manager UI.
            Please see the above (first) issue and correct it, and then try again.
        </p>

        <p>
            We also have <a href="https://docs.ixpmanager.org/<?= DOCUMENTATION_VERSION ?>/features/settings/" target="_blank">documentation on the
            supported dotEnv features here</a>.
        </p>



    </div>
</div>
<?php $this->append() ?>
