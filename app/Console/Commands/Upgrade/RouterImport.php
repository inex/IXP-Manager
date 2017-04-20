<?php

namespace IXP\Console\Commands\Upgrade;

/*
 * Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee.
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
use Entities\{
    Router as RouterEntity,
    Vlan as VlanEntity
};
use D2EM;

/**
 * Class ImportRouters - tool to copy Router from the read file config/routers to the table router
 *
 * @author Yann Robin <yann@islandbridgenetworks.ie>
 * @author Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @package IXP\Console\Commands\Upgrade
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class RouterImport extends IXPCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'router:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import router definitions from config/routers.php (upgrade to v4.4 requirement)';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        if( !$this->confirm( 'Are you sure you wish to proceed? This command will import the router definitions from config/routers.php to the new dedicated router table in the database ' ) ) {
            return 1;
        }

        // Get the routers list from the file config/routers
        if( !( $routers = config( 'routers') ) ) {
            $this->error( 'No router definitions have been found' );
            return 2;
        }

        $found    = 0;
        $imported = 0;
        foreach( $routers as $index => $router) {
            $found++;

            if( D2EM::getRepository( RouterEntity::class )->findBy( [ 'handle' => $index ] ) ) {
                $this->alert( "Router with handle {$index} already exists in database, skipping." );
                continue;
            }

            switch ( $router['type'] ) {
                case 'RS':
                    $type = RouterEntity::TYPE_ROUTE_SERVER;
                    break;
                case 'RC':
                    $type = RouterEntity::TYPE_ROUTE_COLLECTOR;
                    break;
                case 'AS112':
                    $type = RouterEntity::TYPE_AS112;
                    break;
                default:
                    $type = RouterEntity::TYPE_OTHER;
            }

            /** @var VlanEntity $vlan */
            if( !($vlan = D2EM::getRepository( VlanEntity::class )->find( $router[ 'vlan_id' ] )  ) ){
                $this->alert( 'The VLAN with the ID  ' . $router[ 'vlan_id' ] . ' does not exist in the database! The router '. $index . ' can not be imported.' );
                continue;
            }

            /** @var RouterEntity $rt */
            $rt = new RouterEntity();
            $rt->setVlan( $vlan );
            $rt->setHandle( $index );
            $rt->setProtocol( $router[ 'protocol' ] );
            $rt->setType( $type );
            $rt->setName( $router[ 'name' ] );
            $rt->setShortName( $router[ 'shortname' ] );
            $rt->setRouterId( $router[ 'router_id' ] );
            $rt->setPeeringIp( $router[ 'peering_ip' ] );
            $rt->setAsn( $router[ 'asn' ] );
            $rt->setSoftware( array_search( strtolower( $router[ 'software' ] ),array_map('strtolower', RouterEntity::$SOFTWARES ) ) );
            $rt->setMgmtHost( $router[ 'mgmt_ip' ] );
            $rt->setApi( $router[ 'api' ] ?? '' );
            $rt->setApiType( array_search( strtolower( $router[ 'api_type' ] ?? '' ), array_map('strtolower', RouterEntity::$API_TYPES ) ) );
            $rt->setLgAccess( $router[ 'lg_access' ] ?? false );
            $rt->setQuarantine( $router[ 'quarantine' ] ?? false );
            $rt->setBgpLc( $router[ 'bgp_lc' ] ?? false );
            $rt->setSkipMd5( $router['skip_md5'] ?? false );
            $rt->setTemplate( $router[ 'template' ] );
            D2EM::persist($rt);
            $imported++;
        }

        D2EM::flush();

        $this->info( "Migration completed successfully. Found {$found}, imported {$imported}." );
        return 0;
    }
}
