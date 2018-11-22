<br/>
<div class="col-sm-12">

    <ul class="nav nav-tabs">

        <li id="peering-potential-li" role="potential-peers" class="active">
            <a data-toggle="tab" href="#potential-peers">Potential Peers</a>
        </li>

        <li id="peering-peers-li" role="peered-peers">
            <a data-toggle="tab" href="#peered-peers">Peers</a>
        </li>

    </ul>


    <div class="tab-content">

        <div id="potential-peers" class="tab-pane fade in active">
            <?= $t->insert( 'customer/overview-tabs/peers/table' , [ "listOfCusts" =>  $t->peers[ "potential" ] ] ); ?>
        </div>

        <div id="peered-peers" class="tab-pane fade">
            <?= $t->insert( 'customer/overview-tabs/peers/table' , [ "listOfCusts" => $t->peers[ "peered" ] ] ); ?>
        </div>

    </div>
    
</div>