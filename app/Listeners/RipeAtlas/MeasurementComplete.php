<?php

namespace IXP\Listeners\RipeAtlas;

/*
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use App;

use IXP\Events\RipeAtlas\MeasurementComplete as MeasurementCompleteEvent;
use IXP\Services\RipeAtlas\Interpretor;

/**
 * MeasurementComplete
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Listeners\RipeAtlas
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class MeasurementComplete
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(){}

    public function handle( MeasurementCompleteEvent $event ): void
    {
        // get the measurement ORM entity
        $am = $event->atlasMeasurement;

        // if there's already a result, delete it as this is a re-run
        if( $r = $am->atlasResult() ) {
            $r->delete();
        }

        // interpret the measurement
        $result = App::make(Interpretor::class )->interpret( $am );

        $result->update( [ 'measurement_id' => $am->id ] );
    }
}