<?php $this->layout('services/lg/layout') ?>

<?php $this->section('title') ?>
    <small>Routes for <?= ucwords($t->source) ?> <code><?= $t->name ?></code></small>
<?php $this->append() ?>

<?php $this->section('content') ?>

    <div class="card mb-4">
        <div class="card-body">
            Key: <span class="badge badge-success">P</span>
            - Primary / active route.
            <span class="badge badge-warning">N</span>
            - Inactive route.
        </div>
    </div>

    <table class="table table-striped" id="routes">
        <thead class="thead-dark">
            <tr>
                <th>
                    Network
                </th>
                <th>
                    Next Hop
                </th>
                <th></th>
                <th>
                    Metric
                </th>
                <th>
                    Communities?
                </th>
                <th>
                    AS Path
                </th>
                <th></th>
            </tr>
        </thead>
        <tbody>

            <?php if( !count( $t->content->routes ) ): ?>

                <tr><td colspan="6">No routes found</td></tr>

            <?php else: ?>

                <?php foreach( $t->content->routes as $r ): ?>

                    <tr>
                        <td>
                            <?php
                                // need to split the ip/netmask so we don't urlencode() the '/' between them:
                                list( $ip, $mask ) = explode( '/', $r->network );
                            ?>
                            <a href="<?= url('/lg') . '/' . $t->lg->router()->handle() ?>/route/<?= urlencode($ip) ?>/<?= $mask ?>/table/master"
                                    data-toggle="modal" data-target="#route-modal">
                                <?= $r->network ?>
                            </a>
                        </td>
                        <td>
                            <?= $r->gateway ?>
                        </td>
                        <td>
                            <?php if( $r->primary ): ?>
                                <span class="badge badge-success">P</span>
                            <?php else: ?>
                                <span class="badge badge-warning">N</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $r->metric ?></td>
                        <td>
                            <span class="badge badge-secondary">
                                <?php if( isset( $r->bgp->communities ) ): ?>
                                    <?= count( $r->bgp->communities ) ?>
                                <?php else: ?>
                                    0
                                <?php endif; ?>
                            </span>

                            <?php if( isset( $r->bgp->large_communities ) ): ?>
                                <span class="badge badge-secondary">LC:
                                    <?= count( $r->bgp->large_communities ) ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if( isset($r->bgp->as_path) ): ?>
                                <?= implode(' ', $r->bgp->as_path) ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a class="btn btn-outline-secondary btn-sm" data-toggle="modal"
                                href="<?= url('/lg') . '/' . $t->lg->router()->handle() ?>/route/<?= urlencode( explode('/',$r->network)[0] ) ?>/<?= explode('/',$r->network)[1] ?>/<?= $t->source ?>/<?= $t->name ?>"
                                data-target="#route-modal">Details</a>
                        </td>
                    </tr>

                <?php endforeach; ?>

            <?php endif; ?>

        </tbody>
    </table>

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
