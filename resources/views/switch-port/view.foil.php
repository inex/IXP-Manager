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
        <a id="d2f-list-a" class="btn btn-white" href="<?= route($t->feParams->route_prefix.'@list') ?>">
            <span class="fa fa-th-list"></span>
        </a>
        <?php if( !isset( $t->feParams->readonly ) || !$t->feParams->readonly ): ?>
            <a class="btn btn-white" href="<?= route($t->feParams->route_prefix.'@edit' , [ 'id' => $t->data[ 'item' ][ 'id' ] ]) ?>">
                <span class="fa fa-pencil"></span>
            </a>
            <a class="btn btn-white" href="<?= route($t->feParams->route_prefix.'@add') ?>">
                <span class="fa fa-plus"></span>
            </a>
        <?php endif; ?>
    </div>

<?php $this->append() ?>



<?php $this->section('content') ?>

    <?php

        // some customer content for switch port
        /** @var \Entities\SwitchPort $sp */
        $sp = D2EM::getRepository( \Entities\SwitchPort::class )->find( $t->data[ 'item' ]['id'] );
    ?>



        <?= $t->alerts() ?>

        <?= $t->data[ 'view' ]['viewPreamble'] ? $t->insert( $t->data[ 'view' ]['viewPreamble'] ) : '' ?>

            <div class="card">
                <div class="card-header">
                    <b>Details for switch port: <?= $sp->getSwitcher()->getName() ?> :: <?= $sp->getName() ?></b>
                    (DB ID: <?= $sp->getId() ?>)
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
                                            <?= $sp->getId() ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <b>Name</b>
                                        </td>
                                        <td>
                                            <?= $t->ee( $sp->getName() ) ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <b>Switch</b>
                                        </td>
                                        <td>
                                            <a href="<?= route( 'switch@view', [ 'id' => $sp->getSwitcher()->getId() ] ) ?>">
                                                <?= $t->ee( $sp->getSwitcher()->getName() ) ?>
                                            </a>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <b>Type</b>
                                        </td>
                                        <td>
                                            <?= $sp->resolveType() ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <b>Active</b>
                                        </td>
                                        <td>
                                            <?= $sp->getActive() ? 'Yes' : 'No' ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b>MAU Type</b>
                                        </td>
                                        <td>
                                            <?= $sp->getMauType() ?? '(not supported / unknown)' ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b>MAU State</b>
                                        </td>
                                        <td>
                                            <?= $sp->getMauState() ?? '(not supported / unknown)' ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b>MAU Availability</b>
                                        </td>
                                        <td>
                                            <?= $sp->getMauAvailability() ?? '(not supported / unknown)' ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b>MAU Jacktype</b>
                                        </td>
                                        <td>
                                            <?= $sp->getMauJacktype() ?? '(not supported / unknown)' ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b>MAU Autoneg Supported?</b>
                                        </td>
                                        <td>
                                            <?= $sp->getMauAutoNegSupported() === null ? '(not supported / unknown)' : ( $sp->getMauAutoNegSupported() ? 'Yes' : 'No' ) ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b>MAU Authneg Admin State</b>
                                        </td>
                                        <td>
                                            <?= $sp->getMauAutoNegAdminState() === null ? '(not supported / unknown)' : ( $sp->getMauAutoNegAdminState() ? 'Enabled' : 'No' ) ?>
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
                                            <?= $sp->getIfIndex() ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <b>ifName</b>
                                        </td>
                                        <td>
                                            <?= $sp->getIfName() ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <b>ifAlias</b>
                                        </td>
                                        <td>
                                            <?= $sp->getIfAlias() ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <b>ifHighSpeed</b>
                                        </td>
                                        <td>
                                            <?= $sp->getIfHighSpeed() ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <b>iMtu</b>
                                        </td>
                                        <td>
                                            <?= $sp->getIfMtu() ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <b>ifPhysAddress</b>
                                        </td>
                                        <td>
                                            <?= $sp->getIfPhysAddress() ?><
                                            /td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <b>ifAdminStatus</b>
                                        </td>
                                        <td>
                                            <?= $sp->getIfAdminStatus() === null ? '(not supported / unknown)' : \OSS_SNMP\MIBS\Iface::$IF_ADMIN_STATES[ $sp->getIfAdminStatus() ] ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <b>ifOperStatus</b>
                                        </td>
                                        <td>
                                            <?= $sp->getIfOperStatus() === null ? '(not supported / unknown)' : \OSS_SNMP\MIBS\Iface::$IF_OPER_STATES[ $sp->getIfOperStatus() ] ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <b>ifLastChange</b>
                                        </td>
                                        <td>
                                            <?= \Carbon\Carbon::createFromTimestamp( $sp->getIfLastChange() )->format( 'Y-m-d H:m:s' ) ?> <sup>*</sup>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <b>lastSnmpPoll</b>
                                        </td>
                                        <td>
                                            <?= $sp->getLastSnmpPoll() ? $sp->getLastSnmpPoll()->format( 'Y-m-d H:i:s' ) : '(not yet polled)' ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <b>lagIfIndex</b>
                                        </td>
                                        <td>
                                            <?= $sp->getLagIfIndex() ?>
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

                                <?php if( $sp->getPhysicalInterface() ): ?>

                                    <b>
                                        This port is in use by

                                        <a href="<?= route( 'customer@overview', [ 'id' => $sp->getPhysicalInterface()->getVirtualInterface()->getCustomer()->getId() ] ) ?>">
                                            <?= $sp->getPhysicalInterface()->getVirtualInterface()->getCustomer()->getName() ?></a>.

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