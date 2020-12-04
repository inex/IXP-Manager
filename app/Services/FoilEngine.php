<?php

namespace IXP\Services;

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
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
use Illuminate\Contracts\View\Engine as EngineInterface;

use Foil\Engine as EngineFoil;

class FoilEngine implements EngineInterface
{
    /** @var PlatesEngine */
    private $engine;

    public function __construct( EngineFoil $engine )
    {
        $this->engine = $engine;
    }

    public function engine(): EngineFoil
    {
        return $this->engine;
    }

    /**
     * Get the evaluated contents of the view.
     *
     * @param  string  $path
     * @param  array   $data
     *
     * @return string
     */
    public function get( $path, array $data = array() )
    {
        return $this->engine->render( $path, $data );
    }
}