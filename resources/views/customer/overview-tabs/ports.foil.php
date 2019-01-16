<div class="d-flex row">

    <div class="col-md-12 row">

        <?php if( Auth::getUser()->isSuperUser() && !$t->c->statusIsNormal() ): ?>

            <div class="alert alert-danger" role="alert">
                <b>Warning! Customer status is not normal.</b>
                Many backend processes that configure interface related systems (for example
                MRTG, P2P statistics, Nagios, Smokeping, route collector, route servers, etc.)
                will skip members that do not have a normal status.
            </div>

        <?php endif; ?>

        <?php $nbVi = 1 ?>

        <?php foreach( $t->c->getVirtualInterfaces() as $vi ): ?>
            <?= $t->insert( 'customer/overview-tabs/ports/port', [ 'c' => $t->c ,'vi' => $vi, 'nbVi' => $nbVi ] ); ?>
            <?php $nbVi++ ?>
        <?php endforeach; ?>

    </div>
</div>