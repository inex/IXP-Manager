<?php $this->layout( 'layouts/ixpv4' );
/** @var object $t */
?>

<?php $this->section( 'title' ) ?>
    Search - <?= $t->type ?> - <?= $t->ee( $t->search ) ?>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>

    <div class="row">

        <div class="col-sm-12">
            <?= $t->alerts() ?>
            <div class="well">

                <form class="form-inline" method="get" action="<?= route( 'search' ) ?>">

                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Search for..." name="search" value="<?= $t->search ?>">
                        <button class="btn btn-default" type="submit">Search</button>
                        <a class="btn btn-default" id="searchHelp" data-toggle="modal" data-target="#searchHelpModal">
                            <span class="glyphicon glyphicon-question-sign"></span>
                        </a>
                    </div>

                </form>

            </div>


            <?php if( count( $t->results ) ): ?>

                <h4>
                    <?= count( $t->results ) ?> Result(s)

                    <?php
                    switch( $t->type ) {
                        case 'asn':       echo ' - AS Number'; break;
                        case 'asmacro':   echo ' - AS Macro'; break;
                        case 'cust_wild': echo ' - Wildcard Customer Search'; break;
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


                <?php if( $t->type == 'email' ): ?>

                    <?= $t->insert( 'search/users' ) ?>
                    <?= $t->insert( 'search/contacts' ) ?>

                <?php elseif( $t->type == 'username' ): ?>

                    <?= $t->insert( 'search/users' ) ?>

                <?php elseif( $t->type == 'rsprefix' ): ?>

                    <?= $t->insert( 'search/rsprefixes' ) ?>

                <?php elseif( $t->type == 'ppp-xc' ): ?>

                    <?= $t->insert( 'search/ppps' ) ?>

                <?php else: ?>

                    <div class="list-group">
                        <?php foreach( $t->results as $cust ): ?>
                            <div class="list-group-item">
                                <div>
                                    <b>
                                        <a style="font-size: x-large" href="<?= route( "customer@overview" , [ "id" => $cust->getId() ] ) ?>">
                                            <?= $t->ee( $cust->getAbbreviatedName() ) ?> - AS<?= $t->ee( $cust->getAutsys() )?>
                                        </a>
                                    </b>
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    <?= $t->insert( 'customer/cust-type', [ 'cust' => $cust  ] ) ?>
                                </div>

                                <?php if( count( $t->interfaces ) ): ?>
                                    <?php if( $t->type == 'mac' ): ?>
                                        <?= $t->insert( 'search/additional/mac', [ 'cust' => $cust ,'interfaces' => $t->interfaces , 'type' => $t->type, 'search' => $t->search ]  ) ?>
                                    <?php elseif( $t->type == 'ipv4' || $t->type == 'ipv6' ): ?>
                                        <?= $t->insert( 'search/additional/ip', [ 'cust' => $cust ,'interfaces' => $t->interfaces , 'type' => $t->type ] ) ?>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <div class="btn-group">
                                    <a class="btn btn-default" href="<?= route( "customer@overview" , [ "id" => $cust->getId() ] ) ?>">Overview</a>
                                    <a class="btn btn-default" href="<?= route( "customer@overview" , [ "id" => $cust->getId(), "tab" => "ports" ] ) ?>">Ports</a>
                                    <a class="btn btn-default" href="<?= route( "statistics@member-drilldown" , [ "typeid" => $cust->getId(), "type" => "agg" ] ) ?>">
                                        Statistics
                                    </a>
                                    <a class="btn btn-default" href="<?= route( 'statistics@p2p-get', [ "id" => $cust->getId() ] )?>">
                                        P2P
                                    </a>
                                    <a class="btn btn-default" href="<?= route( "customer@overview" , [ "id" => $cust->getId(), "tab" => "users" ] ) ?>">Users</a>
                                    <a class="btn btn-default" href="<?= route( "customer@overview" , [ "id" => $cust->getId(), "tab" => "contacts" ] )?>">Contacts</a>
                                </div>

                            </div>

                        <?php endforeach; ?>
                    </div>

                <?php endif; ?>

            <?php else: ?>

                <h3>No results found.</h3>

            <?php endif; ?>


        </div>

    </div>

<?php $this->append() ?>
