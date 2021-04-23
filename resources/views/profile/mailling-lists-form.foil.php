<?php if( config( 'mailinglists.enabled', false ) ): ?>
    <div class="col-lg-6 col-md-12">
        <h3>
            Your Mailing List Subscriptions
        </h3>
        <hr>
        <p>
            <?= config( "identity.orgname" ) ?> operates the below mailing lists to help us interact with our
            members and for our members to interact with each other.
        </p>
        <p>
            The below are your subscriptions for <strong><?= Auth::getUser()->email ?></strong>.
        </p>

        <?= Former::open()
            ->populate( $t->mailingListSubscriptions )
            ->method( 'post' )
            ->id( "mailing" )
            ->action( route ( "profile@update-mailing-lists" ) )
            ->actionButtonsCustomClass( "grey-box")
            ->customInputWidthClass( 'col-sm-10' );
        ?>

        <?php foreach( config( "mailinglists.lists") as $name => $ml ): ?>
            <?php if( !Auth::getUser()->customer->typeAssociate() || ( isset( $ml[ "associates"] ) && $ml[ "associates" ] ) ): ?>
                <?= Former::checkbox( 'ml_'. $name )
                    ->label( '' )
                    ->value( 1 )
                    ->inline()
                    ->check(isset( $t->mailingListSubscriptions[ $name ] ) && $t->mailingListSubscriptions[ $name ] )
                    ->text( "<strong>".$ml[ "name"] ."</strong> - " . $ml[ "desc" ]
                        . "(" . ( ( $ml[ "email" ] ) ? "<a href='mailto:" .$ml[ "email" ]. " '>" . $ml[ "email" ] . "</a> - " : '' )
                        . "<a target='_blank' href='" . $ml[ "archive" ] ."'>archives</a>)"
                    )
                ?>
            <?php endif; ?>
        <?php endforeach; ?>

        <?= Former::actions(
            Former::primary_submit( 'Update Subscriptions' )
        );
        ?>

        <?= Former::close() ?>
    </div>
<?php endif; ?>