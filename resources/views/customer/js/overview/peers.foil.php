<script>

    $( '.peers-table').DataTable({
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