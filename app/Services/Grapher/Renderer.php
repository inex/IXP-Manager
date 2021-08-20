<?php namespace IXP\Services\Grapher;

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

use IXP\Exceptions\Services\Grapher\RendererException;
use IXP\Services\Grapher;
use IXP\Services\Grapher\Graph as Graph;

use App;
use View;

/**
 * Grapher -> Renderer of the given graph
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Grapher
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Renderer
{
    /**
     * Style for future expansion
     * @var string
     */
    public const BOX_STYLE_LEGACY = 'legacy';

    /**
     * All styles
     * @array
     */
    public const BOX_STYLES = [
        self::BOX_STYLE_LEGACY,
    ];


    /**
     * Graph under consideration
     *
     * @var Graph
     */
    private $graph;


    /**
     * Constructor
     */
    public function __construct( Graph $g )
    {
        $this->graph = $g;
    }

    /**
     * Access for the graph object under consideration
     *
     * @return Graph
     */
    private function graph(): Graph
    {
        return $this->graph;
    }

    /**
     * Render the graph box
     *
     * @param string $style The style (See BOX_STYLES above)
     *
     * @return string
     *
     * @throws RendererException
     */
    public function box( string $style ): string
    {
        if( !in_array($style,self::BOX_STYLES) || !View::exists('services.grapher.renderer.box.'.$style) ) {
            throw new RendererException("No box style exists for: {$style}");
        }

        return View::make('services.grapher.renderer.box.'.$style)->with( [ 'graph' => $this->graph() ])->render();
    }

    /**
     * Alias for box renderer with legacy style
     *
     * @return string
     *
     * @throws RendererException
     */
    public function boxLegacy(): string
    {
        return $this->box( self::BOX_STYLE_LEGACY );
    }
}