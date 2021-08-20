<?php

namespace IXP\Services\Grapher;

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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

/**
 * Backend -> abstract
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Grapher
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
abstract class Backend
{
    /**
     * Get a complete list of functionality that this backend supports.
     *
     * @return array
     */
    abstract public static function supports(): array;

    /**
     * Examines the provided graph object and determines if this backend is able to
     * process the request or not.
     *
     * {inheritDoc}
     *
     * @param Graph $graph
     * @return bool
     */
    public function canProcess( Graph $graph ): bool
    {
        // find what this backend can support
        $s = $this->supports();

        if( isset( $s[ $graph->lcClassType() ] )
            && ( isset($s[ $graph->lcClassType() ]['categories']) && in_array( $graph->category(), $s[ $graph->lcClassType() ]['categories'] ) )
            && ( isset($s[ $graph->lcClassType() ]['periods']   ) && in_array( $graph->period(),   $s[ $graph->lcClassType() ]['periods'   ] ) )
            && ( isset($s[ $graph->lcClassType() ]['protocols'] ) && in_array( $graph->protocol(), $s[ $graph->lcClassType() ]['protocols' ] ) )
            && ( isset($s[ $graph->lcClassType() ]['types']     ) && in_array( $graph->type(),     $s[ $graph->lcClassType() ]['types'     ] ) )
        ) {
            return true;
        }
        return false;
    }
}