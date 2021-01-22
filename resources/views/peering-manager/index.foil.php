<?php
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Peering Manager
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">
        <div class="col-lg-12">
            <?= $t->alerts() ?>

            <?php if( config( 'ixp.peering_manager.testmode', false ) ): ?>
                <div class="alert alert-warning mt-4" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="text-center">
                            <i class="fa fa-exclamation-circle fa-2x"></i>
                        </div>
                        <div class="col-sm-12">
                            <strong>Test mode enabled.</strong>
                            All peering requests will only be sent to <code><?= config( 'ixp.peering_manager.testemail' ) ?></code>.
                            The CC/BCC recipients will be ignored.
                            This can be changed in your <code>.env</code> configuration file.
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="card mt-4">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li id="peering-potential-li" role="potential" class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#potential">
                                Potential Peers
                            </a>
                        </li>
                        <li id="peering-potential-bilat-li" role="potential-bilat" class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#potential-bilat">
                                Potential Bilateral Peers
                            </a>
                        </li>
                        <li id="peering-peers-li" role="peers" class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#peers">
                                Peers
                            </a>
                        </li>
                        <li id="peering-rejected-li" role="rejected" class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#rejected">
                                Rejected / Ignored Peers
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div id="potential" class="tab-pane fade active show">
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
                </div>
            </div>
            <?= $t->insert( 'peering-manager/modal-peering' ); ?>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'peering-manager/js/index' ); ?>
<?php $this->append() ?>