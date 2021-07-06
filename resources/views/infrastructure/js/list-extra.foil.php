
<script type="text/javascript">

    $('#infra_reg_banner').hide();

    $(document).ready(function() {
        let ixfids = [ <?php $ids = []; foreach( $t->data[ 'rows' ] as $row ) { $ids[] = $row[ 'ixf_ix_id' ] ?? -999; } echo implode( ', ', $ids ); ?> ];
        let showRegBanner = false;

        $.getJSON('<?= route( 'ixpmanager-users/ixf-ids' ) ?>', function () {
            console.log('Fetched registered IXP Manager instances from www.ixpmanager.org')
        })
            .done(function (ixps) {

                ixfids.forEach(function (id, idx) {
                    if (!ixps.includes(id) || id === -999) {
                        showRegBanner = true;
                    }
                });

                if (showRegBanner) {
                    $('#infra_reg_banner').slideDown();
                }

            })
            .fail(function () {
                console.log('FAILED to fetch registered IXP Manager instances from www.ixpmanager.org');
            });
    });

</script>
