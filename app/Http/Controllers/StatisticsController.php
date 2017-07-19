<?php
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

namespace IXP\Http\Controllers;

use App, D2EM;

use IXP\Services\Grapher\Graph;

use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;

use Entities\{
    Customer as CustomerEntity,
    Vlan as VlanEntity
};

/**
 * Statistics Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Statistics
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class StatisticsController extends Controller
{

    /**
     * Display all member graphs
     *
     * @return  View
     */
    public function members() : View {

//        $this->setIXP();
//        $this->setInfrastructure();
//        $this->setCategory();
//        $category = $this->setCategory();
//        $period   = $this->setPeriod();

        $grapher = App::make('IXP\Services\Grapher');

        $custs = D2EM::getRepository( CustomerEntity::class )->getCurrentActive( false, true, false );

//        if( $this->infra instanceof Entities\Infrastructure ) {
//            $custs = $this->getD2R( 'Entities\Customer')->filterForInfrastructure( $custs, $this->infra );
//        }

        $graphs = [];

        foreach( $custs as $c ) {
            $graphs[] = $grapher->customer( $c )
                ->setType(     Graph::TYPE_PNG )
                ->setProtocol( Graph::PROTOCOL_ALL )
                ->setCategory( Graph::CATEGORY_BITS )
                ->setPeriod( Graph::PERIOD_DAY );
        }

        return view( 'statistics/members' )->with([
            'graphs'       => $graphs,
        ]);
    }
}
