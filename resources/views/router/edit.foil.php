<?php $this->layout( 'layouts/ixpv4' ) ?>

<?php $this->section( 'title' ) ?>
    <a href="<?= url( 'router/list' )?>">Router</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>Edit</li>
<?php $this->append() ?>


<?php $this->section( 'content' ) ?>

    <?= $t->alerts() ?>

    <?= Former::open()->method( 'POST' )
        ->action( url( 'router/store' ) )
        ->customWidthClass( 'col-sm-3' )
        ->addClass( 'col-md-10' );
    ?>

    <?= Former::text( 'handle' )
        ->label( 'Handle' )
        ->blockHelp( '');

    ?>

    <?= Former::select( 'vlan' )
        ->label( 'Vlan' )
        ->fromQuery( $t->vlans, 'name' )
        ->placeholder( 'Choose a Vlan' )
        ->addClass( 'chzn-select' )
        ->blockHelp( 'bar' );

    ?>

    <?= Former::select( 'protocol' )
        ->label( 'Protocol' )
        ->fromQuery( $t->protocols )
        ->placeholder( 'Choose a Protocol' )
        ->addClass( 'chzn-select' )
        ->blockHelp( '' );
    ?>

    <?= Former::select( 'type' )
        ->label( 'Type' )
        ->fromQuery( $t->types )
        ->placeholder( 'Choose a type' )
        ->addClass( 'chzn-select' )
        ->blockHelp( '' );
    ?>

    <?= Former::text( 'name' )
        ->label( 'Name' )
        ->blockHelp( '' );
    ?>

    <?= Former::text( 'shortname' )
        ->label( 'ShortName' )
        ->maxlength( 20 )
        ->blockHelp( '' );
    ?>

    <?= Former::text( 'router_id' )
        ->label( 'Router ID' )
        ->blockHelp( '' );
    ?>

    <?= Former::text( 'peering_ip' )
        ->label( 'Peering IP' )
        ->blockHelp( '' );
    ?>

    <?= Former::text( 'asn' )
        ->label( 'ASN' )
        ->blockHelp( '' );
    ?>

    <?= Former::select( 'software' )
        ->label( 'Software' )
        ->fromQuery( $t->softwares )
        ->placeholder( 'Choose a software' )
        ->addClass( 'chzn-select' )
        ->blockHelp( '' );
    ?>

    <?= Former::text( 'mgmt_host' )
        ->label( 'MGMT Host' )
        ->blockHelp( '' );
    ?>

    <?= Former::select( 'api_type' )
        ->label( 'API Type' )
        ->fromQuery( $t->apiTypes )
        ->placeholder( 'Choose an API Type' )
        ->addClass( 'chzn-select' )
        ->blockHelp( '' );
    ?>

    <?= Former::text( 'api' )
        ->label( 'API' )
        ->blockHelp( '' );
    ?>

    <?= Former::select( 'lg_access' )
        ->label( 'LG Access' )
        ->fromQuery( $t->lgAccess )
        ->placeholder( 'Choose LG Access' )
        ->addClass( 'chzn-select' )
        ->blockHelp( '' );
    ?>

    <?= Former::checkbox( 'quarantine' )
        ->label( 'Quarantine' )
        ->blockHelp( '' );

    ?>

    <?= Former::checkbox( 'bgp_lc' )
        ->label( 'BGP LC' )
        ->blockHelp( '' );
    ?>

    <?= Former::checkbox( 'skip_md5' )
        ->label( 'Skip MD5' )
        ->blockHelp( '' );
    ?>

    <?= Former::text( 'template' )
        ->label( 'Template' )
        ->blockHelp( '' );
    ?>

    <?=Former::actions( Former::primary_submit( 'Save Changes' ),
        Former::default_link( 'Cancel' )->href( url( 'router/list/' ) ),
        Former::success_button( 'Help' )->id( 'help-btn' )
    );?>

    <?= Former::hidden( 'id' )
        ->value( $t->rt ? $t->rt->getId() : '' )
    ?>

<?= Former::close() ?>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
<script>
    /**
     * hide the help block at loading
     */
    $('p.help-block').hide();

    /**
     * display / hide help sections on click on the help button
     */
    $( "#help-btn" ).click( function() {
        $( "p.help-block" ).toggle();
    });

</script>
<?php $this->append() ?>