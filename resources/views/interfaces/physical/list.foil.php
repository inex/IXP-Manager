<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Physical Interfaces / List
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">
        <div class="col-sm-12">
            <?= $t->alerts() ?>
            <table id='table' class="collapse table table-striped w-100">
                <thead class="thead-dark">
                    <tr>
                        <th>
                            <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?>
                        </th>
                        <th>
                            Facility
                        </th>
                        <th>
                            Switch
                        </th>
                        <th>
                            Port
                        </th>
                        <th>
                            Status
                        </th>
                        <th>
                            Speed
                        </th>
                        <th>
                            Raw Speed
                        </th>
                        <th>
                            Rate Limit
                        </th>
                        <th>
                            Raw Rate Limit
                        </th>
                        <th>
                            Auto-neg
                        </th>
                        <th>
                            Action
                        </th>
                    </tr>
                <thead>
                <tbody>
                    <?php foreach( $t->pis as $pi ): ?>
                        <tr>
                            <td>
                                <a href="<?= route( "customer@overview" , [ 'cust' => $pi['custid'] ] ) ?>">
                                    <?= $t->ee( $pi['customer'] )   ?>
                                </a>
                            </td>
                            <td>
                                <a href="<?= route( 'facility@view', [ 'id' => $pi['locid'] ] ) ?>">
                                    <?= $t->ee(  $pi['location'] )   ?>
                                </a>
                            </td>
                            <td>
                                <a href="<?= route( 'switch@view', [ "id" => $pi['switchid'] ] ) ?>">
                                    <?= $t->ee(  $pi['switch'] )   ?>
                                </a>
                            </td>
                            <td>
                                <?= $t->ee( $pi['port'] )   ?>
                            </td>
                            <td>
                                <?= \IXP\Models\PhysicalInterface::$STATES[ $pi[ 'status' ] ] ?? 'Unknown' ?>
                            </td>
                            <td>
                                <?= $t->scaleBits( $pi['speed'] * 1000 * 1000, 0 ) ?>
                            </td>
                            <td>
                                <?= $pi['speed'] ?? 0  ?>
                            </td>
                            <td>
                                <?= $pi['rate_limit'] ? $t->scaleSpeed( $pi['rate_limit'] ) : '' ?>
                            </td>
                            <td>
                                <?= $pi['rate_limit'] ?? 0 ?>
                            </td>
                            <td>
                                <?= $pi['autoneg'] ? 'Yes' : 'No'?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a class="btn btn-white" href="<?= route( 'physical-interface@view' , [ 'pi' => $pi['id'] ] ) ?>" title="View">
                                        <i class="fa fa-eye"></i>
                                    </a>

                                    <a class="btn btn-white" href="<?= route( 'virtual-interface@edit' , [ 'vi' => $pi['vintid'] ] ) ?>" title="Virtual Interface">
                                        <i class="fa fa-filter"></i>
                                    </a>

                                    <a class="btn btn-white" href="<?= route ( 'physical-interface@edit', [ 'pi' => $pi['id'] ] ) ?>" title="Edit">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <a class="btn btn-white btn-delete" <?php if( $t->resellerMode() && ( $pi['ppid'] || $pi['fpid'] ) ) :?> data-related="1" <?php endif; ?> data-type="<?= $pi['type'] ?>" href="<?= route( 'physical-interface@delete', [ 'pi' => $pi[ 'id' ] ] ) ?>" title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach;?>
                <tbody>
            </table>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $this->insert( 'interfaces/physical/js/list' ) ?>
<?php $this->append() ?>