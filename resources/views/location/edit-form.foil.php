<div class="well col-sm-12">

    <?= Former::open()->method( 'POST' )
        ->id( 'form' )
        ->action( action( $t->controller.'@store' ) )
        ->customWidthClass( 'col-sm-3' )
    ?>

    <?= Former::text( 'name' )
        ->label( 'Name' )
        ->blockHelp( "The name of the data centre / facility / point of presence (PoP)." );
    ?>

    <?= Former::text( 'shortname' )
        ->label( 'Shortname' )
        ->blockHelp( "A short name, ideally less than 10 characters, that can be substituted for the full name above where space is contrained." );
    ?>

    <?= Former::text( 'tag' )
        ->label( 'Tag' )
        ->blockHelp( "Typically a lower case, 3-4 letter identifier. For example, INEX uses tags as part of our switch hostname to identify its facility." );
    ?>

    <?= Former::select( 'pdb_facility_id' )
        ->id( 'pdb_facility_id' )
        ->label( 'PeeringDB Facility' )
        ->placeholder( 'Please wait, loading...' )
        ->addClass( 'chzn-select' )
        ->blockHelp( "If listed, identify the facility from PeeringDB's facility list. If it is not listed here and you believe it should be, "
            . "then please contact PeeringDB directly. Note also that IXP Manager caches this data for a number of hours - so an "
            . "<code>artisan cache:clear</code> is required if you get your facility listed and IXP Manager still does not have it." );
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

    <h3>
        Notes
    </h3>
    <hr>

    <?= Former::textarea( 'notes' )
        ->label( '&nbsp;' )
        ->rows( 10 )
        ->style( 'width:500px' )
        ->blockHelp( '' );
    ?>

    <?php if( !$t->data['params']['isAdd'] ): ?>
        <?= Former::hidden( 'pdb_facility_id' )
            ->value( $t->data[ 'params'][ 'object']->getPdbFacilityId() )
        ?>
    <?php endif; ?>


    <?= Former::actions(
        Former::primary_submit( $t->data['params']['isAdd'] ? 'Add' : 'Save Changes' )->id( 'btn-submit' )->disabled( true ),
        Former::default_link( 'Cancel' )->href( action ($t->controller.'@list') ),
        Former::success_button( 'Help' )->id( 'help-btn' )
    );
    ?>

    <?= Former::hidden( 'id' )
        ->value( $t->data[ 'params'][ 'object'] ? $t->data[ 'params'][ 'object']->getId() : '' )
    ?>

    <?= Former::close() ?>

</div>

