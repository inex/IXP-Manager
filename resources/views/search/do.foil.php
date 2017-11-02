<?php $this->layout( 'layouts/ixpv4' );
/** @var object $t */
?>

<?php $this->section( 'title' ) ?>
    Search - <?= $t->type ?> - <?= $t->ee( $t->search ) ?>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>

    <?php if( count( $t->results ) ): ?>
        <div class="row-fluid">
            <div class="span12">
                <h4><?= count( $t->results ) ?> Result(s):</h4>
            </div>
        </div>
        <?php if( $t->type == 'username' || $t->type == 'email' ): ?>

            <?= $t->insert( 'search/contacts' ) ?>

        <?php elseif( $t->type == 'rsprefix' ): ?>

            <?= $t->insert( 'search/rsprefixes' ) ?>

        <?php else: ?>

            <div class="col-sm-10">
                <div class="list-group">
                    <?php foreach( $t->results as $cust ): ?>
                        <div class="list-group-item">
                            <div>
                                <b>
                                    <a style="font-size: x-large" href="<?= url( 'customer/overview/id/' . $cust->getId() )?>">
                                        <?= $t->ee( $cust->getAbbreviatedName() ) ?> - AS<?= $t->ee( $cust->getAutsys() )?>
                                    </a>
                                </b>
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <?= $t->insert( 'customer/cust-type', [ 'cust' => $cust , 'resellerMode' => $t->resellerMode ] ) ?>
                            </div>

                            <?php if( count( $t->interfaces ) ): ?>
                                <?php if( $t->type == 'mac' ): ?>
                                    <?= $t->insert( 'search/additional/mac', [ 'cust' => $cust ,'interfaces' => $t->interfaces , 'type' => $t->type, 'search' => $t->search ]  ) ?>
                                <?php elseif( $t->type == 'ipv4' || $t->type == 'ipv6' ): ?>
                                    <?= $t->insert( 'search/additional/ip', [ 'cust' => $cust ,'interfaces' => $t->interfaces , 'type' => $t->type ] ) ?>
                                <?php endif; ?>
                            <?php endif; ?>

                            <div class="btn-group">
                                <a class="btn btn-default" href="<?= url( 'customer/overview/id/' . $cust->getId() )?>">Overview</a>
                                <a class="btn btn-default" href="<?= url( 'customer/overview/id/' . $cust->getId() . '/tab/ports' )?>">Ports</a>
                                <a class="btn btn-default" href="<?= url( 'statistics/member-drilldown/monitorindex/aggregate/shortname/' . $cust->getShortname() )?>">
                                    Statistics
                                </a>
                                <a class="btn btn-default" href="<?= url( 'statistics/p2p/shortname/' . $cust->getShortname() )?>">
                                    P2P
                                </a>
                                <a class="btn btn-default" href="<?= url( 'customer/overview/id/' . $cust->getId() . '/tab/users' )?>">Users</a>
                                <a class="btn btn-default" href="<?= url( 'customer/overview/id/' . $cust->getId() . '/tab/contacts' )?>">Contacts</a>
                            </div>

                        </div>

                    <?php endforeach; ?>
                </div>
            </div>

        <?php endif; ?>
    <?php endif; ?>


<div class="row-fluid">

    <div class="span12">

        <?php if( !count( $t->results ) ): ?>
            <h3>No results found.</h3>
        <?php endif; ?>

    </div>

</div>
<br>
<div class="row-fluid">
    <div class="span12">
        <form class="form-inline" method="get" action="<?= action( 'SearchController@do' ) ?>">
            <div>
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search for..." name="search" value="<?= $t->search ?>">
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="submit">Search</button>
                        <a class="btn btn-default" id="searchHelp" data-toggle="modal" data-target="#searchHelpModal">
                            <span class="glyphicon glyphicon-question-sign"></span>
                        </a>
                    </span>
                </div>

            </div>
        </form>
    </div>
</div>

<?php $this->append() ?>
