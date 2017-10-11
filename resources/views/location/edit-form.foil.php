<div class="well col-sm-12">

    <?= Former::open()->method( 'POST' )
        ->id( 'form' )
        ->action( action( $t->controller.'@store' ) )
        ->customWidthClass( 'col-sm-3' )
    ?>

    <?= Former::text( 'name' )
        ->label( 'Name' )
        ->blockHelp( "" );
    ?>

    <?= Former::text( 'shortname' )
        ->label( 'Shortname' )
        ->blockHelp( "" );
    ?>

    <?= Former::text( 'tag' )
        ->label( 'Tag' )
        ->blockHelp( "" );
    ?>

    <?= Former::textarea( 'address' )
        ->label( 'Address' )
        ->rows( 5 )
        ->style( 'width:500px' )
        ->blockHelp( '' );
    ?>

    <h3>
        NOC Details
    </h3>
    <hr>

    <?= Former::text( 'nocphone' )
        ->label( 'Phone' )
        ->blockHelp( "" );
    ?>

    <?= Former::text( 'nocfax' )
        ->label( 'Fax' )
        ->blockHelp( "" );
    ?>

    <?= Former::text( 'nocemail' )
        ->label( 'E-mail' )
        ->blockHelp( "" );
    ?>

    <h3>
        Office Details
    </h3>
    <hr>

    <?= Former::text( 'officephone' )
        ->label( 'Phone' )
        ->blockHelp( "" );
    ?>

    <?= Former::text( 'officefax' )
        ->label( 'Fax' )
        ->blockHelp( "" );
    ?>

    <?= Former::text( 'officeemail' )
        ->label( 'E-mail' )
        ->blockHelp( "" );
    ?>

    <?= Former::textarea( 'notes' )
        ->label( 'Notes' )
        ->rows( 10 )
        ->style( 'width:500px' )
        ->blockHelp( '' );
    ?>

    <h3>
        NOC Details
    </h3>
    <hr>

    <?= Former::select( 'pdb_facility_id' )
        ->id( 'pdb_facility_id' )
        ->label( 'PeeringDB Facility' )
        ->placeholder( 'Please wait, loading...' )
        ->addClass( 'chzn-select' )
        ->blockHelp( "");
    ?>

    <?= Former::actions(
        Former::primary_submit( 'Save Changes' )->id( 'btn-submit' )->disabled( true ),
        Former::default_link( 'Cancel' )->href( action ($t->controller.'@list') ),
        Former::success_button( 'Help' )->id( 'help-btn' )
    );
    ?>

    <?= Former::hidden( 'id' )
        ->value( $t->params[ 'object'] ? $t->params[ 'object']->getId() : '' )
    ?>

    <?= Former::close() ?>

</div>

