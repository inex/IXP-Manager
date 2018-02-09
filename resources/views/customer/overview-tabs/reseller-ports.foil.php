<div class="col-sm-12">
    <br>
    <ul class="nav nav-tabs">
        <li role="peering" class="active">
            <a data-toggle="tab" href="#peering">Peering Ports</a>
        </li>
        <li role="reseller" >
            <a data-toggle="tab" href="#reseller">Reseller Uplink Ports</a>
        </li>
        <li role="fanout" >
            <a data-toggle="tab" href="#fanout">Fanout Ports</a>
        </li>
    </ul>
    <?php $nbVi = 1 ?>
    <div class="tab-content">
        <div id="peering" class="tab-pane fade in active ">
            <br>
            <?= $t->insert( 'customer/overview-tabs/ports/port-type', [ 'nbVi' => $nbVi, 'type' => \Entities\SwitchPort::TYPE_PEERING ] ); ?>
        </div>
        <div id="reseller" class="tab-pane fade">
            <br>
            <?= $t->insert( 'customer/overview-tabs/ports/port-type' , [ 'nbVi' => $nbVi, 'type' => \Entities\SwitchPort::TYPE_RESELLER ] ); ?>
        </div>
        <div id="fanout" class="tab-pane fade">
            <br>
            <?= $t->insert( 'customer/overview-tabs/ports/port-type' , [ 'nbVi' => $nbVi, 'type' => \Entities\SwitchPort::TYPE_FANOUT ] ); ?>
        </div>
    </div>
</div>

