<div class="well col-sm-12">

    <?= Former::open()->method( 'POST' )
        ->id( 'form' )
        ->action( action( $t->controller.'@store' ) )
        ->customWidthClass( 'col-sm-3' )
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
        ->checked_value( 1 )
        ->unchecked_value( 0 )
        ->blockHelp( "Only one infrastructure can be primary. Setting this will unset this on all other infrastructures. Usually used to "
            . "signify an infrastructure where <em>everyone</em> connects such as a primary peering LAN." );
    ?>

    <?= Former::select( 'ixf_ix_id' )
        ->id( 'ixf_ix_id' )
        ->label( 'IX-F DB IX ID' )
        ->placeholder( 'Please wait, loading...' )
        ->addClass( 'chzn-select' )
        ->blockHelp( "Identify your IXP from the <a href=\"http://ml.ix-f.net/\">IX Federation's database</a>. If it does not exist there, "
            . "<a href=\"https://www.euro-ix.net/\">contact the euro-ix secretariat</a>.<br><br>Note the local copy of this list is "
            . "cached for two hours. Use 'artisan cache:clear' to reset it.");
    ?>

    <?= Former::select( 'pdb_ixp' )
        ->id( 'pdb_ixp' )
        ->label( 'Peering DB IX ID' )
        ->placeholder( 'Please wait, loading...' )
        ->addClass( 'chzn-select' )
        ->blockHelp( "Identify your IXP from <a href=\"https://www.peeringdb.com/\">PeeringDB</a>. If it does not exist there, "
            . "then you should add it yourself through their web interface.<br><br>Note the local copy of this list is "
            . "cached for two hours. Use 'artisan cache:clear' to reset it.");
    ?>

    <?= Former::actions(
        Former::primary_submit( 'Save Changes' ),
        Former::default_link( 'Cancel' )->href( action ($t->controller.'@list') ),
        Former::success_button( 'Help' )->id( 'help-btn' )
    );
    ?>

    <?= Former::hidden( 'id' )
        ->value( $t->params[ 'inf'] ? $t->params[ 'inf']->getId() : '' )
    ?>

    <?= Former::close() ?>

</div>

