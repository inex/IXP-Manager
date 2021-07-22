<?php if( $t->peers ): ?>
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-pills card-header-pills">
                <li class="nav-item" id="peering-potential-li" role="potential-peers" >
                    <a class="nav-link active" data-toggle="tab" href="#potential-peers">
                      Potential Peers
                    </a>
                </li>
                <li class="nav-item"  id="peering-peers-li" role="peered-peers">
                    <a class="nav-link" data-toggle="tab" href="#peered-peers">
                      Peers
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content">
                <div id="potential-peers" class="tab-pane fade show active">
                    <?= $t->insert( 'customer/overview-tabs/peers/table' , [ "listOfCusts" =>  $t->peers[ "potential" ] ] ); ?>
                </div>
                <div id="peered-peers" class="tab-pane fade">
                    <?= $t->insert( 'customer/overview-tabs/peers/table' , [ "listOfCusts" => $t->peers[ "peered" ] ] ); ?>
                </div>
            </div>
        </div>
    </div>

    <?= $t->insert( 'customer/js/overview/peers', [ "peers" => $t->peers ] ); ?>
<?php else: ?>
    <script>
        $( '.peers-tab' ).remove();
    </script>
<?php endif; ?>