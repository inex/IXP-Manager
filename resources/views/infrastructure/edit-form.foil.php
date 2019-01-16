<div class="well col-sm-12">

    <?= Former::open()->method( 'POST' )
        ->id( 'form' )
        ->action( route( $t->feParams->route_prefix . '@store' ) )
        ->customInputWidthClass( 'col-sm-3' )
    ?>

    <?= Former::text( 'name' )
        ->label( 'Name' )
        ->blockHelp( "The name of this infrastructure. Displayed in a number of places. Examples at INEX are: INEX LAN1, INEX LAN2, INEX Cork." );
    ?>

    <?= Former::text( 'shortname' )
        ->label( 'Shortname' )
        ->blockHelp( "A lowercase single word to represent the infrastructure." );
    ?>

    <?= Former::checkbox( 'primary' )
        ->label( '&nbsp;' )
        ->text( 'Primary Infrastructure' )
        ->value( 1 )
        ->blockHelp( "Only one infrastructure can be primary. Setting this will unset this on all other infrastructures. Usually used to "
            . "signify an infrastructure where <em>everyone</em> connects such as a primary peering LAN." );
    ?>

    <?= Former::select( 'ixf_ix_id' )
        ->id( 'ixf_ix_id' )
        ->label( 'IX-F DB IX ID' )
        ->placeholder( 'Please wait, loading...' )
        ->blockHelp( "Identify your IXP from the <a href=\"http://ml.ix-f.net/\">IX Federation's database</a>. If it does not exist there, "
            . "<a href=\"https://www.euro-ix.net/\">contact the euro-ix secretariat</a>.<br><br>Note the local copy of this list is "
            . "cached for two hours. Use 'artisan cache:clear' to reset it.");
    ?>

    <?= Former::select( 'pdb_ixp' )
        ->id( 'pdb_ixp' )
        ->label( 'Peering DB IX ID' )
        ->placeholder( 'Please wait, loading...' )
        ->blockHelp( "Identify your IXP from <a href=\"https://www.peeringdb.com/\">PeeringDB</a>. If it does not exist there, "
            . "then you should add it yourself through their web interface.<br><br>Note the local copy of this list is "
            . "cached for two hours. Use 'artisan cache:clear' to reset it.");
    ?>

    <?= Former::actions(
        Former::primary_submit( $t->data['params']['isAdd'] ? 'Add' : 'Save Changes' )->id( 'btn-submit' )->disabled( true ),
        Former::default_link( 'Cancel' )->href( route($t->feParams->route_prefix . '@list') ),
        Former::success_button( 'Help' )->id( 'help-btn' )
    );
    ?>

    <?= Former::hidden( 'id' )
        ->value( $t->data[ 'params'][ 'object'] ? $t->data[ 'params'][ 'object']->getId() : '' )
    ?>

    <?= Former::close() ?>

</div>

