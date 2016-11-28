<?php $this->layout('layouts/ixpv4') ?>

<?php $this->section('title') ?>
    Looking Glass
<?php $this->append() ?>

<?php $this->section('page-header-preamble') ?>
    <?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>
        <li class="pull-right">
    <?php else: ?>
        <div class="pull-right">
    <?php endif; ?>

        <div class="btn-group" role="group">

            <div class="btn-group" role="group">
                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    <?= $t->lg ? $t->lg->router()->name() : 'Select a router...' ?>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right">
                    <?php foreach( $t->routers as $type => $subRouters ): ?>
                        <li role="separator" class="divider"></li>
                        <li class="dropdown-header">
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
                        </li>
                        <?php foreach( $subRouters as $key => $name ): ?>
                            <li class="<?= $t->lg && $key == $t->lg->router()->handle() ? 'active' : '' ?>">
                                <a href="<?= url('/lg/'.$key) ?>">
                                    <?= $name ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php if( $t->lg ): ?>
                <a type="button" class="btn btn-default" href="<?= url('lg/' . $t->lg->router()->handle() . '/route-search') ?>">
                    <span class="glyphicon glyphicon-search"></span>
                </a>
            <?php endif; ?>
            <a type="button" class="btn btn-default" href="<?= url('lg') .'/' . ( $t->lg ? $t->lg->router()->handle() : '' ) ?>">
                <span class="glyphicon glyphicon-home"></span>
            </a>
        </div>

    <?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>
        </li>
    <?php else: ?>
        </div>
    <?php endif; ?>
<?php $this->append() ?>

<?php if( !Auth::check() ): ?>
    <?php $this->section('page-header-postamble') ?>
        <em>This is the public looking glass. Uncached results and additional routers available when logged in.</em>
    <?php $this->replace() ?>
<?php endif; ?>

<?php $this->section('content') ?>

<?php if( $t->lg ): ?>
    <div class="well well-sm">
        <?= ucfirst( $t->lg->router()->software() ) ?>
        <?= $t->status->status->version ?>
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
