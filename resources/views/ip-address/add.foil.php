<?php $this->layout( 'layouts/ixpv4' ) ?>



<?php $this->section( 'title' ) ?>
    <a href="<?= route ( 'ip-address@list', [ 'protocol' => $t->protocol, 'vlanid' => request()->input( 'vlan' ) ] ) ?>">IP Addresses</a>
<?php $this->append() ?>



<?php $this->section( 'page-header-postamble' ) ?>
    <li>
        Add IPv<?= $t->protocol ?> Address
    </li>
<?php $this->append() ?>



<?php $this->section( 'page-header-preamble' ) ?>
    <li class="pull-right">
        <div class="btn-group btn-group-xs" role="group">
            <a type="button" class="btn btn-default" href="<?= route ( 'ip-address@list', [ 'protocol' => $t->protocol, 'vlanid' => request()->input( 'vlan' ) ] ) ?>" title="list">
                <span class="glyphicon glyphicon-list"></span>
            </a>
        </div>
    </li>
<?php $this->append() ?>


<?php $this->section( 'content' ) ?>

<div class="row">

    <div class="col-sm-12">

        <?= $t->alerts() ?>

        <?= Former::open()->method( 'post' )
            ->action( route ('ip-address@store' ) )
            ->customInputWidthClass( 'col-sm-6' )
            ->addClass( 'col-md-10' );
        ?>

        <?= Former::select( 'vlan' )
            ->label( 'Vlan' )
            ->fromQuery( $t->vlans, 'name' )
            ->placeholder( 'Choose a VLAN...' )
            ->addClass( 'chzn-select' )
            ->blockHelp( 'Select the VLAN to add the new IP addresses to.' )
        ?>


        <?= Former::text( 'network' )
            ->label( 'Network' )
            ->placeholder( $t->protocol == 6 ? '2001:db8:23::100/121' : '192.0.2.24/28' )
            ->blockHelp( 'Enter a subnet is CIDR format. /' . ( $t->protocol == 6 ? '128' : '32' ) . ' is optional for a single address.' )
        ?>


        <?php if( $t->protocol == 6 ): ?>

            <?= Former::checkbox( 'decimal' )
                ->label( '&nbsp;' )
                ->text( 'Enter decimal values only' )
                ->value( 1 )
                ->blockHelp( "Typically IXs allocate a customer an IPv6 address such that the last block matches the last block of the IPv4 address. "
                    . "If you check this, IXP Manager will add the number of addresses as indicated by the CIDR block size but skip over any "
                    . "addresses containing <code>a-f</code> characters."
                )
            ?>

            <div id="div-overflow" style="display: none;">
                <?= Former::checkbox( 'overflow' )
                    ->label( '&nbsp;' )
                    ->text( 'Overflow network bound for decimal-only values' )
                    ->value( 1 )
                    ->check()
                    ->blockHelp( "If you are adding decimal addresses only, you would typically want the number of addresses created to match the "
                        . "size of the subnet even if it overflows the subnet bounds. Unchecking this will limit the decimal addresses create to the subnet."
                    )
                ?>
            </div>

        <?php else: ?>

            <?= Former::hidden( 'decimal' )->value( '0' ) ?>
            <?= Former::hidden( 'overflow' )->value( '0' ) ?>

        <?php endif; ?>

        <?= Former::checkbox( 'skip' )
            ->label( '&nbsp;' )
            ->text( 'Skip over existing addresses without throwing an error' )
            ->value( 1 )
            ->check()
            ->blockHelp( 'When adding a range of addresses, some may already exist in the database (created during provisioning a VLAN interface, previously added, etc.) '
                . 'Checking this will just skip over any addresses that already exist and only add the new ones.' );
        ?>

        <?=Former::actions( Former::primary_submit( 'Add Addresses' ),
            Former::default_link( 'Cancel' )->href( route ( 'ip-address@list', [ 'protocol' => $t->protocol, 'vlanid' => request()->input( 'vlan' ) ] ) ),
            Former::success_button( 'Help' )->id( 'help-btn' )
        );?>

        <?= Former::close() ?>

        <div style="clear: both"></div>

        <br><br>
        <div class="well">
            <h3>Adding IPv<?= $t->protocol ?> Addresses</h3>

            <p>
                IP addresses are added by specifying a subnet of addresses to add in CIDR notation. The only exception is adding a single
                IP address in which case the <code><?= $t->protocol == 6 ? '/128' : '/32' ?></code> is optional.
            </p>

            <?php if( $t->protocol == 6 ): ?>

                <p>
                    Here is an example - if you wanted to add 8 sequential IPv6 addresses starting from <code>2001:db8:32::64</code>, you would enter the
                    following in the <em>Network</em> inout box above: <code>2001:db8:32::64/125</code>. This would then add the following 8 IP addresses:
                </p>

                <pre>2001:db8:32::64
2001:db8:32::65
2001:db8:32::66
2001:db8:32::67
2001:db8:32::68
2001:db8:32::69
2001:db8:32::6a
2001:db8:32::6b</pre>

                <p>
                    If <em>Enter decimal values only</em> was checked above, the following addresses would instead have been entered:
                </p>

                <pre>2001:db8:32::64
2001:db8:32::65
2001:db8:32::66
2001:db8:32::67
2001:db8:32::68
2001:db8:32::69
2001:db8:32::70 [only if 'Overflow network bound for decimal-only values' is checked]
2001:db8:32::71 [only if 'Overflow network bound for decimal-only values' is checked]</pre>


            <?php else: ?>

                <p>
                    Here is an example - if you wanted to add 8 sequential IPv4 addresses starting from <code>192.0.2.64</code>, you would enter the
                    following in the <em>Network</em> inout box above: <code>192.0.2.64/29</code>. This would then add the following 8 IP addresses:
                </p>

                <pre>192.0.2.64
192.0.2.65
192.0.2.66
192.0.2.67
192.0.2.68
192.0.2.69
192.0.2.70
192.0.2.71</pre>

            <?php endif; ?>

            <p>
                To prevent you accidentally populating your database with a ridiculous number of IP addresses, there is a lower subnet
                bound of <code>/<?= $t->protocol == 6 ? '120' : '24' ?></code>. If you need to add more than this, just add them in batches.
            </p>

        </div>


    </div>

</div>





<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'ip-address/js/add.foil.js' ) ?>
<?php $this->append() ?>

