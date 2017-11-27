<?php $this->layout( 'layouts/ixpv4' ) ?>



<?php $this->section( 'title' ) ?>
    <a href="<?= route ( 'vlan@list' ) ?>">VLANs</a>
<?php $this->append() ?>



<?php $this->section( 'page-header-postamble' ) ?>
    <li>
        Delete Free IP Addresses
    </li>
<?php $this->append() ?>



<?php $this->section( 'content' ) ?>

    <?= $t->alerts() ?>

    <div class="row">

        <div class="col-md-12">

            <div class="well col-md-12 form-inline">
                <div style="display: inline" class="col-md-12 col-ld-12">

                    <?= Former::open()->method( 'post' )
                        ->action( route( 'ip-address@delete-by-network', [ "vlanid" => $t->vlan->getId() ] ) )
                    ?>

                    <?= Former::text( 'network' )
                        ->label("Network")
                        ->addClass("col-md-6 col-ld-6")
                        ->id( 'network' )
                        ->placeholder( '2a01:db8::/124 | 192.0.2.0/28' )
                        ->blockHelp( '' )
                    ?>

                    <?= Former::actions( Former::primary_submit( 'Find Free Addresses' ) ); ?>

                    <?= Former::close() ?>

                </div>
            </div>

            <?php if( $t->ips ): ?>

                <h3> List of Free IP Addresses To Be Deleted in <?= $t->vlan->getName() ?> </h3>

                <div class="col-md-12">

                    <table id='table-ip' class="table table-striped">

                        <thead>
                            <tr>
                                <th>
                                    IP Addresses
                                </th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php $count = 1; ?>
                            <tr>
                            <?php foreach( $t->ips as $ip ): ?>

                                <td>
                                    <?= $ip->getAddress() ?>
                                </td>

                                <?php if( $count++ % 3 == 0 ): ?> </tr><tr> <?php endif; ?>

                            <?php

                                endforeach;

                                if( !$count % 3 == 0 ) {
                                    while( $count % 3 != 0 ) {
                                        echo '<td></td>';
                                        $count++;
                                    }
                                    echo "</tr>";
                                }

                            ?>


                        </tbody>
                    </table>
                </div>

            <?php endif; ?>


            <?php if( count( $t->ips ) > 0 ): ?>

                <br><br>

                <div class="alert alert-danger" role="alert">
                        <strong>Delete all the IP addresses displayed above?</strong>
                    <a class="btn btn btn-danger" onclick="deleteIPs()" style="float: right;" title="Delete">
                        Delete
                    </a>
                </div>

            <?php endif; ?>

        </div>
    </div>


<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script>

        $(document).ready( function() {
            $( '#table-ip'   ).dataTable( { "autoWidth": false, "pageLength": 50 } );
        });

        /**
         * Function that allow to delete a core link
         */
        function deleteIPs( vid , network ){
            let urlAction = "<?= route('ip-address@do-delete-by-network', [ 'vlanid' => $t->vlan->getId() ]) ?>";

            bootbox.confirm({
                title: "Delete IP Addresses",
                message: `Do you really want to delete all the listed IP addresses?` ,
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
                            alert( 'Error deleting IP addresses' );
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
