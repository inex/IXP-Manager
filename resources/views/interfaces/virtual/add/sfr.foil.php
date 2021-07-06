<?php
// ************************************************************************************************************
// **
// ** The "Sflow Receivers" table on the virtual interface add/edit page.
// **
// ** Not a standalone template - called from interfaces/virtual/add.foil.php
// **
// ************************************************************************************************************
?>
<?php if( config('grapher.backends.sflow.enabled') ):?>
    <div class="row mt-4">
        <h3 class="col-md-12">
            Sflow Receivers
            <a class="btn btn-white btn-sm" href="<?= route('sflow-receiver@create' , [ 'vi' => $t->vi->id ] ) ?>">
                <i class="fa fa-plus"></i>
            </a>
        </h3>

        <div class="col-md-12 table">
            <?php if( $t->vi->sflowReceivers()->count() ): ?>
                <table id="table-sflr" class="table table-striped table-responsive-ixp-no-header">
                    <thead class="thead-dark">
                        <tr>
                            <th>
                                Target IP
                            </th>
                            <th>
                                Target Port
                            </th>
                            <th>
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach( $t->vi->sflowReceivers as $sflr ):
                            /** @var \IXP\Models\SflowReceiver $sflr */ ?>
                            <tr>
                                <td>
                                    <?= $t->ee( $sflr->dst_ip ) ?>
                                </td>
                                <td>
                                    <?= $sflr->dst_port ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a class="btn btn-white" href="<?= route('sflow-receiver@edit-from-virtual-interface' , [ 'sflr' => $sflr->id, 'vi' => $t->vi->id ] ) ?>">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                        <a class="btn btn-white btn-delete-sflr" href="<?= route( 'sflow-receiver@delete', [ 'sflr' => $sflr->id ] ) ?>" title="Delete Sflow Receiver">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                        <?php if( !config( 'ixp_fe.frontend.disabled.logs' ) && method_exists( \IXP\Models\SflowReceiver::class, 'logSubject') ): ?>
                                            <a class="btn btn-white btn-sm" title="View Logs" href="<?= route( 'log@list', [ 'model' => 'SflowReceiver' , 'model_id' => $sflr->id ] ) ?>">
                                                <i class="fa fa-list"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="text-center">
                            <i class="fa fa-question-circle fa-2x"></i>
                        </div>
                        <div class="col-sm-12">
                            There are no sflow receivers defined for this virtual interface.
                            <a class="btn btn-white" href="<?= route('sflow-receiver@create' , [ 'vi' => $t->vi->id ] ) ?>">
                                Create one now...
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; /* sflow receiver functionality enabled */ ?>