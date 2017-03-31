<?php
/** @var Foil\Template\Template $t */

    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= url( 'virtual-interface/edit/id/'.$t->vli->getVirtualInterface()->getId())?>">Vlan Interface</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>
        Layer 2 Interface : <?= $t->vli->getVlan()->getName()?>
    </li>

    <span class="pull-right">
        <div class="btn-group btn-group-xs" role="group">
            <a type="button" class="btn btn-default" id="add-l2a">
                <span class="glyphicon glyphicon-plus"></span>
            </a>
        </div>
    </span>
<?php $this->append() ?>


<?php $this->section( 'content' ) ?>
    <div id="message"></div>
    <div id="list-area">
        <table id='layer-2-interface-list' class="table">
            <thead>
            <tr>
                <td>Id</td>
                <td>MAC Address</td>
                <td>Created at</td>
                <td>Action</td>
            </tr>
            <thead>
            <tbody >
            <?php foreach( $t->vli->getLayer2Addresses() as $l2a ):
                /** @var \Entities\PatchPanelPort $ppp */
                ?>
                <tr>
                    <td>
                        <?= $l2a->getId() ?>
                    </td>
                    <td>
                        <?= $l2a->getMacFormatedComma() ?>
                    </td>
                    <td>
                        <?= $l2a->getCreatedAtFormated() ?>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <a class="btn btn btn-default" id="view-l2a-<?= $l2a->getId() ?>" href="#" title="View">
                                <i class="glyphicon glyphicon-eye-open"></i>
                            </a>
                            <button class="btn btn btn-default" id="delete-l2a-<?= $l2a->getId() ?>" href="#" title="Delete">
                                <i class="glyphicon glyphicon-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            <?php endforeach;?>
            <tbody>
        </table>
    </div>

    <!-- Modal dialog for views mac address with different formats -->
    <div class="modal fade" id="notes-modal" tabindex="-1" role="dialog" aria-labelledby="notes-modal-label">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="notes-modal-label">MAC Address</h4>
                </div>
                <div class="modal-body" id="notes-modal-body">
                    <div class="input-group">
                        <input class="form-control" readonly id="mac">
                        <div class="input-group-btn">
                            <button id="btn-copy-mac" class="btn btn-copy btn-default" data-clipboard-action="copy" data-clipboard-target="#mac">
                                <span class="glyphicon glyphicon-copy"></span>
                            </button>
                        </div>
                    </div>
                    <br>
                    <div class="input-group">
                        <input class="form-control" readonly id="macComma">
                        <div class="input-group-btn">
                            <button id="btn-copy-mac-comma" class="btn btn-copy btn-default" data-clipboard-action="copy" data-clipboard-target="#macComma">
                                <span class="glyphicon glyphicon-copy"></span>
                            </button>
                        </div>
                    </div>
                    <br>
                    <div class="input-group">
                        <input class="form-control" readonly id="macDot">
                        <div class="input-group-btn">
                            <button id="btn-copy-mac-dot" class="btn btn-copy btn-default" data-clipboard-action="copy" data-clipboard-target="#macDot">
                                <span class="glyphicon glyphicon-copy"></span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input  id="notes-modal-ppp-id"      type="hidden" name="notes-modal-ppp-id" value="">
                    <button id="notes-modal-btn-cancel"  type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                </div>
            </div>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script type="text/javascript" src="<?= asset( '/bower_components/clipboard/dist/clipboard.min.js' ) ?>"></script>
    <?= $t->insert( 'vlan-interface/js/index' ); ?>
<?php $this->append() ?>