<script>

    $( '.peers-table').DataTable({
        "autoWidth": false,
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