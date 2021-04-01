<?php
  $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    VLANs
    /
    Delete Free IP Addresses
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="row">
        <div class="col-md-12">
            <?= $t->alerts() ?>
            <div class="card">
                <div class="card-header">
                    <h3>Delete Free IP Addresses for VLAN: <?= $t->vlan->name ?></h3>
                </div>

                <div class="card-body row">
                    <div class="col-md-6">
                        <?= Former::open()->method( 'post' )
                            ->action( route( 'ip-address@delete-by-network', [ "vlan" => $t->vlan->id ] ) )
                            ->actionButtonsCustomClass( "grey-box")
                            ->customInputWidthClass( 'col-lg-8 col-sm-6' )
                            ->customLabelWidthClass( 'col-lg-4 col-sm-4' )
                        ?>

                        <?= Former::text( 'network' )
                            ->label("Network")
                            ->id( 'network' )
                            ->placeholder( '2a01:db8::/124 | 192.0.2.0/28' )
                            ->blockHelp( 'Enter a subnet is CIDR format.' )
                        ?>

                        <?=
                        Former::actions(
                            Former::primary_submit( 'Find Free Addresses' )->class( "mb-2 mb-lg-0"),
                            Former::secondary_link( 'Cancel' )->href( route( 'ip-address@list', [ 'protocol' => 6, 'vlanid' => $t->vlan->id ] ) )->class( "mb-2 mb-lg-0")
                        )->class( "text-center" );
                        ?>

                        <?= Former::close() ?>
                    </div>

                    <div class="col-md-6 mt-4 mt-md-0">
                        <p>
                            This tool allows you to delete free (unused / unallocated) IP addresses in a given CIDR network.
                        </p>
                        <p>
                            Enter a network in the input box such as <code>2a01:db8::/124</code> or <code>192.0.2.0/28</code>.
                        </p>
                        <p>
                            Once you click <em>Find IP Addresses</em>, IXP Manager will find and show you the available IP addresses
                            that would be deleted. You will have the opportunity to confirm this action before it is processed.
                        </p>
                    </div>
                </div>
            </div>

            <?php if( count( $t->ips ) ): ?>
                <div class="card mt-4">
                    <div class="card-header">
                        <h3>List of Free IP Addresses To Be Deleted</h3>
                    </div>
                    <div class="card-body">
                        <table id='table-ip' class="table table-striped" width="100%">
                            <thead class="thead-dark">
                                <tr>
                                    <th>
                                        IP Addresses
                                    </th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>

                            <tbody>
                                  <?php $count = 1; ?>
                                  <tr>
                                      <?php foreach( $t->ips as $ip ): ?>

                                          <td>
                                              <?= $ip->address ?>
                                          </td>

                                          <?php if( $count++ % 3 == 0 ) { echo "</tr><tr>"; } ?>

                                      <?php
                                  endforeach;

                                  if( !$count % 3 == 0 ) {
                                      while( $count % 3 == 0 ) {
                                          echo '<td></td>';
                                          $count++;
                                      }
                                      echo "</tr>";
                                  }

                                  ?>
                            </tbody>

                        </table>

                        <div class="alert alert-danger mt-4" role="alert">
                            <div class="d-flex align-items-center">
                                <div class="text-center">
                                    <i class="fa fa-exclamation-triangle fa-2x"></i>
                                </div>
                                <div class="col-sm-12 d-flex">
                                    <b class="mr-auto my-auto">
                                        Delete all the IP addresses displayed above?
                                    </b>
                                    <a class="btn btn-danger mr-4" id="delete" href="#">
                                        Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'ip-address/js/delete-by-network.foil.php' ) ?>
<?php $this->append() ?>