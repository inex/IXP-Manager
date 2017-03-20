<?php $this->layout('layouts/ixpv4') ?>

<?php $this->section('title') ?>
    <a href="<?= url('patch-panel-port/list')?>">Patch Panel Port</a>
<?php $this->append() ?>

<?php $this->section('page-header-postamble') ?>
    <li>Edit</li>
<?php $this->append() ?>


<?php $this->section('content') ?>

<?php if(!$t->allocating): ?>
    <div class="alert alert-warning" role="alert">
        <b>Warning!</b>
        IXP Manager provides context-aware actions for allocating / setting connected / requested ceases / ceasing a patch
        panel port and these <i>do the right thing</i>. As such, editing a patch panel port manually throught this
        interface is stringly discouraged unless you know what you are doing.
    </div>
<?php endif; ?>

<?= $t->alerts() ?>

<?= Former::open()->method('POST')
    ->action(url('patch-panel-port/store'))
    ->customWidthClass('col-sm-3')
    ->addClass('col-md-10');
?>

    <?php if(!$t->allocating): ?>
        <?= Former::text('number')
            ->label('Patch Panel Port Name')
            ->help('help text');
        ?>

        <?= Former::text('patch_panel')
            ->label('Patch Panel')
            ->help('help text');
        ?>
    <?php endif; ?>

    <?= Former::text('colo_circuit_ref')
        ->label('Colocation Circuit Reference')
        ->help('help text');
    ?>

    <?= Former::text('ticket_ref')
        ->label('Ticket Reference(s)')
        ->help('help text');
    ?>

    <?= Former::checkbox('duplex')?>

    <span id='duplex-port-area' style="display: none">
        <?= Former::select('partner_port')
            ->label('Partner Port')
            ->fromQuery($t->partnerPorts, 'name')
            ->placeholder('Choose a partner port')
            ->addClass('chzn-select')
            ->help('help text');
        ?>
    </span>

    <div class="well">
        <?= Former::default_button('Reset')
            ->addClass('reset-button-well reset-btn')
            ->icon('glyphicon glyphicon-refresh')
            ->title('Reset')
            ->style('margin-top : 1%')
            ->id('resetSwitchSelect');
        ?>

        <?= Former::select('switch')
            ->label('Switch')
            ->fromQuery($t->switches, 'name')
            ->placeholder('Choose a switch')
            ->addClass('chzn-select')
            ->help('help text');
        ?>

        <?= Former::select('switch_port')
            ->label('Switch Port')
            ->fromQuery($t->switchPorts, 'name')
            ->placeholder('Choose a switch port')
            ->addClass('chzn-select')
            ->help('help text');
        ?>
    </div>

    <div class="well">
        <?= Former::default_button('Reset')
            ->addClass('reset-button-well reset-btn')
            ->icon('glyphicon glyphicon-refresh')
            ->title('Reset')
            ->id('resetCustomer');
        ?>

        <?= Former::select('customer')
            ->label('Customer')
            ->fromQuery($t->customers, 'name')
            ->placeholder('Choose a customer')
            ->addClass('chzn-select')
            ->help('help text');
        ?>
    </div>

    <?= Former::select('state')
        ->label('States')
        ->options($t->states)
        ->placeholder('Choose a states')
        ->addClass('chzn-select')
        ->help('help text');
    ?>

    <?php if($t->allocating): ?>
        <span id='pi_status_area' style="display: none">
            <?= Former::select('pi_status')
                ->label('Physical Interface status')
                ->options($t->piStatus)
                ->placeholder('Choose a status')
                ->addClass('chzn-select')
                ->help('help text');
            ?>
        </span>
    <?php endif; ?>

    <?= Former::textarea('notes')
        ->label('Public Notes')
        ->rows(10)
        ->style('width:500px')
        ->help('help text');
    ?>

    <?= Former::textarea('private_notes')
        ->label('Privates Notes')
        ->rows(10)
        ->style('width:500px')
        ->help('help text');
    ?>

    <?php if(!$t->allocating): ?>
        <?= Former::date('assigned_at')
            ->label('Assigned At')
            ->append('<button class="btn-default btn" onclick="setToday(\'assigned_at\')" type="button">Today</button>')
            ->help('help text')
            ->value(date('Y-m-d'));
        ?>

        <?= Former::date('connected_at')
            ->label('Connected At')
            ->append('<button class="btn-default btn" onclick="setToday(\'connected_at\')" type="button">Today</button>')
            ->help('help text');
        ?>

        <?= Former::date('ceased_requested_at')
            ->label('Ceased Requested At')
            ->append('<button class="btn-default btn" onclick="setToday(\'ceased_requested_at\')" type="button">Today</button>')
            ->help('help text');
        ?>

        <?= Former::date('ceased_at')
            ->label('Ceased At')
            ->append('<button class="btn-default btn" onclick="setToday(\'ceased_at\')" type="button"">Today</button>')
            ->help('help text');
        ?>

        <?= Former::text('last_state_change_at')
            ->label('Last State change At')
            ->help('help text');
        ?>
    <?php endif; ?>

    <?= Former::select('chargeable')
        ->label('Chargeable')
        ->options($t->chargeables)
        ->select($t->ppp->getChargeableDefaultNo())
        ->addClass('chzn-select')
        ->help('help text');
    ?>

    <?= Former::radios('internal_use')
        ->radios(array(
            'Yes' => array('name' => 'internal_use', 'value' => '1'),
            'No' => array('name' => 'internal_use', 'value' => '0'),
        ))->inline()->check($t->ppp->getInternalUseInt())
        ->help('help text');
    ?>

    <?= Former::select('owned_by')
        ->label('Owned By')
        ->options($t->ownedBy)
        ->addClass('chzn-select')
        ->help('help text');
    ?>

    <?= Former::hidden('patch_panel_port_id')
        ->value($t->ppp->getId())
    ?>

    <?= Former::hidden('allocated')
        ->value($t->allocating)
    ?>

    <?= Former::hidden('switch_port_id')
        ->id('switch_port_id')
        ->value($t->ppp->getSwitchPortId())
    ?>

    <?= Former::hidden('patch_panel_id')
        ->id('patch_panel_id')
        ->value($t->ppp->getPatchPanel()->getId())
    ?>

    <?=Former::actions( Former::primary_submit('Save Changes'),
        Former::default_link('Cancel')->href(url('patch-panel-port/list/patch-panel/'.$t->ppp->getPatchPanel()->getId())),
        Former::success_button('Help')->id('help-btn')
    );?>

    <?= Former::hidden('date')
        ->id('date')
        ->value(date('Y-m-d'))
    ?>

    <?= Former::hidden( 'id' )
        ->value( $t->ppp ? $t->ppp->getId() : '' )
    ?>

<?= Former::close() ?>


<?php $this->append() ?>


<?php $this->section('scripts') ?>
    <?= $t->insert( 'patch-panel-port/js/edit' ); ?>
<?php $this->append() ?>