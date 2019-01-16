<?php $this->layout( 'layouts/ixpv4' ) ?>

<?php $this->section( 'title' ) ?>
    <a href="<?= route ( 'patch-panel-port/list' )?>">Patch Panel Port</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>
        Edit&nbsp;&nbsp;[<?= $t->ee( $t->ppp->getPatchPanel()->getName() ) ?> - <?= $t->ee( $t->ppp->getName() ) ?>]
    </li>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    <li class="pull-right">
        <div class="btn-group btn-group-xs" role="group">
            <a type="button" class="btn btn-default" href="<?= route('patch-panel-port/list/patch-panel' ,  [ 'id' => $t->ppp->getPAtchPanel()->getId() ]  ) ?>" title="list">
                <span class="glyphicon glyphicon-th-list"></span>
            </a>
        </div>
    </li>
<?php $this->append() ?>


<?php $this->section( 'content' ) ?>

<?php if (!$t->allocating and  !$t->prewired): ?>
    <div class="alert alert-warning" role="alert">
        <b>Note:</b>
        IXP Manager provides context-aware actions for allocating / setting connected / requested ceases / ceasing a patch
        panel port and these <i>do the right thing</i>. As such, editing a patch panel port manually through this
        interface is discouraged for lifecycle changes.
    </div>
<?php endif; ?>

<?= $t->alerts() ?>

<?= Former::open()->method( 'POST' )
    ->action( route( 'patch-panel-port@store' ) )
    ->customInputWidthClass( 'col-sm-3' )
    ->addClass( 'col-md-10' );
?>

    <?php if (!$t->allocating and  !$t->prewired): ?>
        <?= Former::text( 'number' )
            ->label( 'Patch Panel Port Name' )
            ->forceValue( $t->ee( $t->ppp->getName() ) );

        ?>

        <?= Former::text( 'patch_panel' )
            ->label( 'Patch Panel' );

        ?>

        <?= Former::text( 'cabinet_name' )
            ->label( 'Rack' )
            ->disabled( 'disable' );
        ?>


        <?= Former::text( 'colocation_centre' )
            ->label( 'Colocation Centre' )
            ->disabled( 'disable' );
        ?>
    <?php endif; ?>

    <?= Former::text( 'description' )
            ->label( 'Description' )
            ->blockHelp( 'A one line short description to be shown in the list of patch panel ports. '
                . 'Just enough to help explain the ports purpose. Detailed information should be '
                . 'placed in the notes below. Can also be used to explain a reserved / broken / '
                . 'other port. Note that this is parsed as Markdown.<br><br>'
                . '<b>NB: A description is discouraged for customer ports connected to a switch. '
                . 'The customer name and switch port <em>are the description</em></b>.' );
    ?>

    <?php if (!$t->prewired): ?>
        <?= Former::text( 'colo_circuit_ref' )
            ->label( 'Colocation Circuit Reference' )
            ->blockHelp( 'The cross connect reference as provided by the colocation provider.' );
        ?>

        <?= Former::text( 'colo_billing_ref' )
            ->label( 'Colocation Billing Reference' )
            ->blockHelp( 'The cross connect billing reference as provided by the colocation provider.' );
        ?>

        <?= Former::text( 'ticket_ref' )
            ->label( 'Ticket Reference(s)' )
            ->blockHelp( 'This is a free text field to allow you to add helpdesk ticket reference(s) that deal with your member for this connection.' );
        ?>
    <?php endif; ?>

    <?= Former::checkbox( 'duplex' )
        ->label( 'Duplex connection?' )
        ->value( 1 )
        ->blockHelp('Typically fibre connections are <em>duplex connections</em> in that they use two ports. If this is the '
            . 'case, check this and select the partner port. <em>Duplex ports should generally start with an odd number and '
            . 'have an even numbered partner port (assuming port numbering starts from 1).</em>' );
    ?>

    <span id='duplex-port-area' style="display: none">
        <?= Former::select( 'partner_port' )
            ->label( 'Partner Port' )
            ->fromQuery( $t->partnerPorts, 'name' )
            ->placeholder( 'Choose a partner port' )
            ->addClass( 'chzn-select' )
            ->blockHelp( 'The second half of the duplex port.' );
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
        <?php if( $t->ppp ) : ?><?php if( $t->ppp->getSwitchPort() ) : ?><div  class="label label-info" style="margin-left: 17%"> Was  <?= $t->ppp->getSwitchPort()->getSwitcher()->getName() ?> </div> <br/><br/><?php endif; ?><?php endif; ?>


        <?= Former::select( 'switch_port' )
            ->label( 'Switch Port' )
            ->fromQuery( $t->switchPorts, 'name' )
            ->placeholder( 'Choose a switch port' )
            ->addClass( 'chzn-select' );
        ?>
        <?php if( $t->ppp ) : ?><?php if( $t->ppp->getSwitchPort() ) : ?><span class="label label-info" style="margin-left: 17%"> Was  <?= $t->ppp->getSwitchPort()->getName() ?> </span> <?php endif; ?><?php endif; ?>
    </div>

    <?php if ( !$t->prewired ): ?>
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

            <?php if( $t->ppp ) : ?><?php if( $t->ppp->getCustomer() ) : ?><span class="label label-info" style="margin-left: 17%"> Was  <?= $t->ppp->getCustomer()->getName() ?> </span> <?php endif; ?><?php endif; ?>
        </div>
    <?php endif; ?>

    <?= Former::select( 'state' )
        ->label( 'Patch Panel Port Status' )
        ->options( $t->states )
        ->placeholder( 'Choose a states' )
        ->addClass( 'chzn-select' )
        ->blockHelp( 'The state of the patch panel port.' )
        ->disabled( $t->prewired ? true : false );
    ?>

    <?php if( $t->allocating ): ?>
        <span id='pi_status_area' style="display: none">
            <?= Former::select( 'pi_status' )
                ->label( 'Physical Interface Status' )
                ->options( Entities\PhysicalInterface::$STATES )
                ->placeholder( 'Choose a status' )
                ->addClass( 'chzn-select' )
                ->blockHelp( 'This allows you to update the physical interface status when updating the patch panel port status. '
                    . '<b>The current state is shown by default.</b>' );
            ?>
        </span>
    <?php endif; ?>

    <?php if ( !$t->prewired ): ?>

        <div class="form-group">

            <label for="notes" class="control-label col-lg-2 col-sm-4">Public Notes</label>
            <div class="col-sm-8">

                <ul class="nav nav-tabs">
                    <li role="presentation" class="active">
                        <a class="tab-link-body-note" href="#body1">Notes</a>
                    </li>
                    <li role="presentation">
                        <a class="tab-link-preview-note" href="#preview1">Preview</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="body1">
                        <textarea class="form-control" style="font-family:monospace;" rows="20" id="notes" name="notes"><?= $t->notes ?></textarea>
                        <p class="help-block">These notes are visible (but not editable) to the member. You can use markdown here.</p>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="preview1">
                        <div class="well well-preview" style="background: rgb(255,255,255);">
                            Loading...
                        </div>
                    </div>
                </div>

                <br><br>
            </div>

        </div>

    <?php endif; ?>

        <div class="form-group">

            <label for="private_notes" class="control-label col-lg-2 col-sm-4">Private Notes</label>
            <div class="col-sm-8">

                <ul class="nav nav-tabs">
                    <li role="presentation" class="active">
                        <a class="tab-link-body-note" href="#body2">Notes</a>
                    </li>
                    <li role="presentation">
                        <a class="tab-link-preview-note" href="#preview2">Preview</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="body2">
                        <textarea class="form-control" style="font-family:monospace;" rows="20" id="private_notes" name="private_notes"><?= $t->private_notes ?></textarea>
                        <p class="help-block">These notes are <b>NOT</b> visible to the member. You can use markdown here.</p>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="preview2">
                        <div class="well well-preview" style="background: rgb(255,255,255);">
                            Loading...
                        </div>
                    </div>
                </div>

                <br><br>
            </div>

        </div>

    <?php if ( !$t->prewired ): ?>
        <?php if( !$t->allocating ): ?>
            <?= Former::date( 'assigned_at' )
                ->label( 'Assigned At' )
                ->append( '<button class="btn-default btn" onclick="setToday( \'assigned_at\' )" type="button">Today</button>' )
                ->blockHelp( 'help text' )
                ->value( date( 'Y-m-d' ) );
            ?>

            <?= Former::date( 'connected_at' )
                ->label( 'Connected At' )
                ->append( '<button class="btn-default btn" onclick="setToday( \'connected_at\' )" type="button">Today</button>' )
                ->blockHelp( 'help text' );
            ?>

            <?= Former::date( 'ceased_requested_at' )
                ->label( 'Ceased Requested At' )
                ->append( '<button class="btn-default btn" onclick="setToday( \'ceased_requested_at\' )" type="button">Today</button>' )
                ->blockHelp( 'help text' );
            ?>

            <?= Former::date( 'ceased_at' )
                ->label( 'Ceased At' )
                ->append( '<button class="btn-default btn" onclick="setToday( \'ceased_at\' )" type="button"">Today</button>' )
                ->blockHelp( 'help text' );
            ?>

            <?= Former::text( 'last_state_change_at' )
                ->label( 'Last State change At' )
                ->blockHelp( 'help text' )
                ->disabled( true );
            ?>
        <?php endif; ?>

        <?= Former::select( 'chargeable' )
            ->label( 'Chargeable' )
            ->options( $t->chargeables )
            ->addClass( 'chzn-select' )
            ->blockHelp( 'Usually IXPs request their members to <em>come to them</em> and bear the costs of that. '
                . 'However, sometimes a co-location facility may charge the IXP for a half circuit or the IXP may need '
                . 'order and pay for the connection. This can be used, for example, to reconcile billing.' );
        ?>

        <?= Former::radios( 'internal_use' )
            ->radios([
                'Yes' => ['name' => 'internal_use', 'value' => '1'],
                'No' => ['name' => 'internal_use', 'value' => '0'],
            ])->inline()->check( $t->ppp->getInternalUse() ? '1' : '0' )
            ->blockHelp( 'Indicates that this cross connect is for IXP use rather than relating to a member.' );
        ?>

        <?= Former::select( 'owned_by' )
            ->label( 'Owned By' )
            ->options( $t->ownedBy )
            ->addClass( 'chzn-select' )
            ->blockHelp( 'Indicates who order the cross connect / who the contracting entity is.' );
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

    <?php if ( $t->prewired ): ?>
        <?= Former::hidden( 'state' )
            ->id( 'state' )
            ->forceValue( \Entities\PatchPanelPort::STATE_PREWIRED )
        ?>
    <?php endif; ?>

    <?=Former::actions( Former::primary_submit( 'Save Changes' ),
        Former::default_link( 'Cancel' )->href( route ( 'patch-panel-port/list/patch-panel' , [ 'id' => $t->ppp->getPatchPanel()->getId() ] ) ),
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