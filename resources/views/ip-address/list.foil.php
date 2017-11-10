<?php
/** @var Foil\Template\Template $t */
/** @var $t->active */

$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    IPv<?= $t->protocol ?> Address
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    <li class="pull-right">
        <div class="btn-group btn-group-xs" role="group">
            <a type="button" class="btn btn-default" href="<?= route ('ipAddress@add', [ 'protocol' => $t->protocol ]) ?>">
                <span class="glyphicon glyphicon-plus"></span>
            </a>
        </div>
    </li>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <?= $t->alerts() ?>

    <div class="well col-md-12">
        <div class="col-md-6">

            <?= Former::select( 'vlan' )
                ->id( 'vlan' )
                ->label( ' ' )
                ->placeHolder( 'Choose a Vlan' )
                ->select( $t->vlan  ? $t->vlan->getId() : null )
                ->fromQuery( $t->vlans, 'name' )
                ->addClass( 'chzn-select' )
                ->blockHelp( '' );
            ?>

        </div>
    </div>

    <?php if( count( $t->ips ) ): ?>
        <table id='ip-address-list' class="table collapse" >
            <thead>
                <tr>
                    <td>
                        IP Address
                    </td>
                    <td>
                        Customer
                    </td>
                    <td>
                        Hostname
                    </td>
                    <td>
                        Action
                    </td>
                </tr>
            <thead>
            <tbody>
                <?php foreach( $t->ips as $ip ):?>
                    <tr>
                        <td>
                            <?= $t->ee( $ip[ 'address' ] ) ?>
                        </td>
                        <td>
                            <?= $t->ee( $ip[ 'customer' ] ) ?>
                        </td>
                        <td>
                            <?= $t->ee( $ip[ 'hostname' ] ) ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a class="btn btn btn-default <?= $ip[ 'viid' ] ? '' : 'disabled' ?>" href=" <?= route( "interfaces/virtual/edit" , [ 'id' => $ip[ 'viid' ] ]) ?>" title="Preview">
                                    <i class="glyphicon glyphicon-eye-open"></i>
                                </a>
                                <a class="btn btn btn-default <?= !$ip[ 'vliid' ] ? '' : 'disabled' ?>" id="delete-ip-<?=$ip[ 'id' ] ?>" href="" title="Preview">
                                    <i class="glyphicon glyphicon-trash"></i>
                                </a>

                            </div>
                        </td>
                    </tr>
                <?php endforeach;?>
            <tbody>
        </table>
    <?php endif;  /* !count( $t->patchPanels ) */ ?>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script>

        const protocol = "<?= $t->protocol ?>";

        $(document).ready(function(){
            $( '#ip-address-list' ).dataTable( { "autoWidth": false } );
            $( '#ip-address-list' ).show();

        });

        $( "#vlan" ).on( 'change', function(e){
            let vlan = this.value;

            window.location = "<?= url( 'address/list/' ) ?>/"+ protocol + '/' + vlan;
        });

        $( "a[id|='delete-ip']" ).on( 'click', function( e ) {
            e.preventDefault();
            let ipid = ( this.id ).substring( 10 );

            bootbox.confirm({
                message: "Do you want to delete this IP Address ?",
                buttons: {
                    confirm: {
                        label: 'Confirm',
                        className: 'btn-primary',
                    },
                    cancel: {
                        label: 'Cancel',
                        className: 'btn-default',
                    }
                },
                callback: function ( result ) {
                    if( result) {
                        $.ajax( "<?= url('address/delete/' )?>/" + protocol + "/" + ipid,{
                            type : 'POST'
                        })
                            .done( function( data ) {
                                location.reload();
                            })
                            .fail( function(){
                                throw new Error( `Error running ajax query for ${urlDelete}/${id}` );
                            })
                    }
                }
            });
        });
    </script>
<?php $this->append() ?>