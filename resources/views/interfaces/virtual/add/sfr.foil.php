
<?php if( config('grapher.backends.sflow.enabled') ) : ?>
<div class="row-fluid">
    <h3>
        Sflow Receivers
        <a class="btn btn-default btn-xs" href="<?= route('interfaces/sflow-receiver/add' , ['id' => 0 , 'viid' => $t->vi->getId() ] ) ?>"><i class="glyphicon glyphicon-plus"></i></a>
    </h3>
    <div id="message-sflr"></div>
    <div id="area-sflr">
        <?php if( count( $t->vi->getSflowReceivers() ) ) : ?>
            <table id="table-sflr" class="table table-bordered">
                <tr>
                    <th>
                        Target IP
                    </th>
                    <th>
                        Target Port
                    </th>
                    <th>
                        Action
                    </th>
                </tr>
                <?php foreach( $t->vi->getSflowReceivers() as $sflr ):
                    /** @var Entities\SflowReceiver $sflr */ ?>
                    <tr>
                        <td>
                            <?= $t->ee( $sflr->getDstIp() ) ?>
                        </td>
                        <td>
                            <?= $sflr->getDstPort() ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a class="btn btn btn-default" href="<?= route('interfaces/sflow-receiver/edit/from-virtual-interface' , [ 'id' => $sflr->getId(), 'viid' => $t->vi->getId() ] ) ?>">
                                    <i class="glyphicon glyphicon-pencil"></i>
                                </a>
                                <a class="btn btn btn-default" id="delete-sflr-<?= $sflr->getId()?>">
                                    <i class="glyphicon glyphicon-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <div id="table-sflr" class="alert alert-info" role="alert">
                <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                <span class="sr-only">Information :</span>
                There are no Sflow receivers defined for this virtual interface.
                <a href="<?= route('interfaces/sflow-receiver/add' , ['id' => 0 , 'viid' => $t->vi->getId() ] ) ?>">
                    Add one now...
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

