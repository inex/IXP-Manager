<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Core Bundle / List
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class=" btn-group btn-group-sm" role="group">
        <a target="_blank" class="btn btn-white" href="https://docs.ixpmanager.org/features/core-bundles/">
            Documentation
        </a>
        <a id="add-cb-wizard" type="button" class="btn btn-white" href="<?= route( 'core-bundle@create-wizard' )?>">
            <i class="fa fa-plus"></i>
        </a>
    </div>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">
        <div class="col-sm-12">
            <?= $t->alerts() ?>
            <table id='table-cb' class="table collapse table-striped w-100">
                <thead class="thead-dark">
                    <tr>
                        <th>
                            Description
                        </th>
                        <th>
                            Type
                        </th>
                        <th>
                            Enabled
                        </th>
                        <th>
                            Switch A
                        </th>
                        <th>
                            Switch B
                        </th>
                        <th>
                            Capacity
                        </th>
                        <th>
                            Action
                        </th>
                    </tr>
                <thead>
                <tbody>
                    <?php foreach( $t->cbs as $cb ):
                        /** @var \IXP\Models\CoreBundle $cb */
                        $clsNb      = $cb->coreLinks->count();
                        $piSpeed    = $cb->speedPi();
                        ?>
                        <tr>
                            <td>
                                <?= $t->ee( $cb->description ) ?>
                            </td>
                            <td>
                                <?= $t->ee( $cb->typeText() )  ?>
                            </td>
                            <td>
                                <?php if( !$cb->enabled ):?>
                                    <i class="fa fa-remove"></i>
                                <?php elseif( $cb->enabled && $cb->allCoreLinksEnabled() ): ?>
                                    <i class="fa fa-check"></i>
                                <?php else:?>
                                    <span class="badge badge-warning">
                                        <?= $cb->coreLinks()->active()->count() ?> / <?= $clsNb ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= $t->ee( $cb->switchSideX( true )->name )  ?>
                            </td>
                            <td>
                                <?= $t->ee( $cb->switchSideX( false )->name )  ?>
                            </td>
                            <td data-sort="<?= $clsNb * $piSpeed ?>" >
                                <?= $t->scaleBits(  $clsNb * $piSpeed * 1000000, 0 )  ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a id="edit-cb-<?=  $cb->id ?>" class="btn btn-white" href="<?= route( 'core-bundle@edit' , [ 'cb' => $cb->id ] ) ?>" title="Edit">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach;?>
                <tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-10 offset-sm-1">
            <p class="tw-italic">
                <br><br>
                <sup>*</sup> Operational means the number of enabled core links for which both sides had a SNMP IFace operational state of 'up' the last time the switch
                was polled (typically every 5 mins).
            </p>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'interfaces/core-bundle/js/list' ); ?>
<?php $this->append() ?>