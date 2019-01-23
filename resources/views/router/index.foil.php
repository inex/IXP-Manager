<?php
    /** @var Foil\Template\Template $t */

    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Router / List
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm" role="group">
        <a target="_blank" class="btn btn-outline-secondary" href="https://docs.ixpmanager.org/features/routers/">
            Documentation
        </a>

        <a class="btn btn-outline-secondary" href="<?= route ('router@add') ?>">
            <i class="fa fa-plus"></i>
        </a>
    </div>
<?php $this->append() ?>

<?php $this->section('content') ?>

<div class="row">

    <div class="col-sm-12">

        <?= $t->alerts() ?>
        <table id='router-list' class="table table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>
                        Handle
                    </th>
                    <th>
                        Name
                    </th>
                    <th>
                        Vlan
                    </th>
                    <th>
                        Protocol
                    </th>
                    <th>
                        Type
                    </th>
                    <th>
                        Router
                    </th>
                    <th>
                        Peering IP
                    </th>
                    <th>
                        ASN
                    </th>
                    <th>
                        Last Updated
                    </th>
                    <th>
                        Actions
                    </th>
                </tr>
            <thead>
            <tbody>
                <?php foreach( $t->routers as $router ):
                    /** @var Entities\Router $router */ ?>
                    <tr>
                        <td>
                            <?= $t->ee( $router->getHandle() ) ?>
                        </td>
                        <td>
                            <?= $t->ee( $router->getShortName() ) ?>
                        </td>
                        <td>
                            <a href="<?= route( "vlan@view", [ "id" => $router->getVlan()->getId() ] ) ?> ">
                                <?= $t->ee( $router->getVlan()->getName() )?>
                            </a>
                        </td>
                        <td>
                            <?= $router->resolveProtocol() ?>
                        </td>
                        <td>
                            <?= $router->resolveTypeShortName() ?>
                        </td>
                        <td>
                            <?= $router->getRouterId() ?>
                        </td>
                        <td>
                            <?= $router->getPeeringIp() ?>
                        </td>
                        <td>
                            <?= $router->getAsn() ?>
                        </td>
                        <td>
                            <?= $router->getLastUpdated() ? $router->getLastUpdated()->format('Y-m-d H:i:s') : '(unknown)' ?>
                            <?php if( $router->getLastUpdated() && $router->lastUpdatedGreaterThanSeconds( 86400 ) ): ?>
                                <span class="badge badge-danger">
                                    <i class="fa fa-exclamation-triangle" title="Last updated more than 1 day ago"></i>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a target="_blank" class="btn btn-outline-secondary" href="<?= route('apiv4-router-gen-config', [ 'handle' => $router->getHandle() ] ) ?>" title="Configuration">
                                    <i class="fa fa-file"></i>
                                </a>
                                <a class="btn btn-outline-secondary" href="<?= route('router@view' , [ 'id' => $router->getId() ] ) ?>" title="Preview">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a class="btn btn-outline-secondary" href="<?= route('router@edit' , [ 'id' => $router->getId() ] )?>" title="Edit">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <a class="btn btn-outline-secondary" id="delete-router-<?=$router->getId() ?>" href="" title="Delete">
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
    <script>
        $('#router-list').DataTable({
            "autoWidth": false
        });

        $( "a[id|='delete-router']" ).on( 'click', function( e ) {
            e.preventDefault();
            let rtid = ( this.id ).substring( 14 );
            bootbox.confirm({
                message: "Do you want to delete this router ?",
                buttons: {
                    confirm: {
                        label: 'Confirm',
                        className: 'btn-primary',
                    },
                    cancel: {
                        label: 'Cancel',
                        className: 'btn-secondary',
                    }
                },
                callback: function ( result ) {
                    if( result ){
                        location.href = "<?= url('router/delete' )?>/" + rtid;
                    } else {
                        $( '.bootbox.modal' ).modal( 'hide' );
                    }
                }
            });
        });

    </script>
<?php $this->append() ?>