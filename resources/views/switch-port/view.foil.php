<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    <?=  $t->feParams->pagetitle  ?>
    /
    View <?=  $t->feParams->titleSingular  ?>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm" role="group">
        <a id="e2f-list-a" class="btn btn-white" href="<?= route($t->feParams->route_prefix.'@list') ?>">
            <span class="fa fa-th-list"></span>
        </a>
        <?php if( !isset( $t->feParams->readonly ) || !$t->feParams->readonly ): ?>
            <a class="btn btn-white" href="<?= route($t->feParams->route_prefix.'@edit' , [ 'id' => $t->data[ 'item' ][ 'id' ] ]) ?>">
                <span class="fa fa-pencil"></span>
            </a>
            <a class="btn btn-white" href="<?= route($t->feParams->route_prefix.'@create') ?>">
                <span class="fa fa-plus"></span>
            </a>
        <?php endif; ?>
    </div>
<?php $this->append() ?>

<?php $this->section('content') ?>

    <?= $t->data[ 'view' ]['viewPreamble'] ? $t->insert( $t->data[ 'view' ]['viewPreamble'] ) : '' ?>

    <div class="card">
        <div class="card-header">
            <b>Details for switch port: <?= $t->data[ 'item' ]['switchname'] ?> :: <?= $t->data[ 'item' ]['name'] ?></b>
            (DB ID: <?= $t->data[ 'item' ]['id'] ?>)
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-lg-6 col-md-12">
                    <table class="table_view_info">
                        <tbody>
                            <tr>
                                <td>
                                    <b>DB ID</b>
                                </td>
                                <td>
                                    <?= $t->data[ 'item' ]['id'] ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>Name</b>
                                </td>
                                <td>
                                    <?= $t->ee( $t->data[ 'item' ]['name'] ) ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>Switch</b>
                                </td>
                                <td>
                                    <a href="<?= route( 'switch@view', [ 'id' => $t->data[ 'item' ]['switchid'] ] ) ?>">
                                        <?= $t->ee( $t->data[ 'item' ]['switchname'] ) ?>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>Type</b>
                                </td>
                                <td>
                                    <?= \IXP\Models\SwitchPort::$TYPES[ $t->data[ 'item' ][ 'type' ] ] ?>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <b>Active</b>
                                </td>
                                <td>
                                    <?= $t->data[ 'item' ][ 'active' ] ? 'Yes' : 'No' ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>MAU Type</b>
                                </td>
                                <td>
                                    <?= $t->data[ 'item' ][ 'mauType' ] ?? '(not supported / unknown)' ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>MAU State</b>
                                </td>
                                <td>
                                    <?= $t->data[ 'item' ][ 'mauState' ] ?? '(not supported / unknown)' ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>MAU Availability</b>
                                </td>
                                <td>
                                    <?= $t->data[ 'item' ][ 'mauAvailability' ] ?? '(not supported / unknown)' ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>MAU Jacktype</b>
                                </td>
                                <td>
                                    <?= $t->data[ 'item' ][ 'mauJacktype' ] ?? '(not supported / unknown)' ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>MAU Autoneg Supported?</b>
                                </td>
                                <td>
                                    <?= $t->data[ 'item' ][ 'mauAutoNegSupported' ] === null ? '(not supported / unknown)' : ( $t->data[ 'item' ][ 'mauAutoNegSupported' ] ? 'Yes' : 'No' ) ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>MAU Authneg Admin State</b>
                                </td>
                                <td>
                                    <?= $t->data[ 'item' ][ 'mauAutoNegAdminState' ] === null ? '(not supported / unknown)' : ( $t->data[ 'item' ][ 'mauAutoNegAdminState' ] ? 'Enabled' : 'No' ) ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Created:
                                    </b>
                                </td>
                                <td>
                                    <?= $t->data[ 'item' ][ 'created_at' ] ? \Carbon\Carbon::create( $t->data[ 'item' ][ 'created_at' ] ) : ''?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Updated:
                                    </b>
                                </td>
                                <td>
                                    <?= $t->data[ 'item' ][ 'updated_at' ] ? \Carbon\Carbon::create( $t->data[ 'item' ][ 'updated_at' ] ) : '' ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="col-lg-6 col-md-12">
                    <table class="table_view_info">
                        <tbody>
                            <tr>
                                <td>
                                    <b>ifIndex</b>
                                </td>
                                <td>
                                    <?= $t->data[ 'item' ][ 'ifIndex' ] ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>ifName</b>
                                </td>
                                <td>
                                    <?= $t->ee( $t->data[ 'item' ][ 'ifName' ] ) ?>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <b>ifAlias</b>
                                </td>
                                <td>
                                    <?= $t->ee( $t->data[ 'item' ][ 'ifAlias' ] ) ?>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <b>ifHighSpeed</b>
                                </td>
                                <td>
                                    <?= $t->data[ 'item' ][ 'ifHighSpeed' ]?>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <b>iMtu</b>
                                </td>
                                <td>
                                    <?= $t->data[ 'item' ][ 'ifMtu' ] ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>ifPhysAddress</b>
                                </td>
                                <td>
                                    <?= $t->ee( $t->data[ 'item' ][ 'ifPhysAddress' ] ) ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>ifAdminStatus</b>
                                </td>
                                <td>
                                    <?= $t->data[ 'item' ][ 'ifAdminStatus' ] === null ? '(not supported / unknown)' : \OSS_SNMP\MIBS\Iface::$IF_ADMIN_STATES[ $t->data[ 'item' ][ 'ifAdminStatus' ] ] ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>ifOperStatus</b>
                                </td>
                                <td>
                                    <?= $t->data[ 'item' ][ 'ifOperStatus' ] === null ? '(not supported / unknown)' : \OSS_SNMP\MIBS\Iface::$IF_OPER_STATES[ $t->data[ 'item' ][ 'ifOperStatus' ] ] ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>ifLastChange</b>
                                </td>
                                <td>
                                    <?= $t->data[ 'item' ][ 'ifLastChange' ] ? \Carbon\Carbon::createFromTimestamp( $t->data[ 'item' ][ 'ifLastChange' ] )->format( 'Y-m-d H:m:s' ) : 'Never' ?> <sup>*</sup>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>lastSnmpPoll</b>
                                </td>
                                <td>
                                    <?= $t->data[ 'item' ][ 'ifLastChange' ] ?? '(not yet polled)' ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>lagIfIndex</b>
                                </td>
                                <td>
                                    <?= $t->data[ 'item' ][ 'lagIfIndex' ] ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p>
                        <em><sup>*</sup> as of last switch poll</em>
                    </p>
                </div>
            </div>

            <div class="alert alert-info mt-4" role="alert">
                <div class="d-flex align-items-center">
                    <div class="mr-4 text-center">
                        <i class="fa fa-question-circle fa-2x"></i>
                    </div>
                    <div>
                        <?php if( $t->data[ 'item' ][ 'cid' ] ): ?>
                            <b>
                                This port is in use by
                                <a href="<?= route( 'customer@overview', [ 'cust' => $t->data[ 'item' ][ 'cid' ] ] ) ?>">
                                    <?= $t->data[ 'item' ][ 'cname' ] ?></a>.
                            </b>
                        <?php else: ?>
                            <b>
                                This port is not currently in use by an IXP customer.
                            </b>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?= $t->data[ 'view' ]['viewPostamble'] ? $t->insert( $t->data[ 'view' ]['viewPostamble'] ) : '' ?>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->data[ 'view' ][ 'viewScript' ] ? $t->insert( $t->data[ 'view' ][ 'viewScript' ] ) : '' ?>
<?php $this->append() ?>