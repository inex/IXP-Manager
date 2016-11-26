<?php $this->layout('services/lg/layout') ?>

<?php $this->section('title') ?>
    <small>Routes for <?= ucwords($t->source) ?> <code><?= $t->name ?></code></small>
<?php $this->append() ?>

<?php $this->section('content') ?>


<table class="table" id="routes">
    <thead>
        <tr>
            <th>Network</th>
            <th>Next Hop</th>
            <th></th>
            <th>Metric</th>
            <th>AS Path</th>
            <th></th>
        </tr>
    </thead>
    <tbody>

<?php if( !count( $t->content->routes ) ): ?>

    <tr><td colspan="6">No routes found</td></tr>

<?php else:

    foreach( $t->content->routes as $r ): ?>

    <tr>
        <td>
            <a href="<?= url('/lg') . '/' . $t->lg->router()->handle() ?>/route/<?= urlencode($r->network) ?>/table/master"
                    data-toggle="modal" data-target="#route-modal">
                <?= $r->network ?>
            </a>
        </td>
        <td>
            <?= $r->gateway ?>
        </td>
        <td>
            <?php if( $r->primary ): ?>
                <span class="label label-success">P</span>
            <?php else: ?>
                <span class="label label-warning">N</span>
            <?php endif; ?>
        </td>
        <td><?= $r->metric ?></td>
        <td>
            <?php if( isset($r->bgp->as_path) ): ?>
                <?= implode(' ', $r->bgp->as_path) ?>
            <?php endif; ?>
        </td>
        <td>
            <a class="btn btn-default btn-xs" data-toggle="modal"
                href="<?= url('/lg') . '/' . $t->lg->router()->handle() ?>/route/<?= urlencode($r->network) ?>/<?= $t->source ?>/<?= $t->name ?>"
                data-target="#route-modal">Details</a>
        </td>
    </tr>

    <?php endforeach; ?>

<?php endif; ?>

    </tbody>
</table>

<p>
    <br><br>
    Key: <span class="label label-success">P</span> - Primary / active route. <span class="label label-warning">N</span> - Inactive route.
</p>

<div class="modal fade" id="route-modal" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    </div>
  </div>
</div>



<?php $this->append() ?>

<?php $this->section('scripts') ?>

    <script type="text/javascript">

        $('#routes')
            .removeClass( 'display' )
            .addClass('table');

        $(document).ready(function() {
            $('#routes').DataTable({
                paging: false,
                order: [[ 0, "asc" ]],
                columnDefs: [
                    { type: 'ip-address', targets: 0 },
                    { type: 'ip-address', targets: 0 },
                    { type: 'int', targets: 0 },
                    { type: 'string', targets: 0 }
                ]
            });
        });

    </script>

<?php $this->append() ?>
