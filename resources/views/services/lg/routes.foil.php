<?php $this->layout('services/lg/layout') ?>

<?php $this->section('title') ?>
    <small>Routes for <?= ucwords( $t->source ) ?> <code><?= $t->name ?></code></small>
<?php $this->append() ?>

<?php $this->section('content') ?>

    <div class="card mb-4">
        <div class="card-body">
            <?php if( $t->source ?? false ): ?>
                <b>Routes <?= $t->source === 'export to protocol' ? 'exported to protocol' : 'from ' . $t->source ?>: <code><?= $t->name ?></code>.</b>
            <?php endif; ?>
            
            <b>Key:</b> <span class="badge badge-success">P</span>
            - Primary / active route.
            <span class="badge badge-warning">N</span>
            - Inactive route.
            <i class="fa fa-exclamation-triangle"></i>
            - Blocked / filtered route.
        </div>
    </div>

    <table class="table table-striped table-sm text-monospace"  style="font-size: 14px;" id="routes">
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
                    Metric&nbsp;
                </th>
                <th>
                    Communities?&nbsp;
                </th>
                <th>
                    AS Path
                </th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php if( !count( $t->content->routes ) ): ?>
                <tr>
                  <td colspan="6">No routes found</td>
                </tr>
            <?php else: ?>
                <?php foreach( $t->content->routes as $r ): ?>
                    <?php
                        // any blocked routes?
                        $blocked = false;
                        if( isset( $r->bgp->large_communities ) ) {
                            foreach( $r->bgp->large_communities as $lc ) {
                                if( $lc[0] == $t->lg->router()->asn && $lc[1] == 1101 ) {
                                    $blocked = true;
                                    break;
                                }
                            }
                        }
                    ?>

                    <tr>
                        <td>
                            <?php
                                // need to split the ip/netmask so we don't urlencode() the '/' between them:
                                list( $ip, $mask ) = explode( '/', $r->network );
                            ?>
                            <a href="<?= url('/lg') . '/' . $t->lg->router()->handle ?>/route/<?= urlencode($ip) ?>/<?= $mask ?>/table/master<?= (int)$t->lg->router()->software === \IXP\Models\Router::SOFTWARE_BIRD2 ? $t->lg->router()->protocol()[-1] : '' ?>"
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

                                <?= !$blocked ? '' : '<i class="fa fa-exclamation-triangle"></i>' ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if( isset( $r->bgp->as_path ) ): ?>
                                <?php foreach( $r->bgp->as_path as $asp ): ?>
                                    <?= $t->asNumber( $asp, false ) ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a class="btn btn-white btn-sm" style="font-size: 14px;" data-toggle="modal"
                                href="<?= url('/lg') . '/' . $t->lg->router()->handle ?>/route/<?= urlencode( explode('/',$r->network)[0] ) ?>/<?= explode('/',$r->network)[1] ?>/<?= $t->source == 'export to protocol' ? 'export' : $t->source ?>/<?= $t->name ?>"
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
        $('#routes').removeClass( 'display' ).addClass( 'table' );

        $(document).ready(function() {
            $('#routes').DataTable({
                stateSave: true,
                stateDuration : DATATABLE_STATE_DURATION,
                paging: false,
                order: [[ 0, "asc" ]],
                columnDefs: [
                    { type: 'ip-address', targets: 0 },
                    { type: 'ip-address', targets: 0 },
                    { type: 'int', targets: 0 },
                    { type: 'string', targets: 0 }
                ]
            });

            $('body').on('click', '[data-toggle="modal"]', function() {
                $( $( this ).data( "target" )+' .modal-content').html( `
                    <div class="text-center">
                        <div class="spinner-border m-5" style="width: 5rem; height: 5rem;" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                ` );

                $( $( this ).data( "target" ) + ' .modal-content').load( $( this ).attr( 'href' ) );
            });
        });

    </script>

<?php $this->append() ?>