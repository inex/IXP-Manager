<?php $this->layout( 'layouts/ixpv4' ) ?>

<?php $this->section( 'title' ) ?>
    <a href="<?= route ( 'vlan@list' ) ?>">VLANs</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>
        Delete IP Addresses
    </li>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <?= $t->alerts() ?>
    <div class="row">
        <div class="col-md-12">
            <?php if( $t->network ): ?>
                <div class="well col-md-12 form-inline">
                    <div style="display: inline" class="col-md-6 ">

                        <?= Former::open()->method( 'POST' )
                            ->action( route( 'ipAddress@preDeleteForVlanPost', [ "vlan" => $t->vlan->getId(), 'protocol' => 1 ] ) )
                        ?>

                        <?= Former::text( 'network' )
                            ->id( 'network' )
                            ->placeholder( 'Enter Network' )
                            ->label( ' ' )
                            ->blockHelp( '' )
                        ?>

                        <?=Former::actions( Former::primary_submit( 'Save Changes' ) );?>

                        <?= Former::close() ?>

                    </div>
                </div>

            <?php endif; ?>

            <?php if( $t->ip ): ?>

                <h3> List of IP addresses ready to be deleted for <?= $t->ip ?> </h3>

                <div class="col-md-6">
                    <table id='table-ip' class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>
                                    IP Address
                                </th>
                            </tr>
                        <thead>
                        <tbody>
                            <?php if( count( $t->ips ) > 0 ): ?>
                                <?php foreach( $t->ips[ 'ip' ] as $ip ): ?>

                                    <tr>
                                        <td>
                                            <?= $t->ee( $ip->getAddress() )   ?>
                                        </td>
                                    </tr>

                                <?php endforeach;?>
                            <?php else: ?>
                                <tr>
                                    <td>
                                        No result found
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <tbody>
                    </table>
                </div>

                <input type="hidden" id="ip-searched" value="<?= $t->ip ?>">
            <?php endif; ?>

            <?php if( !$t->network ): ?>

                <h3> List of IP addresses ready to be deleted for the Vlan: <?= $t->ee( $t->vlan->getName() ) ?></h3>

                <div class="col-md-6">
                    <table id='table-ipv4' class="table col-md-6">
                        <thead>
                            <tr>
                                <th>
                                    IPv4 Address
                                </th>
                            </tr>
                            <thead>

                        <tbody>
                            <?php if( isset( $t->ips[ 'ipv4' ] ) && count( $t->ips[ 'ipv4' ] ) > 0 ): ?>
                                <?php foreach( $t->ips[ 'ipv4' ] as $ipv4 ): ?>

                                    <tr>
                                        <td>
                                            <?= $t->ee( $ipv4->getAddress() )   ?>
                                        </td>
                                    </tr>

                                <?php endforeach;?>
                            <?php else: ?>
                                <tr>
                                    <td>
                                        No result found
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <tbody>

                    </table>
                </div>

                <div class="col-md-6">
                    <table id='table-ipv6' class="table">
                        <thead>
                        <tr>
                            <th>
                                IPv6 Address
                            </th>
                        </tr>
                        <thead>

                        <tbody>
                            <?php if( isset( $t->ips[ 'ipv6' ] ) && count( $t->ips[ 'ipv6' ] ) > 0 ): ?>
                                <?php foreach( $t->ips[ 'ipv6' ] as $ipv6 ): ?>

                                    <tr>
                                        <td>
                                            <?= $t->ee( $ipv6->getAddress() )   ?>
                                        </td>
                                    </tr>

                                <?php endforeach;?>
                            <?php else: ?>
                                <tr>
                                    <td>
                                        No result found
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <tbody>

                    </table>
                </div>

            <?php endif; ?>

            <?php if( count( $t->ips ) > 0 ): ?>
                <div class="col-sm-12 alert alert-danger" style="float: right;" role="alert"><div>
                    <span style="line-height: 34px;">
                        <strong>Delete All the IP Addresses displayed above</strong>
                    </span>
                    <a class="btn btn btn-danger" onclick="deleteIPs()" style="float: right;" title="Delete">
                        Delete
                    </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div>


<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script>

        $(document).ready(function(){
            $( '#table-ipv4' ).dataTable( { "autoWidth": false, "pageLength": 50 } );
            $( '#table-ipv6' ).dataTable( { "autoWidth": false, "pageLength": 50 } );
            $( '#table-ip'   ).dataTable( { "autoWidth": false, "pageLength": 50 } );
            $( '#network'   ).prop('required',true);


        });

        /**
         * Function that allow to delete a core link
         */
        function deleteIPs( vid , network ){
            let urlAction = "<?= route('ipAddress@deleteForVlan') ?>";


            bootbox.confirm({
                title: "Delete IP Addresses",
                message: `Do you really want to delete All the IP Addresses?` ,
                buttons: {
                    cancel: {
                        label: '<i class="fa fa-times"></i> Cancel'
                    },
                    confirm: {
                        label: '<i class="fa fa-check"></i> Confirm'
                    }
                },
                callback: function (result) {
                    if (result) {
                        $.ajax( urlAction, {
                            type: 'POST',
                            data: {
                                vid: <?= $t->vlan->getId() ?>,
                                network: $( '#ip-searched' ).val()
                            },
                        })
                        .done( function( data ) {
                            if( result ) {
                                 window.location.href = "<?= route('vlan@list') ?>/";
                            }
                        })
                        .fail( function(){
                            alert( 'Could not update notes. API / AJAX / network error' );
                            throw new Error("Error running ajax query for " + urlAction);
                        })
                        .always( function() {
                            $('#notes-modal').modal('hide');
                        });
                    }
                }
            });
        }
    </script>
<?php $this->append() ?>
