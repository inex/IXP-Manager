<?php
/** @var Foil\Template\Template $t */
/** @var Entities\PhysicalInterface $pi */
$pi = $t->pi; // for convenience
$this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Physical Interfaces / View
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>

    <div class="btn-group btn-group-sm" role="group">
        <a class="btn btn-outline-secondary" href="<?= route( 'interfaces/physical/list' ) ?>" title="list">
            <span class="fa fa-th-list"></span>
        </a>
        <a class="btn btn-outline-secondary" href="<?= route('interfaces/physical/edit' , [ 'id' => $pi->getId() ]) ?>" title="edit">
            <span class="fa fa-pencil"></span>
        </a>
    </div>

<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
<div class="row">

    <div class="col-lg-12">

        <div class="card">
            <div class="card-header">
                <h3>
                    Informations
                </h3>
            </div>
            <div class="card-body">

                <table class="table_view_info">
                    <tr>
                        <td>
                            <b>
                                Customer:
                            </b>
                        </td>
                        <td>
                            <?= $t->ee( $pi->getVirtualInterface()->getCustomer()->getName() ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Facility:
                            </b>
                        </td>
                        <td>
                            <a href="<?= route( 'facility@view', ['id' => $pi->getSwitchPort()->getSwitcher()->getCabinet()->getLocation()->getId() ] ) ?> ">
                                <?= $t->ee(  $pi->getSwitchPort()->getSwitcher()->getCabinet()->getLocation()->getName() )?>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Switch:
                            </b>
                        </td>
                        <td>
                            <?= $t->ee(  $pi->getSwitchPort()->getSwitcher()->getName() ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Port:
                            </b>
                        </td>
                        <td>
                            <?= $t->ee(  $pi->getSwitchPort()->getName() ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Status:
                            </b>
                        </td>
                        <td>
                            <?= $pi->resolveStatus()  ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Speed:
                            </b>
                        </td>
                        <td>
                            <?= $pi->resolveSpeed() ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Duplex:
                            </b>
                        </td>
                        <td>
                            <?= ucfirst( $pi->getDuplex() ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Auto-Neg:
                            </b>
                        </td>
                        <td>
                            <?= $pi->getAutoneg() ? 'Yes' : 'No' ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Notes:
                            </b>
                        </td>
                        <td>
                            <?= @parsedown( $pi->getNotes() ) ?>
                        </td>
                    </tr>
                </table>

            </div>
        </div>

    </div>

</div>

<?php $this->append() ?>