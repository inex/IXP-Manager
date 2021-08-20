<div class="card mt-4">
    <div class="card-body">
        <h4>
            Common Link Settings:
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
                    ->blockHelp( "Choose the 'a side' switch." )
                ?>

                <?= Former::select( 'switch-b' )
                    ->id( 'switch-b' )
                    ->label( 'Switch B' )
                    ->required( true )
                    ->placeholder( 'Choose a switch' )
                    ->addClass( 'chzn-select switch-dd' )
                    ->dataValue( "b")
                    ->blockHelp( "Choose the 'b side' switch." )
                ?>
            </div>

            <div class="col-lg-6 col-md-12">
                <?= Former::select( 'duplex' )
                    ->id( 'duplex' )
                    ->label( 'Duplex' )
                    ->fromQuery( \IXP\Models\PhysicalInterface::$DUPLEX, 'name' )
                    ->placeholder( 'Choose a duplex' )
                    ->required( true )
                    ->select( 'full' )
                    ->addClass( 'chzn-select' )
                    ->blockHelp( "Duplex setting - must be the same on both sides. Informational unless you are provisioning your switches from IXP Manager." )
                ?>

                <?= Former::select( 'speed' )
                    ->label( 'Speed' )
                    ->id( 'speed' )
                    ->fromQuery( \IXP\Models\PhysicalInterface::$SPEED, 'name' )
                    ->required( true )
                    ->placeholder( 'Choose a speed' )
                    ->addClass( 'chzn-select' )
                    ->blockHelp( "Speed of the link(s) between the two switches. Each individual core link must have a matching speed on each end. IXP Manager only supports core bundles with multiple links if all links have the same speed." )

                ?>

                <?= Former::checkbox( 'auto-neg' )
                    ->label( 'Auto-Neg' )
                    ->value( 1 )
                    ->blockHelp( "Auto-negotiation setting - must be the same on both sides. Informational unless you are provisioning your switches from IXP Manager." )
                    ->check()
                ?>
            </div>
        </div>

        <div class="card former-help-text">
            <div class="card-body bg-light">
                You have a number of options when assigning a port:
                <ul>
                    <li>
                        If you have pre-wired the patch panel to a port, enter the switch and port here. As long as no customer has been
                        assigned to the switch port, the patch panel port will remain available but will be marked as connected to
                        the given switch port in the patch panel port list.
                    </li>
                </ul>

                If you need to reset these fields, just click either of the <em>Reset</em> button.
            </div>
        </div>
    </div>
</div>