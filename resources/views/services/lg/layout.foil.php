<?php $this->layout('layouts/ixpv4') ?>

<?php $this->section('page-header-preamble') ?>
    Looking Glass
<?php $this->append() ?>

<?php $this->section('page-header-postamble') ?>

    <div class="btn-group btn-group-sm" role="group">
        <div class="btn-group btn-group-sm" role="group">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <?= $t->lg ? $t->lg->router()->name() : 'Select a router...' ?>
            </button>
            <div class="dropdown-menu dropdown-menu-right">
                <?php foreach( $t->routers as $type => $subRouters ): ?>
                    <div class="dropdown-divider"></div>
                    <h6 class="dropdown-header">
                        <?php
                            switch( $type ):
                                case 'AS112':
                                    echo 'AS112 Services';
                                    break;
                                case 'RC':
                                    echo 'Route Collectors';
                                    break;
                                case 'RS':
                                    echo 'Route Servers';
                                    break;
                                default:
                                    echo $type;
                                    break;
                            endswitch;
                        ?>
                    </h6>
                    <?php foreach( $subRouters as $key => $name ): ?>

                        <a class="dropdown-item <?= $t->lg && $key == $t->lg->router()->handle() ? 'active' : '' ?>" href="<?= url('/lg/'.$key) ?>">
                            <?= $name ?>
                        </a>

                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php if( $t->lg ): ?>
            <a class="btn btn-outline-secondary" href="<?= url('lg/' . $t->lg->router()->handle() . '/route-search') ?>">
                <span class="fa fa-search"></span>
            </a>
        <?php endif; ?>
        <a class="btn btn-outline-secondary" href="<?= url('lg') .'/' . ( $t->lg ? $t->lg->router()->handle() : '' ) ?>">
            <span class="fa fa-home"></span>
        </a>
    </div>


<?php $this->append() ?>

<?php if( !Auth::check() ): ?>
    <?php $this->section('page-header-postamble') ?>
        <em>This is the public looking glass. Uncached results and additional routers available when logged in.&nbsp;&nbsp;&nbsp;&nbsp;</em>
    <?php $this->replace() ?>
<?php endif; ?>

<?php $this->section('content') ?>

<?php if( $t->lg ): ?>
    <div class="card mb-4">
        <div class="card-body bg-light d-flex">
            <div class="mr-auto">
                <?= $t->lg->router()->resolveSoftware() ?>
                <?= $t->status->status->version ?>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                API: <?= $t->status->api->version ?>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <?php if( isset( $t->status->status->router_id ) ): ?>
                    Router ID: <?= $t->status->status->router_id ?>
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                <?php endif; ?>
                Uptime: <?= (new DateTime)->diff( DateTime::createFromFormat( 'Y-m-d\TH:i:sO', $t->status->status->last_reboot ) )->days ?> days.
                &nbsp;&nbsp;|&nbsp;&nbsp;
                Last Reconfigure: <?= DateTime::createFromFormat( 'Y-m-d\TH:i:sO', $t->status->status->last_reconfig )->format( 'Y-m-d H:i:s' ) ?>
                <?php if( isset( $t->content->api->from_cache ) and $t->content->api->from_cache ): ?>
                    <span class="label label-info pull-right">
                    Cached data. Maximum age: <?= $t->content->api->ttl_mins ?> mins.
                </span>
                <?php endif; ?>
            </div>

            <div tyle="font-family: monospace;">
                JSON:
                [<a href="<?= route( "lg-api::status",  [ 'handle' => $t->lg->router()->getHandle() ] ) ?>">status</a>]
                [<a href="<?= route( "lg-api::bgp-sum", [ 'handle' => $t->lg->router()->getHandle() ] ) ?>">bgp</a>]
            </div>
        </div>

    </div>
<?php endif; ?>

<?php $this->append() ?>

<?php $this->section('scripts') ?>

<?= $t->insert('services/lg/js/datatables-ip-sort') ?>

<script type="text/javascript">
    // http://stackoverflow.com/questions/12449890/reload-content-in-modal-twitter-bootstrap
    $(document).on('hidden.bs.modal', function (e) {
        $(e.target).removeData('bs.modal');
    });
</script>

<?php $this->append() ?>
