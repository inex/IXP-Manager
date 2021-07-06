<?php
    // ************************************************************************************************************
    // **
    // ** This template describes the create / edit virtual interface page which lists a virtual interface's
    // ** details, its physical and vlan interfaces and any configured sflow receivers.
    // **
    // ** This template is broken up for simplicity with each indepentant element loaded from the add/ directory.
    // **
    // ************************************************************************************************************
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    (Virtual) Interfaces /
    <?= $t->vi ? 'Edit' : 'Create' ?>
    Virtual Interface
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class=" btn-group btn-group-sm" role="group">
        <a class="btn btn-white" href="<?= route( 'virtual-interface@list' )?>" title="list">
            <i class="fa fa-th-list"></i>
        </a>
        <button type="button" class="btn btn-white dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-plus"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="<?= route( 'virtual-interface@wizard' )?>" >
                Create Interface Wizard...
            </a>
            <a class="dropdown-item" href="<?= route( 'virtual-interface@create' )?>" >
                Virtual Interface Only...
            </a>
        </ul>

        <?php if( $t->vi && !config( 'ixp_fe.frontend.disabled.logs' ) && method_exists( \IXP\Models\VirtualInterface::class, 'logSubject') ): ?>
            <a class="btn btn-white btn-sm" href="<?= route( 'log@list', [ 'model' => 'VirtualInterface' , 'model_id' => $t->vi->id ] ) ?>">
                View logs
            </a>
        <?php endif; ?>
    </div>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">
        <div class="col-lg-12">
            <div id="instructions-alert" class="alert alert-info mt-4 collapse" role="alert">
                <div class="d-flex align-items-center">
                    <div class="text-center">
                        <i class="fa fa-question-circle fa-2x"></i>
                    </div>
                    <div class="col-sm-12">
                        <b>Instructions: </b> You are strongly advised to review <a href="http://docs.ixpmanager.org/usage/interfaces/">the documentation</a> before adding / editing interfaces
                        on a production system.
                    </div>
                </div>
            </div>
            <?= $t->alerts() ?>

            <?= $t->insert( 'interfaces/virtual/add/vi-details' ) ?>

            <?php if( $t->vi ): ?>
                <?= $t->insert( 'interfaces/virtual/add/pi' ) ?>

                <?php if( !$t->cb ): ?>
                    <?= $t->insert( 'interfaces/virtual/add/vli' ) ?>
                    <?= $t->insert( 'interfaces/virtual/add/sfr' ) ?>
                <?php endif; ?>

            <?php endif; ?>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'interfaces/virtual/js/interface' ); ?>
    <?= $t->insert( 'interfaces/virtual/js/add' ); ?>
<?php $this->append() ?>