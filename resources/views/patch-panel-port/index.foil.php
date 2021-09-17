<?php $this->layout( 'layouts/ixpv4' );
    /** @var object $t */
    /** @var \IXP\Models\PatchPanel $pp */
    $pp = $t->pp;
    $isSuperUser = Auth::getUser()->isSuperUser();
    $ppHasDuplex = $pp ? $pp->hasDuplexPort() : false;
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Patch Panel Port
    <?php if( $t->pp ): ?>
        - <?= $t->ee( $pp->name ) ?>
    <?php endif;?>
    <?= isset( $t->data()['summary'] ) ? ' :: ' . $t->summary : '' ?>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm" role="group">
        <a class="btn btn-white" href="<?= \Illuminate\Support\Facades\Request::url() ?>" title="Refresh">
            <span class="fa fa-refresh"></span>
        </a>
        <?php if( $t->pp ): ?>
            <a class="btn btn-white" href="<?= route('patch-panel@edit' , [ 'pp' => $pp->id ] ) ?>" title="Edit Patch Panel">
                <span class="fa fa-pencil"></span>
            </a>
            <a class="btn btn-white" href="<?= route('patch-panel@view' , [ 'pp' => $pp->id ] ) ?>" title="View Patch Panel">
                <span class="fa fa-eye"></span>
            </a>
        <?php endif;?>
    </div>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="row">
        <div class="col-sm-12">
            <?php if( $t->pp ): ?>
                <div>
                    <h2>
                        Ports for <?= $t->ee( $t->pp->name ) ?>
                        <?php if( $pp->colo_reference !== $pp->name ): ?>
                            (Colo Ref: <?= $t->ee( $pp->colo_reference ) ?>)
                        <?php endif; ?>
                        <small>
                            <?php $cabinet = $pp->cabinet ?>
                            <?= $t->ee( $cabinet->name ) ?>, <?= $t->ee( $cabinet->location->name ) ?>
                            [<?= $pp->cableType() ?>/<?= $pp->connectorType() ?>]
                        </small>
                    </h2>
                </div>
            <?php endif;?>

            <?= $t->alerts() ?>

            <span id="message-ppp"></span>

            <table id='table-ppp' class="collapse table table-striped" width="100%">
                <thead class="thead-dark">
                    <tr>
                        <th>
                            Id
                        </th>
                        <th>
                            Name
                        </th>
                        <?php if( !$pp ): ?>
                            <th>
                                Patch Panel
                            </th>
                        <?php endif;?>
                        <th>
                            Description / Switch / Port
                        </th>
                        <th>
                            <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?>
                        </th>
                        <th>
                            Colocation Ref
                        </th>
                        <th>
                            Flags
                        </th>
                        <th>
                            Assigned at
                        </th>
                        <th>
                            State
                        </th>
                        <th>
                            Action
                        </th>
                    </tr>
                <thead>
                <tbody>
                    <?php
                        $lastUsedNumber = 0;
                        foreach( $t->patchPanelPorts as $ppp ):
                            /** @var \IXP\Models\PatchPanelPort $ppp */
                            $potentialSlave = false; //$t->pp && $t->pp->hasDuplexPort() && !( $ppp->getNumber() % 2 ) && $ppp->isAvailableForUse();
                            ?>
                            <tr <?= $potentialSlave ? 'class="potential-slave" style="display: none;"' : '' ?>>
                                <td>
                                    <?= $ppp->id ?>
                                </td>
                                <td>
                                    <a href="<?= route( 'patch-panel-port@view' , [ 'ppp' => $ppp->id ] ) ?> ">
                                        <?php
                                            $num = floor( $ppp->number / 2 ) + ( $ppp->number % 2 );
                                            $name = $ppp->prefix . $ppp->number;
                                            $slaveName = '';
                                            if( $ppp->nbslave > 0 ) {
                                                $slaveName = $ppp->prefix . $ppp->slavenumber;
                                                $name .=  '/' . $slaveName .  ' (' . ( $ppp->number % 2 ? ( floor( $ppp->number / 2 ) ) + 1 : $ppp->number / 2 ) . ')';
                                            }
                                            if( $pp && $ppHasDuplex && ! ( $ppp->duplex_master_id !== null || $ppp->nbslave > 0 ) ){
                                                echo $name . ' <span class="potential-slave">(' . $num . ')</span>';
                                            } else {
                                                echo $name;
                                            }
                                            $lastUsedNumber = $num;
                                        ?>
                                    </a>
                                </td>
                                <?php if(!$pp): ?>
                                    <td>
                                        <a href="<?= route( 'patch-panel-port@list-for-patch-panel' , [ 'pp' => $ppp->patch_panel_id ] ) ?>">
                                            <?= $t->ee( $ppp->ppname ) ?>
                                        </a>
                                    </td>
                                <?php endif; ?>
                                <td>
                                    <?php if( trim( $ppp->description ) !== '' ): ?>
                                        <?= @parsedown( $t->ee( $ppp->description ) ) ?>
                                        <?= $ppp->switch_port_id ? "<br>" : "" ?>
                                    <?php endif; ?>
                                    <?php if( $ppp->switch_port_id ): ?>
                                        <?= $t->ee( $ppp->sname ) ?> :: <?= $t->ee( $ppp->spname ) ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if( $ppp->customer_id ): ?>
                                        <a href="<?= route( "customer@overview" , [ 'cust' => $ppp->customer_id ] ) ?>">
                                            <?= $t->ee( $ppp->cname ) ?>
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $t->ee( $ppp->colo_circuit_ref ) ?>
                                </td>
                                <td>
                                    <!-- FLAGS -->
                                    <?php if( $ppp->internal_use ): ?>
                                        <span class="badge badge-secondary" data-toggle="tooltip" title="Internal Use">INT</span>
                                    <?php endif; ?>

                                    <?php if( isset( \IXP\Models\PatchPanelPort::$CHARGEABLES[ $ppp->chargeable ] ) && $ppp->chargeable !== \IXP\Models\PatchPanelPort::CHARGEABLE_NO ): ?>
                                        <span class="badge badge-secondary" data-toggle="tooltip" title="<?= \IXP\Models\PatchPanelPort::$CHARGEABLES[ $ppp->chargeable ] ?? 'Unknown' ?>"><?= env( 'CURRENCY_HTML_ENTITY', '&euro;' ) ?></span>
                                    <?php endif; ?>

                                    <?php if( $ppp->files > 0 ): ?>
                                        <span class="badge badge-secondary" data-toggle="tooltip" title="Files">F</span>
                                    <?php endif; ?>

                                    <?php if( trim( $ppp->notes ) !== '' ): ?>
                                        <span class="badge badge-secondary" data-toggle="tooltip" title="Public Note">N+</span>
                                    <?php endif; ?>

                                    <?php if( trim( $ppp->private_notes ) !== '' ): ?>
                                        <span class="badge badge-secondary" data-toggle="tooltip" title="Private Note">N-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $ppp->assigned_at ?>
                                </td>
                                <td>
                                    <span title="" class="badge badge-<?= \IXP\Models\PatchPanelPort::stateCssClass( $ppp->state, $isSuperUser ) ?>">
                                        <?= \IXP\Models\PatchPanelPort::$STATES[ $ppp->state ] ?>
                                    </span>
                                </td>
                                <td width="200px">
                                    <div class="btn-group btn-group-sm my-auto" role="group">

                                        <?= $t->insert( 'patch-panel-port/action-dd', [ 'ppp' => $ppp,
                                             'btnClass' => 'btn-group-sm', 'tpl' => 'index',
                                             'prefix' => $ppp->prefix, 'nbSlave' => $ppp->nbslave,
                                             'slaveName' => $slaveName, 'isSuperUser' => $isSuperUser ] );
                                        ?>

                                        <a class="btn btn-white" title="History"
                                                href="<?= route( 'patch-panel-port@view' , [ 'ppp' => $ppp->id ] ) ?>  ">
                                            <div class="d-flex mt-1">
                                                <i class="fa fa-folder-open"></i>
                                                <span class="badge badge-dark ml-1"><?= $ppp->histories ?></span>
                                            </div>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach;?>
                </tbody>
            </table>
            <?= $t->insert( 'patch-panel-port/modal' ); ?>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'patch-panel-port/js/index' ); ?>
    <?= $t->insert( 'patch-panel-port/js/action-dd' ); ?>
<?php $this->append() ?>