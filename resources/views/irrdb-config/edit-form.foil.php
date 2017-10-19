
<div class="well col-sm-12">
    <?= Former::open()->method( 'POST' )
        ->id( 'form' )
        ->action( action ( $t->controller.'@store' ) )
        ->customWidthClass( 'col-sm-3' )
    ?>

    <?= Former::text( 'host' )
        ->label( 'Host' )
        ->blockHelp( "The IRRDB server. Usually <code>whois.radb.net</code> but we have also used "
            . "<code>whois.ripe.net</code>, "
            . "<code>whois.lacnic.net</code>, "
            . "<code>whois.apnic.net</code> and "
            . "<code>rr.level3.net</code> in specific cases."
        );
    ?>

    <?= Former::text( 'protocol' )
        ->label( 'Protocol' )
        ->blockHelp( "This is no longer used as bgpq3 does not require this parameter and we will most likely deprecate this in time.<br><br>"
            . "For now, if querying RADB, set it to <code>irrd</code>; otherwise use <code>ripe</code>." );
    ?>

    <?= Former::text( 'source' )
        ->label( 'Source' )
        ->blockHelp( "Which IRRDB dataset source(s) to use as a comma separated list. E.g. bgpq3 recommend <code>RADB,RIPE,APNIC</code>.<br><br>"
            . "A set of supported datasets supported by RADB <a href='http://www.radb.net/query/?advanced_query=1'>can be found here</a>." );
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
