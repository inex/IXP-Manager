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

            <h3>Delete Free IP Addresses for VLAN: <?= $t->vlan->getName() ?></h3>

            <div class="well col-md-12">

                <div class="col-md-6">

                    <?= Former::open()->method( 'post' )
                        ->action( route( 'ip-address@delete-by-network', [ "vlanid" => $t->vlan->getId() ] ) )
                    ?>


                    <?= Former::text( 'network' )
                        ->label("Network")
                        ->addClass("col-md-6 col-ld-6")
                        ->id( 'network' )
                        ->placeholder( '2a01:db8::/124 | 192.0.2.0/28' )
                        ->blockHelp( 'Enter a subnet is CIDR format.' )

                    ?>

                    <?=
                        Former::actions(
                            Former::primary_submit( 'Find Free Addresses' ),
                            Former::default_link( 'Cancel' )->href( route( 'ip-address@list', [ 'protocol' => 6, 'vlanid' => $t->vlan->getId() ] ) )
                        );
                    ?>


                    <?= Former::close() ?>

                </div>

                <div class="col-md-6">

                    <p>
                        This tool allows you to delete free (unused / unallocated) IP addresses in a given CIDR network.
                    </p>

                    <p>
                        Enter a network in the input box such as <code>2a01:db8::/124</code> or <code>192.0.2.0/28</code>.
                    </p>

                    <p>
                        Once you click <em>Find IP Addresses</em>, IXP Manager will find and show you the available IP addresses
                        that would be deleted. You will have the opportunity to confirm this action before it is processes.
                    </p>

                </div>
            </div>
        </div>
    </div>


    <?php if( $t->ips ): ?>

        <div class="row">
            <div class="col-md-12">
                <h3>List of Free IP Addresses To Be Deleted</h3>
            </div>
        </div>

        <div class="row">

            <div class="col-md-12">

                <table id='table-ip' class="table table-striped">

                    <thead>
                        <tr>
                            <th colspan="3">
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

                            <?php if( $count++ % 3 == 0 ) { echo "</tr>\n<tr>"; } ?>

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

        </div>

        <div class="row">

            <div class="col-md-12">

                <br><br>

                <div class="alert alert-danger" role="alert">
                    <form method="post" action="<?= route( "ip-address@delete-by-network", [ 'vlanid' => $t->vlan->getId() ] ) ?>">
                        <strong>Delete all the IP addresses displayed above?</strong>

                        <input type="hidden"   name="network"  value="<?= Input::get('network' ) ?>">
                        <input type="hidden"   name="doDelete" value="1">
                        <input type="hidden"   name="_token"   value="<?= csrf_token() ?>">

                        <input type="submit" class="btn btn-sm btn-danger pull-right" name="submit" value="Delete">

                    </form>
                </div>

            </div>

        </div>

    <?php endif; ?>


<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script>

        $(document).ready( function() {
            $( '#table-ip'   ).dataTable( { "autoWidth": false, "pageLength": 50 } );
        });

    </script>
<?php $this->append() ?>
