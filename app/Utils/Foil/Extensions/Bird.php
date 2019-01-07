<?php namespace IXP\Utils\Foil\Extensions;

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

use Foil\Contracts\ExtensionInterface;

/**
 * Bird -> Renderer view extensions
 *
 * See: http://www.foilphp.it/docs/EXTENDING/CUSTOM-EXTENSIONS.html
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Grapher
 * @package    IXP\Services\Grapher
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Bird implements ExtensionInterface {

    private $args;

    public function setup(array $args = []) {
        $this->args = $args;
    }

    public function provideFilters() {
        return [];
    }

    public function provideFunctions() {
        return [
            'bird' => [$this, 'getObject']
        ];
    }


    public function getObject(): Bird {
        return $this;
    }

    /**
     * Convert array of prefixes into less specifics
     */
    public function prefixExactToLessSpecific( array $prefixes, int $proto = 6, int $max = 48 ): array {

        foreach( $prefixes as $i => $p ) {

            list( $net, $mask ) = explode( '/', $p );

            if( $mask > $max ) {
                // subnet is too small already so we do not allow it
                unset( $prefixes[$i] );
                continue;
            }

            if( $mask != $max ) {
                $prefixes[ $i ] = $net . '/' . $mask . '{' . $mask . ',' . $max . '}';
            }
        }

        return $prefixes;
    }

}
