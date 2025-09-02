<?php

namespace IXP\Console\Commands\Upgrade;

/*
 * Copyright (C) 2009 - 2025 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use IXP\Console\Commands\Command as IXPCommand;
use IXP\Models\Customer;
use IXP\Models\VirtualInterface;
use IXP\Models\VlanInterface;


/**
 * Tool to check max prefixes when upgrading to IXP Manager v7.1
 *
 * @author      Barry O'Donovan <barry@opensolutions.ie>
 * @package     IXP\Console\Commands\Upgrade
 * @copyright   Copyright (C) 2009 - 2025 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class MaxPrefixes_7_1_0 extends IXPCommand
{
    protected $signature = 'update:max-prefixes-7.1.0';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check max prefixes for potential issues when upgrading to IXP Manager v7.1';

    public function handle(): int {

        $this->line( <<<END_LINE
            
            In IXP Manager v7.1, we have made some changes to how max prefixes is handled. In previous
            versions, a max prefixes setting on a VLAN interface only took effect if it was greater than
            the global (as set on the customer) value. 
            
            This is not intuitive as we show allow the more specific setting on the VLAN interface, whether
            lower or higher than the global, take precedence. 
            
            This script will identify any settings on VLAN interfaces that are lower than the global setting,
            as these will start to take effect, and give you the option to clear the setting. 

            END_LINE
        );

        foreach( Customer::trafficking()->current()->get() as $c ) {

            /** @var VirtualInterface $vi */
            foreach( $c->virtualInterfaces as $vi ) {

                /** @var VlanInterface $vli */
                foreach( $vi->vlanInterfaces as $vli ) {

                    if( $vli->ipv4maxbgpprefix ) {
                        if( !$c->maxprefixes || $c->maxprefixes < $vli->ipv4maxbgpprefix ) {
                            $this->shouldChange( $c, $vli, 4 );
                        }
                    }

                    if( $vli->ipv6maxbgpprefix ) {
                        if( !$c->maxprefixesv6 || $c->maxprefixesv6 < $vli->ipv6maxbgpprefix ) {
                            $this->shouldChange( $c, $vli, 6 );
                        }
                    }
                }
            }
        }

        return 0;
    }


    private function shouldChange( Customer $c, VlanInterface $vli, int $proto ): void {

        [ $fnC, $fnV ] = match ( $proto ) {
            4 => [ 'maxprefixes',   'ipv4maxbgpprefix'],
            6 => [ 'maxprefixesv6', 'ipv6maxbgpprefix' ],
        };

        $this->line( "\n\n=========================================\n\n" );
        $this->line( $c->name . "  --  ipv$proto\n" );

        $this->line( "Customer Max:       " . $c->$fnC );
        $this->line( "VLAN Interface Max: " . $vli->$fnV );
        $this->line( "" );
        $this->line( "\tVLAN:    " . $vli->vlan->name );
        $this->line( "\tIPv4:    " . $vli->ipv4address?->address );
        $this->line( "\tIPv6:    " . $vli->ipv6address?->address );

        if( !$c->$fnC && $vli->$fnV ) {
            if( $this->confirm( "Max set on VLAN interface but not customer, move to customer and clear VLAN interface?") ) {
                $c->$fnC   = $vli->$fnV;
                $vli->$fnV = null;
                $c->save();
                $vli->save();
                $this->info("DONE");
            } else {
                $this->warn('No confirmation received, moving on...');
            }

        } else if( $c->$fnC && $vli->$fnV && $c->$fnC === $vli->$fnV ) {

            if( $this->confirm( "Equivalent max set on VLAN interface and customer, clear VLAN interface?") ) {
                $vli->$fnV = null;
                $vli->save();
                $this->info("DONE");
            } else {
                $this->warn('No confirmation received, moving on...');
            }

        } else if( $c->$fnC && $vli->$fnV && $c->$fnC < $vli->$fnV ) {

            if( $this->confirm( "VLAN interface has a value greater than the customer, copy to customer clear VLAN interface?") ) {
                $c->$fnC   = $vli->$fnV;
                $vli->$fnV = null;
                $c->save();
                $vli->save();
                $this->info("DONE");
            } else {
                $this->warn('No confirmation received, moving on...');
            }

        }

    }


}
