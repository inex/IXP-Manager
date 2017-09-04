<?php

    // ************************************************************************************************************
    // **
    // ** This template describes the add / edit virtual interface page which lists a virtual interface's
    // ** details, its physical and vlan interfaces and any configured sflow receivers.
    // **
    // ** This template is broken up for simplicity with each indepentant element loaded from the add/ directory.
    // **
    // ************************************************************************************************************

    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= action( 'Interfaces\VirtualInterfaceController@list' )?>">(Virtual) Interfaces</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>Add/Edit Virtual Interface</li>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    <li class="pull-right">
        <div class=" btn-group btn-group-xs" role="group">
            <a type="button" class="btn btn-default" href="<?= action( 'Interfaces\VirtualInterfaceController@list' )?>" title="list">
                <span class="glyphicon glyphicon-th-list"></span>
            </a>
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="glyphicon glyphicon-plus"></i> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-right">
                <li>
                    <a id="" href="<?= action( 'Interfaces\VirtualInterfaceController@wizard' )?>" >
                        Add Interface Wizard...
                    </a>
                </li>
                <li>
                    <a id="" href="<?= action( 'Interfaces\VirtualInterfaceController@add' )?>" >
                        Virtual Interface Only...
                    </a>
                </li>
            </ul>
        </div>
    </li>
<?php $this->append() ?>




<?php $this->section('content') ?>

    <?= $t->alerts() ?>

    <?= $t->insert( 'interfaces/virtual/add/vi-details' ) ?>

    <?php if( $t->vi ): ?>

        <?= $t->insert( 'interfaces/virtual/add/pi' ) ?>

        <?php if( !$t->cb ): ?>
            <?= $t->insert( 'interfaces/virtual/add/vli' ) ?>
            <?= $t->insert( 'interfaces/virtual/add/sfr' ) ?>
        <?php endif; ?>

    <?php endif; ?>

<?php $this->append() ?>




<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'interfaces/virtual/js/interface' ); ?>
    <?= $t->insert( 'interfaces/virtual/js/add' ); ?>
<?php $this->append() ?>