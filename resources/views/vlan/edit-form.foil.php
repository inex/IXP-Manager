
<div class="well col-sm-12">
    <?= Former::open()->method( 'POST' )
        ->id( 'form' )
        ->action( action ( $t->controller.'@store' ) )
        ->customWidthClass( 'col-sm-3' )
    ?>

    <?= Former::text( 'name' )
        ->label( 'Name' )
        ->blockHelp( "" );
    ?>

    <?= Former::text( 'number' )
        ->label( '802.1q Tag' )
        ->blockHelp( "" );
    ?>

    <?= Former::select( 'infrastructureid' )
        ->label( 'Infrastructure' )
        ->fromQuery( $t->params[ 'infrastructure'], 'name' )
        ->placeholder( 'Choose an infrastructure' )
        ->addClass( 'chzn-select' );
    ?>

    <?= Former::text( 'config_name' )
        ->label( 'Config Name' )
        ->blockHelp( '' );
    ?>

    <?= Former::checkbox( 'private' )
        ->label( ' ' )
        ->text( 'Private VLAN between a subset of members' )
        ->checked_value( 1 )
        ->unchecked_value( 0 )
        ->blockHelp('' );
    ?>

    <?= Former::checkbox( 'peering_matrix' )
        ->label( ' ' )
        ->text( 'Include VLAN in the peering matrix (see help)' )
        ->checked_value( 1 )
        ->unchecked_value( 0 )
        ->blockHelp( "Selecting this checkbox means that this VLAN will appear on the <a href='http://ixp-master.dev/peering-matrix' target='_blank'>peering matrix</a>. 
        Note that this does not mean that this matrix will be populated. For that, you need to <a href='https://github.com/inex/IXP-Manager/wiki/Peering-Matrix'>configure
        sflow support for this</a>." );
    ?>

    <?= Former::checkbox( 'peering_manager' )
        ->label( ' ' )
        ->text( 'Include VLAN in the peering manager (see help)' )
        ->checked_value( 1 )
        ->unchecked_value( 0 )
        ->blockHelp( "Selecting this checkbox means that this VLAN will appear on the members' peering manager. 
        Note that this does not mean that it will be populated. For that, you need to <a href='https://github.com/inex/IXP-Manager/wiki/Peering-Matrix'>configure
        sflow support for this</a>." );
    ?>

    <?= Former::textarea( 'notes' )
        ->label( 'Notes' )
        ->rows( 5 )
        ->blockHelp( '' );
    ?>

    <?= Former::actions(
        Former::primary_submit( 'Save Changes' ),
        Former::default_link( 'Cancel' )->href( action ($t->controller.'@list') ),
        Former::success_button( 'Help' )->id( 'help-btn' )
    );
    ?>

    <?= Former::hidden( 'id' )
        ->value( $t->params[ 'object'] ? $t->params[ 'object']->getId() : '' )
    ?>

    <?= Former::close() ?>

</div>
