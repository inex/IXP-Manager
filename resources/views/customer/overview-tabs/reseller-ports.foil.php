<div class="card">
    <div class="card-header">
        <ul class="nav nav-pills card-header-pills">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#peering">
                    Peering Ports
                </a>
            </li>
            <li class="nav-item" >
                <a class="nav-link" data-toggle="tab" href="#reseller">
                    Reseller Uplink Ports
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#fanout">
                    Fanout Ports
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <?php $nbVi = 1 ?>
        <div class="tab-content mt-4 ">
            <div id="peering" class="tab-pane fade show active">
                <?= $t->insert( 'customer/overview-tabs/ports/port-type', [ 'nbVi' => $nbVi, 'type' => \IXP\Models\SwitchPort::TYPE_PEERING, 'isSuperUser' => $t->isSuperUser ] ); ?>
            </div>
            <div id="reseller" class="tab-pane fade">
                <?= $t->insert( 'customer/overview-tabs/ports/port-type' , [ 'nbVi' => $nbVi, 'type' => \IXP\Models\SwitchPort::TYPE_RESELLER, 'isSuperUser' => $t->isSuperUser ] ); ?>
            </div>
            <div id="fanout" class="tab-pane fade">
                <?= $t->insert( 'customer/overview-tabs/ports/port-type' , [ 'nbVi' => $nbVi, 'type' => \IXP\Models\SwitchPort::TYPE_FANOUT, 'isSuperUser' => $t->isSuperUser ] ); ?>
            </div>
        </div>
    </div>
</div>