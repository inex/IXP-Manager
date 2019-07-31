<script>

    $( '.peers-table').DataTable({
        stateSave: true,
        stateDuration : DATATABLE_STATE_DURATION,
        responsive: true,
        "columns": [
            null,
            null,
            <?php foreach( $t->peers[ "vlan" ] as $vlan ): ?>
                <?php if( isset( $t->peers[ "me" ][ 'vlaninterfaces' ][ $vlan->getNumber() ] ) ): ?>
                    { "orderable": false },
                <?php endif; ?>

            <?php endforeach; ?>
        ]
    });

</script>