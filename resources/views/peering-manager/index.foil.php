<?php
/** @var Foil\Template\Template $t */

$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    Peering Manager
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>

<?php $this->append() ?>


<?php $this->section('content') ?>
<div class="row">

    <div class="col-sm-12">

        <?= $t->alerts() ?>



        <?php if( config( 'ixp.peering_manager.testmode', false ) ): ?>

            <div class="alert alert-warning">
                <strong>Test mode enabled.</strong>
                All peering requests will only be sent to <code><?= config( 'ixp.peering_manager.testemail' ) ?></code>.
                The CC/BCC recipients will be ignored.
                This can be changed in your <code>.env</code> configuration file.
            </div>

        <?php endif; ?>
        
        
        
        <ul class="nav nav-tabs">

            <li id="peering-potential-li" role="potential" class="active">
                <a data-toggle="tab" href="#potential">Potential Peers</a>
            </li>

            <li id="peering-potential-bilat-li" role="potential-bilat">
                <a data-toggle="tab" href="#potential-bilat">Potential Bilateral Peers</a>
            </li>

            <li id="peering-peers-li" role="peers">
                <a data-toggle="tab" href="#peers">Peers</a>
            </li>

            <li id="peering-rejected-li" role="rejected">
                <a data-toggle="tab" href="#rejected">Rejected / Ignored Peers</a>
            </li>

        </ul>


        <div class="tab-content">

            <div id="potential" class="tab-pane fade in active">
                <?= $t->insert( 'peering-manager/tabs/potential' ); ?>
            </div>

            <div id="potential-bilat" class="tab-pane fade">
                <?= $t->insert( 'peering-manager/tabs/potential-bilat' ); ?>
            </div>

            <div id="peers" class="tab-pane fade">
                <?= $t->insert( 'peering-manager/tabs/peers' ); ?>
            </div>

            <div id="rejected" class="tab-pane fade">
                <?= $t->insert( 'peering-manager/tabs/rejected' ); ?>
            </div>

        </div>

        <?= $t->insert( 'peering-manager/modal-peering' ); ?>

    </div>
</div>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'peering-manager/js/index' ); ?>
<?php $this->append() ?>
