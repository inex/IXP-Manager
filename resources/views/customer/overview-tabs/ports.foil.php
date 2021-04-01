<div class="d-flex row">
    <?php if( $t->isSuperUser && !$t->c->statusNormal() ): ?>
        <div class="alert alert-danger" role="alert">
            <b>Warning! Customer status is not normal.</b>
            Many backend processes that configure interface related systems (for example
            MRTG, P2P statistics, Nagios, Smokeping, route collector, route servers, etc.)
            will skip members that do not have their customer status set to normal.
        </div>
    <?php endif; ?>

    <?php $nbVi = 1 ?>
    <?php foreach( $t->c->virtualInterfaces as $vi ): ?>
        <?= $t->insert( 'customer/overview-tabs/ports/port', [ 'c' => $t->c ,'vi' => $vi, 'nbVi' => $nbVi, 'isSuperUser' => $t->isSuperUser ] ); ?>
        <?php $nbVi++ ?>
    <?php endforeach; ?>
</div>