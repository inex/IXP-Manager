<?php $this->layout('services/lg/layout') ?>

<?php $this->section('title') ?>
    <small>BGP Protocol Summary</small>
<?php $this->append() ?>

<?php $this->section('content') ?>

<table class="table table-striped table-sm" id="bgpsummary">
    <thead class="thead-dark">
        <tr>
            <th>
                Neighbor
            </th>
            <th>
                Description
            </th>
            <th>
                ASN
            </th>
            <th>
                Table
            </th>
            <th>
                PfxLimit
            </th>
            <th>
                State/PfxRcd
            </th>
            <th>
                PfxExp
            </th>
            <th>
                Actions
            </th>
        </tr>
    </thead>
    <tbody>

        <?php if( !count( (array)$t->content->protocols ) ): ?>

            <tr>
                <td colspan="8">No BGP sessions found</td>
            </tr>

        <?php else: ?>

            <?php foreach( $t->content->protocols as $name => $p ): ?>

                <tr <?= $p->state == 'up' ? '' : 'class="warning"' ?>>
                    <td>
                        <?=$p->neighbor_address?>
                    </td>
                    <td>
                        <?= $p->description_short ?? $p->description ?? "" ?>
                    </td>
                    <td class="text-right">
                        <?= $p->neighbor_as ?>
                    </td>
                    <td>
                        <a href="<?= url('/lg') . '/' . $t->lg->router()->handle() ?>/routes/table/<?= $p->table ?>">
                            <?= $p->table ?>
                        </a>
                    </td>
                    <td class="text-right">
                        <?php if( isset($p->import_limit) and isset( $p->route_limit_at ) and $p->import_limit ): ?>
                            <span
                                <?php if( ( (float)$p->route_limit_at / $p->import_limit ) >= .9 ): ?>
                                    class="badge badge-danger"
                                <?php elseif( ( (float)$p->route_limit_at / $p->import_limit ) >= .8 ): ?>
                                    class="badge badge-warning"
                                <?php endif; ?>
                            >
                                <?= $p->route_limit_at ?>/<?= $p->import_limit ?>
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="text-right">
                        <?php if( $p->state != 'up' ): ?>
                            <?= $p->bgp_state ?>
                        <?php else: ?>
                            <?php if( is_int( $p->routes->imported ) and is_int( $t->content->api->max_routes ) and $p->routes->imported < $t->content->api->max_routes ): ?>
                                <a href="<?= url('/lg') . '/' . $t->lg->router()->handle() ?>/routes/protocol/<?= $name ?>">
                            <?php endif; ?>
                            <?= $p->routes->imported ?>
                            <?php if( is_int( $p->routes->imported ) and is_int( $t->content->api->max_routes ) and $p->routes->imported < $t->content->api->max_routes ): ?>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td class="text-right">
                        <?php if( $p->state == 'up' ): ?>
                            <?php if( is_int( $p->routes->exported ) and is_int( $t->content->api->max_routes ) and $p->routes->exported < $t->content->api->max_routes ): ?>
                                <a href="<?= url('/lg') . '/' . $t->lg->router()->handle() ?>/routes/export/<?= $name ?>">
                            <?php endif; ?>
                            <?= $p->routes->exported ?>
                            <?php if( is_int( $p->routes->exported ) and is_int( $t->content->api->max_routes ) and $p->routes->exported < $t->content->api->max_routes ): ?>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a class="btn btn-outline-secondary btn-sm" id="protocol_details-<?= $name ?>"
                            data-protocol="<?= $name ?>" title="<?= $p->description ?? "" ?>">
                            Details
                        </a>
                    </td>
                </tr>

            <?php endforeach; ?>
        <?php endif; ?>

    </tbody>
</table>

<div class="modal fade" id="protocol-info-modal" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">
                Protocol Details for <code><span id="title_p_name"></span></code>
            </h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <pre>
<span id="p_name"></span>    <span id="p_bird_protocol"></span>    <span="p_table"></span> <span id="p_state"></span>     <span id="p_state_changed"></span>  <span id="p_connection"></span>
  Description:    <span id="p_description"></span>
  Preference:     <span id="p_preference"></span>
  Input filter:   <span id="p_input_filter"></span>
  Output filter:  <span id="p_output_filter"></span>
  Import limit:   <span id="p_import_limit"></span>
    Action:       <span id="p_limit_action"></span>
  Routes:         <span id="p_routes_imported"></span> imported, <span id="p_routes_exported"></span> exported, <span id="p_routes_preferred"></span> preferred
  Route change stats:     received   rejected   filtered    ignored   accepted
    Import updates:     <span id="p_import_updates_received"></span> <span id="p_import_updates_rejected"></span> <span id="p_import_updates_filtered"></span> <span id="p_import_updates_ignored"></span> <span id="p_import_updates_accepted"></span>
    Import withdraws:   <span id="p_import_withdraws_received"></span> <span id="p_import_withdraws_rejected"></span>        --- <span id="p_import_withdraws_ignored"></span> <span id="p_import_withdraws_accepted"></span>
    Export updates:     <span id="p_export_updates_received"></span> <span id="p_export_updates_rejected"></span> <span id="p_export_updates_filtered"></span>        --- <span id="p_export_updates_accepted"></span>
    Export withdraws:   <span id="p_export_withdraws_received"></span>        ---        ---        --- <span id="p_export_withdraws_accepted"></span>
  BGP state:          <span id="p_bgp_state"></span>
    Neighbor address: <span id="p_neighbor_address"></span>
    Neighbor AS:      <span id="p_neighbor_as"></span>
    Neighbor ID:      <span id="p_neighbor_id"></span>
    Neighbor caps:    <span id="p_neighbor_capabilities"></span>
    Session:          <span id="p_bgp_session"></span>
    Source address:   <span id="p_source_address"></span>
    Route limit:      <span id="p_route_limit_at"></span>/<span id="p_import_limit2"></span>
    Hold timer:       <span id="p_hold_timer"></span>
    Keepalive timer:  <span id="p_keepalive"></span>
</pre>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
    </div>
  </div>
</div>

<?php $this->append() ?>

<?php $this->section('scripts') ?>

<script type="text/javascript">

    // http://stackoverflow.com/questions/12449890/reload-content-in-modal-twitter-bootstrap
    $(document).on('hidden.bs.modal', function (e) {
        $(e.target).removeData('bs.modal');
    });

    let protocols = <?= json_encode($t->content->protocols) ?>;
    function spacifyNumber( n, s ) {
        return "" + ' '.repeat( s - n.length ) + "" + String(n);
    }
    $('#bgpsummary').removeClass( 'display' ).addClass('table');

    $(document).ready(function() {

        $('#bgpsummary').DataTable({
            paging: false,
            order: [[ 2, "asc" ]],
            columnDefs: [
                { type: 'ip-address', targets: 0 },
                { type: 'string', targets: 0 },
                { type: 'int', targets: 0 },
                { type: 'string', targets: 0 },
                { type: 'string', targets: 0 },
                { type: 'int', targets: 0 },
                { type: 'int', targets: 0 }
            ]
        });
    });

    $('a[id|="sourceSelector"]').on( 'click', function(){
        if( $("#net").val().trim() == "" ) {
            return;
        }
        $("#submit").prop('disabled', true);
        $.get('<?= url('/lg') . '/' . $t->lg->router()->handle() ?>/route/' + encodeURIComponent($("#net").val().trim()) + '/' +
                source + '/' + encodeURIComponent( $("#source").val() ), function(html) {
            $('#route-modal .modal-content').html(html);
            $('#route-modal').modal('show', {backdrop: 'static'});
         });
        $("#submit").prop('disabled', false);
    });

    $('a[id|="protocol_details"]').on( 'click', function(){
        let pname = $(this).attr('data-protocol');
        let p = protocols[pname];
        $('#title_p_name'   ).html( pname );
        $('#p_name'         ).html( pname );
        $('#p_bird_protocol').html( p.bird_protocol );
        $('#p_table'        ).html( p.table );
        $('#p_state '       ).html( p.state );
        $('#p_state_changed').html( p.state_changed );
        $('#p_connection'   ).html( p.connection );
        $('#p_description'  ).html( p.description );
        $('#p_preference'   ).html( p.preference );
        $('#p_input_filter' ).html( p.input_filter );
        $('#p_output_filter').html( p.output_filter );
        $('#p_import_limit' ).html( p.import_limit );
        $('#p_limit_action' ).html( p.limit_action );
        $('#p_routes_imported'  ).html( p.routes.imported );
        $('#p_routes_exported'  ).html( p.routes.exported );
        $('#p_routes_preferred' ).html( p.routes.preferred );
        $('#p_import_updates_received'  ).html( spacifyNumber( p.route_changes.import_updates.received, 10 ) );
        $('#p_import_updates_rejected'  ).html( spacifyNumber( p.route_changes.import_updates.rejected, 10 ) );
        $('#p_import_updates_filtered'  ).html( spacifyNumber( p.route_changes.import_updates.filtered, 10 ) );
        $('#p_import_updates_ignored'   ).html( spacifyNumber( p.route_changes.import_updates.ignored, 10 ) );
        $('#p_import_updates_accepted'  ).html( spacifyNumber( p.route_changes.import_updates.accepted, 10 ) );
        $('#p_import_withdraws_received').html( spacifyNumber( p.route_changes.import_withdraws.received, 10 ) );
        $('#p_import_withdraws_rejected').html( spacifyNumber( p.route_changes.import_withdraws.rejected, 10 ) );
        $('#p_import_withdraws_ignored' ).html( spacifyNumber( p.route_changes.import_withdraws.ignored, 10 ) );
        $('#p_import_withdraws_accepted').html( spacifyNumber( p.route_changes.import_withdraws.accepted, 10 ) );
        $('#p_export_updates_received'  ).html( spacifyNumber( p.route_changes.export_updates.received, 10 ) );
        $('#p_export_updates_rejected'  ).html( spacifyNumber( p.route_changes.export_updates.rejected, 10 ) );
        $('#p_export_updates_filtered'  ).html( spacifyNumber( p.route_changes.export_updates.filtered, 10 ) );
        $('#p_export_updates_accepted'  ).html( spacifyNumber( p.route_changes.export_updates.accepted, 10 ) );
        $('#p_export_withdraws_received').html( spacifyNumber( p.route_changes.export_withdraws.received, 10 ) );
        $('#p_export_withdraws_accepted').html( spacifyNumber( p.route_changes.export_withdraws.accepted, 10 ) );
        $('#p_bgp_state'            ).html( p.bgp_state );
        $('#p_neighbor_address'     ).html( p.neighbor_address );
        $('#p_neighbor_as'          ).html( p.neighbor_as );
        $('#p_neighbor_id'          ).html( p.neighbor_id );
        if( p.neighbor_capabilities instanceof Array && p.neighbor_capabilities.length ) {
            $('#p_neighbor_capabilities').html( p.neighbor_capabilities.join(' ') );
        } else {
            $('#p_neighbor_capabilities').html( 'n/a' );
        }
        if( p.bgp_session instanceof Array && p.bgp_session.length ) {
            $('#p_bgp_session').html( p.bgp_session.join(' ') );
        } else {
            $('#p_bgp_session').html( 'n/a' );
        }
        $('#p_source_address'   ).html( p.source_address );
        $('#p_route_limit_at'   ).html( p.route_limit_at );
        $('#p_import_limit2'    ).html( p.import_limit );
        $('#p_hold_timer'       ).html( p.hold_timer );
        $('#p_keepalive'        ).html( p.keepalive );
        $('#protocol-info-modal').modal('show', {backdrop: 'static'});
    });
</script>

<?php $this->append() ?>
