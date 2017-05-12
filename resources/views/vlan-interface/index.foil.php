<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= url( 'vlanInterface/list' )?>">Vlan Interfaces</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>List</li>
<?php $this->append() ?>

<?php $this->section('content') ?>

<?= $t->alerts() ?>
    <span id="message-vli"></span>
    <div id="area-vli">
        <table id='table-vli' class="table">
            <thead>
            <tr>
                <td>
                    Customer
                </td>
                <td>
                    VLAN Name
                </td>
                <td>
                    Router Server
                </td>
                <td>
                    IPv4
                </td>
                <td>
                    IPv6
                </td>
                <td>
                    Action
                </td>
            </tr>
            <thead>
            <tbody>
            <?php foreach( $t->listVli as $vli ):
                /** @var Entities\VlanInterface $vli */ ?>
                <tr>
                    <td>
                        <a href="<?= url( '/customer/overview/id' ).'/'.$vli['custid']?>">
                            <?= $vli['customer']   ?>
                        </a>
                    </td>
                    <td>
                        <a href="<?= url( 'vlan/list/id' ).'/'.$vli['vlanid']?>">
                            <?= $vli['vlan']   ?>
                        </a>
                    </td>
                    <td>
                        <?= $vli['rsclient'] ? '<i class="glyphicon glyphicon-ok"></i>' : '<i class="glyphicon glyphicon-remove"></i>'   ?>
                    </td>
                    <td>
                        <?= $vli['ipv4']   ?>
                    </td>
                    <td>
                        <?= $vli['ipv6']   ?>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <a class="btn btn btn-default" href="<?= url( '/virtualInterface/edit' ).'/'.$vli['vintid']?>" title="Virtual Interface">
                                <i class="glyphicon glyphicon-filter"></i>
                            </a>
                            <a class="btn btn btn-default" href="<?= url( '/vlanInterface/view' ).'/'.$vli['id']?>" title="Preview">
                                <i class="glyphicon glyphicon-eye-open"></i>
                            </a>
                            <a class="btn btn btn-default"  href="<?= url( '/vlanInterface/edit' ).'/'.$vli['id']?>" title="Edit">
                                <i class="glyphicon glyphicon-pencil"></i>
                            </a>

                            <a class="btn btn btn-default" id="delete-vli-<?= $vli['id'] ?>" href="" title="Delete">
                                <i class="glyphicon glyphicon-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach;?>
            <tbody>
        </table>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'virtual-interface/js/interface' ); ?>
    <script>
        $(document).ready( function() {
            loadDataTable( 'vli' );
        });

        /**
         * on click even allow to delete a Virtual Interface
         */
        $(document).on('click', "a[id|='delete-vli']" ,function(e){
            e.preventDefault();
            var vli = (this.id).substring(11);
            deletePopup( vli , false , 'vli');
        });

    </script>
<?php $this->append() ?>