<?php
    /** @var Foil\Template\Template $t */
    /** @var $t->active */

    $this->layout( 'layouts/ixpv4');
?>

<?php $this->section('title') ?>
    Patch Panels (<?= $t->active ? 'Active' : 'Inactive' ?> Only)
<?php $this->append() ?>

<?php $this->section('page-header-preamble') ?>
    <li class="pull-right">
        <div class="btn-group btn-group-xs" role="group">

            <a class="btn btn-default" href="<?= url('patch-panel/list' . ( $t->active ? '/activeOnly/0' : '' ) ) ?>">
                Show <?= $t->active ? 'Inactive' : 'Active' ?>
            </a>

            <a type="button" class="btn btn-default" href="<?= url('patch-panel/add') ?>">
                <span class="glyphicon glyphicon-plus"></span>
            </a>
        </div>
    </li>
<?php $this->append() ?>


<?php $this->section('content') ?>

    <?php if(session()->has('success')): ?>
        <div class="alert alert-success" role="alert">
            <?= session()->get('success') ?>
        </div>
    <?php endif; ?>
    <?php if(session()->has('error')): ?>
        <div class="alert alert-danger" role="alert">
            <b>Error : </b><?= session()->get('error')['message'] ?>
        </div>
    <?php endif; ?>

    <table id='patch-panel-list' class="table">
        <thead>
            <tr>
                <td>Name</td>
                <td>Cabinet</td>
                <td>Colocation</td>
                <td>Type</td>
                <td>Ports Available</td>
                <td>Installation Date</td>
                <td>Action</td>
            </tr>
        <thead>
        <tbody>
            <?php foreach( $t->patchPanels as $patchPanel ):
                /** @var Entities\PatchPanel $patchPanel */ ?>
                <tr>
                    <td>
                        <a href="<?= url('/patch-panel-port/list/patch-panel' ).'/'.$patchPanel->getId()?>">
                            <?= $patchPanel->getName() ?>
                        </a>

                    </td>
                    <td>
                        <a href="<?= url('/cabinet/view' ).'/'.$patchPanel->getCabinet()->getId()?>">
                            <?= $patchPanel->getCabinet()->getName() ?>
                        </a>
                    </td>
                    <td>
                        <?= $patchPanel->getColoReference() ?>
                    </td>
                    <td>
                        <?= $patchPanel->resolveCableType() ?> / <?= $patchPanel->resolveConnectorType() ?>
                    </td>
                    <td>
                        <?php
                            $available = $patchPanel->getAvailableForUsePortCount();
                            $total     = $patchPanel->getPortCount();

                            if($total != 0):
                                $dAvailable = floor( $available / 2 );
                                $dTotal     = floor( $total     / 2 );

                                if( ($total - $available) / $total < 0.7 ):
                                    $class = "success";
                                elseif( ($total - $available ) / $total < 0.85 ):
                                    $class = "warning";
                                else:
                                    $class = "danger";
                                endif;
                            else:
                                $class = "danger";
                            endif;
                        ?>

                        <span title="" class="label label-<?= $class ?>">
                            <?php if( $patchPanel->hasDuplexPort() ): ?>
                                <?= $dAvailable ?> / <?= $dTotal ?>
                            <?php else: ?>
                                <?= $available ?> / <?= $total ?>
                            <?php endif; ?>
                        </span>

                        <?php if( $patchPanel->hasDuplexPort() ): ?>
                            &nbsp;
                            <span class="label label-info">
                                <?= $available ?> / <?= $total ?>
                            </span>
                        <?php endif; ?>

                    </td>
                    <td>
                        <?= $patchPanel->getInstallationDateFormated() ?>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <a class="btn btn btn-default" href="<?= url('/patch-panel/view' ).'/'.$patchPanel->getId()?>" title="Preview"><i class="glyphicon glyphicon-eye-open"></i></a>
                            <a class="btn btn btn-default" href="<?= url('/patch-panel/edit' ).'/'.$patchPanel->getId()?>" title="Edit"><i class="glyphicon glyphicon-pencil"></i></a>

                            <?php if( $patchPanel->getActive() ): ?>
                                <a class="btn btn btn-default" id='list-delete-<?= $patchPanel->getId() ?>' href="<?= url( 'patch-panel/change-status/' . $patchPanel->getId()
                                        . '/' . ( $patchPanel->getActive() ? '0' : '1' ) ) ?>" title="Make Inactive">
                                    <i class="glyphicon glyphicon-trash"></i>
                                </a>
                            <?php else: ?>
                                <a class="btn btn btn-default" id='list-reactivate-<?= $patchPanel->getId() ?>' href="<?= url( 'patch-panel/change-status/' . $patchPanel->getId()
                                    . '/' . ( $patchPanel->getActive() ? '0' : '1' ) ) ?>" title="Reactive">
                                    <i class="glyphicon glyphicon-repeat"></i>
                                </a>
                            <?php endif; ?>

                            <a class="btn btn btn-default" href="<?= url('/patch-panel-port/list/patch-panel' ).'/'.$patchPanel->getId()?>" title="See Ports"><i class="glyphicon glyphicon-th"></i></a>
                        </div>
                    </td>
                </tr>
            <?php endforeach;?>
        <tbody>
    </table>
<?php $this->append() ?>

<?php $this->section('scripts') ?>
    <script>

        $(document).ready(function(){

            $('#patch-panel-list').dataTable( {
                "autoWidth": false
            } );
        });
    </script>
<?php $this->append() ?>