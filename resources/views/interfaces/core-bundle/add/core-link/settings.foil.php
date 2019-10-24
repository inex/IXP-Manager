<div class="card mt-4">
    <div class="card-body">
        <h4>
            Common Link Settings :
        </h4>
        <hr>
        <div id="message-cl" class="message"></div>

        <div class="row">
            <div class="col-lg-6 col-md-12">

                <?= Former::select( 'switch-a' )
                    ->id( 'switch-a' )
                    ->label( 'Switch A' )
                    ->fromQuery( $t->switches, 'name' )
                    ->required( true )
                    ->placeholder( 'Choose a switch' )
                    ->addClass( 'chzn-select switch-dd' )
                    ->dataValue( "a")
                ?>

                <?= Former::select( 'switch-b' )
                    ->id( 'switch-b' )
                    ->label( 'Switch B' )
                    ->required( true )
                    ->placeholder( 'Choose a switch' )
                    ->addClass( 'chzn-select switch-dd' )
                    ->dataValue( "b")
                ?>

            </div>

            <div class="col-lg-6 col-md-12">

                <?= Former::select( 'duplex' )
                    ->id( 'duplex' )
                    ->label( 'Duplex' )
                    ->fromQuery( Entities\PhysicalInterface::$DUPLEX, 'name' )
                    ->placeholder( 'Choose a duplex' )
                    ->required( true )
                    ->select( 'full' )
                    ->addClass( 'chzn-select' )
                ?>

                <?= Former::select( 'speed' )
                    ->label( 'Speed' )
                    ->id( 'speed' )
                    ->fromQuery( Entities\PhysicalInterface::$SPEED, 'name' )
                    ->required( true )
                    ->placeholder( 'Choose a Speed' )
                    ->addClass( 'chzn-select' )

                ?>

                <?= Former::checkbox( 'auto-neg' )
                    ->label( 'Auto-Neg' )
                    ->value( 1 )
                    ->check()
                ?>

            </div>

        </div>

        <div class="card former-help-text">
            <div class="card-body bg-light">
                You have a number of options when assigning a port:

                <ul>
                    <li>
                        If you have pre-wired the patch panel to a port, enter the switch and port here. So long as no customer has been
                        assigned to the switch port, the patch panel port will remain available but will be marked as connected to
                        the given switch port in the patch panel port list.
                    </li>
                </ul>

                If you need to reset these fields, just click either of the <em>Reset</em> button.
            </div>
        </div>
    </div>
</div>