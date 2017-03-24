<?php $this->layout( 'layouts/ixpv4' ) ?>

<?php $this->section( 'title' ) ?>
    <a href="<?= url( 'patch-panel-port/list' )?>">Patch Panel Port</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>Edit&nbsp;&nbsp;&nbsp; [<?= $t->ppp->getPatchPanel()->getName() ?> - <?= $t->ppp->getName() ?>]</li>
<?php $this->append() ?>


<?php $this->section( 'content' ) ?>

<?php if (!$t->allocating and  !$t->prewired): ?>
    <div class="alert alert-warning" role="alert">
        <b>Warning!</b>
        IXP Manager provides context-aware actions for allocating / setting connected / requested ceases / ceasing a patch
        panel port and these <i>do the right thing</i>. As such, editing a patch panel port manually through this
        interface is strongly discouraged unless you know what you are doing.
    </div>
<?php endif; ?>

<?= $t->alerts() ?>

<?= Former::open()->method( 'POST' )
    ->action( url('patch-panel-port/store' ) )
    ->customWidthClass( 'col-sm-3' )
    ->addClass( 'col-md-10' );
?>

    <?php if (!$t->allocating and  !$t->prewired): ?>
        <?= Former::text( 'number' )
            ->label( 'Patch Panel Port Name' );
        ?>

        <?= Former::text( 'patch_panel' )
            ->label( 'Patch Panel' );
        ?>
    <?php endif; ?>

    <?php if (!$t->prewired): ?>
        <?= Former::text( 'colo_circuit_ref' )
            ->label( 'Colocation Circuit Reference' )
            ->help( 'The cross connect reference as provided by the colocation provider.' );
        ?>

        <?= Former::text( 'ticket_ref' )
            ->label( 'Ticket Reference(s)' )
            ->help( 'This is a free text field to allow you to add helpdesk ticket reference(s) that deal with your member for this connection.' );
        ?>
    <?php endif; ?>

    <?= Former::checkbox( 'duplex' )
        ->label( 'Duplex connection?' )
        ->help('Typically fibre connections are <em>duplex connections</em> in that they use two ports. If this is the '
            . 'case, check this and select the partner port. <em>Duplex ports should generally start with an odd number and '
            . 'have an even numbered partner port (assuming port numbering starts from 1).</em>' );
    ?>

    <span id='duplex-port-area' style="display: none">
        <?= Former::select( 'partner_port' )
            ->label( 'Partner Port' )
            ->fromQuery( $t->partnerPorts, 'name' )
            ->placeholder( 'Choose a partner port' )
            ->addClass( 'chzn-select' )
            ->help( 'The second half of the duplex port.' );
        ?>
    </span>

    <div class="well help-block">
        You have a number of options when assigning a port:

        <ul>
            <li>
                If you have pre-wired the patch panel to a port, enter the switch and port here. So long as no customer has been
                assigned to the switch port, the patch panel port will remain available but will be marked as connected to
                the given switch port in the patch panel port list.
            </li>
            <li>
                If the switch port has been allocated to a customer, then this patch panel port will also be allocated to that customer.
                The backend logic will detect if this is the case and update the customer field. Conversely, if you chose a customer
                first, the switch / switch port dropdowns will be populated with only that customer's assigned ports.
            </li>
            <li>
                Sometimes you will get cross connects that are not intended to be connected to peering switches (e.g. connections to
                co-located customer equipment, IXP metro connections, etc.). In these cases, just select the customer (and if it's the IXP
                itself, select the IXP customer) and leave switch / switch port unselected.
            </li>
        </ul>

        If you need to reset these fields, just click either of the <em>Reset</em> button.
    </div>

    <div class="well">
        <?= Former::default_button( 'Reset' )
            ->addClass( 'reset-button-well reset-btn' )
            ->icon( 'glyphicon glyphicon-refresh' )
            ->title( 'Reset' )
            ->style( 'margin-top : 1%' )
            ->id( 'resetSwitchSelect' );
        ?>

        <?= Former::select( 'switch' )
            ->label( 'Switch' )
            ->fromQuery( $t->switches, 'name' )
            ->placeholder( 'Choose a switch' )
            ->addClass( 'chzn-select' );
        ?>

        <?= Former::select( 'switch_port' )
            ->label( 'Switch Port' )
            ->fromQuery( $t->switchPorts, 'name' )
            ->placeholder( 'Choose a switch port' )
            ->addClass( 'chzn-select' );
        ?>
    </div>

    <?php if (!$t->prewired): ?>
        <div class="well">
            <?= Former::default_button( 'Reset' )
                ->addClass( 'reset-button-well reset-btn' )
                ->icon( 'glyphicon glyphicon-refresh' )
                ->title( 'Reset' )
                ->id( 'resetCustomer' );
            ?>

            <?= Former::select( 'customer' )
                ->label( 'Customer' )
                ->fromQuery( $t->customers, 'name' )
                ->placeholder( 'Choose a customer' )
                ->addClass( 'chzn-select' );
            ?>
        </div>
    <?php endif; ?>

    <?= Former::select( 'state' )
        ->label( 'Patch Panel Port Status' )
        ->options( $t->states )
        ->placeholder( 'Choose a states' )
        ->addClass( 'chzn-select' )
        ->help( 'The state of the patch panel port.' );
    ?>

    <?php if( $t->allocating ): ?>
        <span id='pi_status_area' style="display: none">
            <?= Former::select( 'pi_status' )
                ->label( 'Physical Interface Status' )
                ->options( $t->piStatus )
                ->placeholder( 'Choose a status' )
                ->addClass( 'chzn-select' )
                ->help( 'This allows you to update the physical interface status when updating the patch panel port status.' );
            ?>
        </span>
    <?php endif; ?>

    <?php if (!$t->prewired): ?>
        <?= Former::textarea( 'notes' )
            ->label( 'Public Notes' )
            ->rows( 10 )
            ->style( 'width:500px' )
            ->help( 'These notes are visible (but not editable) to the member. You can use markdown here.' );
        ?>
    <?php endif; ?>
    <?= Former::textarea( 'private_notes' )
        ->label( 'Privates Notes' )
        ->rows( 10 )
        ->style( 'width:500px' )
        ->help( 'These notes are <b>NOT</b> visible to the member. You can use markdown here.' );
    ?>

    <?php if (!$t->prewired): ?>
        <?php if( !$t->allocating ): ?>
            <?= Former::date( 'assigned_at' )
                ->label( 'Assigned At' )
                ->append( '<button class="btn-default btn" onclick="setToday( \'assigned_at\' )" type="button">Today</button>' )
                ->help( 'help text' )
                ->value( date( 'Y-m-d' ) );
            ?>

            <?= Former::date( 'connected_at' )
                ->label( 'Connected At' )
                ->append( '<button class="btn-default btn" onclick="setToday( \'connected_at\' )" type="button">Today</button>' )
                ->help( 'help text' );
            ?>

            <?= Former::date( 'ceased_requested_at' )
                ->label( 'Ceased Requested At' )
                ->append( '<button class="btn-default btn" onclick="setToday( \'ceased_requested_at\' )" type="button">Today</button>' )
                ->help( 'help text' );
            ?>

            <?= Former::date( 'ceased_at' )
                ->label( 'Ceased At' )
                ->append( '<button class="btn-default btn" onclick="setToday( \'ceased_at\' )" type="button"">Today</button>' )
                ->help( 'help text' );
            ?>

            <?= Former::text( 'last_state_change_at' )
                ->label( 'Last State change At' )
                ->help( 'help text' );
            ?>
        <?php endif; ?>

        <?= Former::select( 'chargeable' )
            ->label( 'Chargeable' )
            ->options( $t->chargeables )
            ->addClass( 'chzn-select' )
            ->help( 'Usually IXPs request their members to <em>come to them</em> and bear the costs of that. '
                . 'However, sometimes a co-location facility may charge the IXP for a half circuit or the IXP may need '
                . 'order and pay for the connection. This can be used, for example, to reconcile billing.' );
        ?>

        <?= Former::radios( 'internal_use' )
            ->radios([
                'Yes' => ['name' => 'internal_use', 'value' => '1'],
                'No' => ['name' => 'internal_use', 'value' => '0'],
            ])->inline()->check($t->ppp->getInternalUseInt())
            ->help( 'Indicates that this cross connect is for IXP use rather than relating to a member.' );
        ?>

        <?= Former::select( 'owned_by' )
            ->label( 'Owned By' )
            ->options( $t->ownedBy )
            ->addClass( 'chzn-select' )
            ->help( 'Indicates who order the cross connect / who the contracting entity is.' );
        ?>
    <?php endif; ?>
    <?= Former::hidden( 'patch_panel_port_id' )
        ->value( $t->ppp->getId() )
    ?>

    <?= Former::hidden( 'allocated' )
        ->id( 'allocated' )
        ->value( $t->allocating )
    ?>

    <?= Former::hidden( 'prewired' )
        ->id( 'prewired' )
        ->value( $t->prewired )
    ?>

    <?= Former::hidden( 'switch_port_id' )
        ->id( 'switch_port_id' )
        ->value( $t->ppp->getSwitchPortId() )
    ?>

    <?= Former::hidden( 'patch_panel_id' )
        ->id( 'patch_panel_id' )
        ->value( $t->ppp->getPatchPanel()->getId() )
    ?>

    <?=Former::actions( Former::primary_submit( 'Save Changes' ),
        Former::default_link( 'Cancel' )->href( url( 'patch-panel-port/list/patch-panel/'.$t->ppp->getPatchPanel()->getId() ) ),
        Former::success_button( 'Help' )->id( 'help-btn' )
    );?>

    <?= Former::hidden( 'date' )
        ->id( 'date' )
        ->value( date( 'Y-m-d' ) )
    ?>

    <?= Former::hidden( 'id' )
        ->value( $t->ppp ? $t->ppp->getId() : '' )
    ?>

<?= Former::close() ?>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'patch-panel-port/js/edit' ); ?>
<?php $this->append() ?>