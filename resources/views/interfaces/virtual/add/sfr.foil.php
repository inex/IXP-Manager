<?php

// ************************************************************************************************************
// **
// ** The "Sflow Receivers" table on the virtual interface add/edit page.
// **
// ** Not a standalone template - called from interfaces/virtual/add.foil.php
// **
// ************************************************************************************************************

?>

<?php
    // only show this section if the functionality is enabled:
    if( config('grapher.backends.sflow.enabled') ):
?>

<div class="row mt-4">

    <h3 class="col-md-12">
        Sflow Receivers
        <a class="btn btn-outline-secondary btn-sm" href="<?= route('interfaces/sflow-receiver/add' , ['id' => 0 , 'viid' => $t->vi->getId() ] ) ?>">
            <i class="fa fa-plus"></i>
        </a>
    </h3>

    <div class="col-md-12" id="message-sflr"></div>

    <div class="col-md-12 table-responsive" id="area-sflr">

        <?php if( count( $t->vi->getSflowReceivers() ) ): ?>

            <table id="table-sflr" class="table table-striped ">

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

                    <?php foreach( $t->vi->getSflowReceivers() as $sflr ): /** @var Entities\SflowReceiver $sflr */ ?>

                        <tr>

                            <td>
                                <?= $t->ee( $sflr->getDstIp() ) ?>
                            </td>

                            <td>
                                <?= $sflr->getDstPort() ?>
                            </td>

                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a class="btn btn-outline-secondary" href="<?= route('interfaces/sflow-receiver/edit/from-virtual-interface' , [ 'id' => $sflr->getId(), 'viid' => $t->vi->getId() ] ) ?>">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <a class="btn btn-outline-secondary" id="delete-sflr-<?= $sflr->getId()?>" href="" title="Delete Sflow Receiver">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </div>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        <?php else: ?>

            <div id="table-sflr" class="alert alert-info" role="alert">
                <span class="fa fa-info-circle" aria-hidden="true"></span>
                <span class="sr-only">Information :</span>
                There are no Sflow receivers defined for this virtual interface.
                <a href="<?= route('interfaces/sflow-receiver/add' , ['id' => 0 , 'viid' => $t->vi->getId() ] ) ?>">
                    Add one now...
                </a>
            </div>

        <?php endif; ?>

    </div>
</div>

<?php endif; /* sflow receiver functionality enabled */ ?>

