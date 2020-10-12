<div id="core-links-area" class="mb-4 collapse">
    <?= Former::horizontal_open()->method( 'POST' )
        ->id( 'core-link-form' )
        ->action( route( 'core-link@store', [ 'cb' => $t->cb->id ] ) )
        ->customInputWidthClass( 'col-sm-6' )
        ->actionButtonsCustomClass( "grey-box")
    ?>
    <div id="core-links">
        <div class="card mt-4">
            <div class="card-header d-flex">
                <div class="mr-auto">
                    <h4>
                        New Core Link:
                    </h4>
                </div>
            </div>

            <div class="card-body row">
                <div class="col-sm-12">
                    <div id="message-new-cl"></div>
                    <?= Former::select( 'cl-details[1][sp-a]' )
                        ->id( "sp-a-1" )
                        ->label( 'Side A Switch Port' )
                        ->fromQuery( $t->switchPortsSideA, 'spname-sptype' )
                        ->placeholder( 'Choose a Switch Port' )
                        ->addClass( 'chzn-select new-core-link-input sp-dd' )
                        ->blockHelp( '' )
                        ->dataValue( "a" )
                        ->dataId( "1" );
                    ?>

                    <?= Former::hidden( 'cl-details[1][hidden-sp-a]' )
                        ->id( 'hidden-sp-a-1')
                        ->value( null )
                    ?>

                    <?= Former::select( 'cl-details[1][sp-b]' )
                        ->id( "sp-b-1" )
                        ->label( 'Side B Switch Port' )
                        ->fromQuery( $t->switchPortsSideB, 'spname-sptype' )
                        ->placeholder( 'Choose a Switch Port' )
                        ->addClass( 'chzn-select new-core-link-input sp-dd' )
                        ->blockHelp( '' )
                        ->dataValue( "b" )
                        ->dataId( "1" );
                    ?>

                    <?= Former::hidden( 'cl-details[1][hidden-sp-b]' )
                        ->id( 'hidden-sp-b-1')
                        ->value( null )
                    ?>

                    <?= Former::checkbox( 'cl-details[1][enabled-cl]' )
                        ->id( "enabled-1" )
                        ->label( 'Enabled' )
                        ->addClass( 'new-core-link-input' )
                        ->value( 1 )
                        ->class( "ml-1" )
                        ->check( true )
                    ?>

                    <?php if( $t->cb->typeECMP() ): ?>
                        <?= Former::checkbox( 'cl-details[1][bfd]' )
                            ->id( "bfd-1" )
                            ->label( 'BFD' )
                            ->addClass( 'new-core-link-input' )
                            ->class( "ml-1" )
                            ->value( 1 )
                        ?>

                        <?= Former::text( 'cl-details[1][subnet]' )
                            ->id( "cl-subnet-1" )
                            ->label( 'Subnet' )
                            ->addClass( 'new-core-link-input subnet' )
                            ->placeholder( '192.0.2.0/30' )
                        ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?= Former::hidden( 'core-bundle' )
            ->id( 'core-bundle')
            ->value( $t->cb->id )
        ?>

        <?=Former::actions(
            Former::primary_submit( 'Create new core link' )->id( 'new-core-links-submit-btn' )
        )->class('text-center');?>

        <?= Former::close() ?>
    </div>
</div>