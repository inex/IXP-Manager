<?php

declare(strict_types=1);

namespace IXP\Utils\DotEnv;

/*
 * Copyright (C) 2009 - 2025 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
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
 *
 */


/**
 *
 * @author Barry O'Donovan <barry@opensolutions.ie>
 * @package IXP\Utils\DotEnv
 * @copyright  Copyright (C) 2009 - 2025 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class DotEnvWriter
{
    /**
     * Constructs a new instance.
     *
     */
    public function __construct( protected ?array $settings = null, protected ?string $filename = null ) {
    }

    public function settings(): ?array
    {
        return $this->settings;
    }

    public function setSettings( ?array $settings ): static
    {
        $this->settings = $settings;
        return $this;
    }

    public function generateContent(): ?string
    {
        if( !is_array($this->settings) || count($this->settings) === 0 ) {
            return null;
        }

        $content = '';

        foreach( $this->settings as $l ) {

            $lineStarted = false;

            if( $l['key'] !== null ) {
                $content .= $l['key'] . '=';
                $lineStarted = true;
            }

            if( $l['value'] !== null ) {

                if( is_bool( $l['value'] ) ) {
                    $content .= $l['value'] ? 'true' : 'false';
                } elseif( is_int( $l['value'] ) ) {
                    $content .= $l['value'];
                } else {
                    $content .= $l[ 'value' ];
                }

                $lineStarted = true;
            }

            if( $l['comment'] !== null ) {
                $content .= ( $lineStarted ? ' ' : '' ) . '#'
                    . ( !strlen( trim( $l['comment'] ) ) || trim( $l['comment'] )[0] === '#' ? '' : ' ' )
                    . $l['comment'];
            }

            $content .= "\n";

        }

        return $content;
    }


}