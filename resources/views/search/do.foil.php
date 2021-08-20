<?php $this->layout( 'layouts/ixpv4' );
/** @var object $t */
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Search - <?= $t->type ?> - <?= $t->ee( $t->search ) ?>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="row">
        <div class="col-sm-12">
            <?= $t->alerts() ?>
            <div class="card bg-light">
                <div class="card-body">
                    <form class="form-inline" method="get" action="<?= route( 'search' ) ?>">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search for..." name="search" value="<?= $t->search ?>">
                            <div class="input-group-append">
                                <button class="btn btn-white" type="submit">Search</button>
                                <button class="btn btn-white" type="button" data-toggle="modal" data-target="#searchHelpModal">
                                    <span class="fa fa-question-circle"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php if( count( $t->results ) ): ?>
                <div class="card mt-4">
                    <div class="card-header">
                        <h4>
                            <?= count( $t->results ) ?> Result(s)
                            <?php
                            switch( $t->type ) {
                                case 'asn':       echo ' - AS Number'; break;
                                case 'asmacro':   echo ' - AS Macro'; break;
                                case 'cust_wild': echo ' - Wildcard ' . config( 'ixp_fe.lang.customer.one' ) . ' Search'; break;
                                case 'email':     echo ' - Email Address'; break;
                                case 'ipv4':      echo ' - IPv4 Addresses'; break;
                                case 'ipv6':      echo ' - IPv6 Addresses'; break;
                                case 'mac':       echo ' - MAC Addresses'; break;
                                case 'ppp-xc':    echo ' - Patch Panel Port Colo Circuit Reference'; break;
                                case 'rsprefix':  echo ' - Route Server Prefix'; break;
                                case 'username':  echo ' - Users from Username'; break;
                            }
                            ?>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if( $t->type === 'email' ): ?>
                            <?= $t->insert( 'search/users' ) ?>
                            <?= $t->insert( 'search/contacts' ) ?>
                        <?php elseif( $t->type === 'username' ): ?>
                            <?= $t->insert( 'search/users' ) ?>
                        <?php elseif( $t->type === 'rsprefix' ): ?>
                            <?= $t->insert( 'search/rsprefixes' ) ?>
                        <?php elseif( $t->type === 'ppp-xc' ): ?>
                            <?= $t->insert( 'search/ppps' ) ?>
                        <?php else: ?>
                            <div class="list-group">
                                <?php foreach( $t->results as $cust ): ?>
                                    <div class="list-group-item">
                                        <div>
                                            <b class="mr-2">
                                                <a style="font-size: x-large" href="<?= route( "customer@overview" , [ 'cust' => $cust->id ] ) ?>">
                                                    <?= $t->ee( $cust->abbreviatedName ) ?> - AS<?= $t->ee( $cust->autsys )?>
                                                </a>
                                            </b>

                                            <?= $t->insert( 'customer/cust-type', [ 'cust' => $cust  ] ) ?>
                                        </div>

                                        <?php if( count( $t->interfaces ) ): ?>
                                            <?php if( $t->type === 'mac' ): ?>
                                                <?= $t->insert( 'search/additional/mac', [ 'cust' => $cust ,'interfaces' => $t->interfaces , 'type' => $t->type, 'search' => $t->search ]  ) ?>
                                            <?php elseif( $t->type === 'ipv4' || $t->type === 'ipv6' ): ?>
                                                <?= $t->insert( 'search/additional/ip', [ 'cust' => $cust ,'interfaces' => $t->interfaces , 'type' => $t->type ] ) ?>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <div class="btn-group flex-wrap">
                                            <a class="btn btn-white" href="<?= route( "customer@overview" , [ 'cust' => $cust->id ] ) ?>">
                                                Overview
                                            </a>
                                            <a class="btn btn-white" href="<?= route( "customer@overview" , [ 'cust' => $cust->id , "tab" => "ports" ] ) ?>">
                                                Ports
                                            </a>
                                            <a class="btn btn-white" href="<?= route( "statistics@member-drilldown" , [ "typeid" => $cust->id, "type" => "agg" ] ) ?>">
                                                Statistics
                                            </a>
                                            <a class="btn btn-white" href="<?= route( 'statistics@p2p-get', [ 'cust' => $cust->id ] )?>">
                                                P2P
                                            </a>
                                            <a class="btn btn-white" href="<?= route( "customer@overview" , [ 'cust' => $cust->id, "tab" => "users" ] ) ?>">
                                                Users
                                            </a>
                                            <a class="btn btn-white" href="<?= route( "customer@overview" , [ 'cust' => $cust->id, "tab" => "contacts" ] )?>">
                                                Contacts
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info mt-4" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="text-center">
                            <i class="fa fa-question-circle fa-2x"></i>
                        </div>
                        <div class="col-sm-12">
                            Sorry, we couldn't find any results matching with "<?= $t->ee( $t->search ) ?>"
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script>
        $( '.table' ).dataTable( {
            stateSave: true,
            stateDuration : DATATABLE_STATE_DURATION,
            responsive: true,
            ordering: false,
            searching: false,
            paging:   false,
            info:   false,
        } ).show();
    </script>
<?php $this->append() ?>
