<?php

namespace IXP\Mail\Grapher;

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use IXP\Services\Grapher\Graph;

/**
 * Mailable for ports with counts of (e.g.) errors / discards
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Grapher
 * @package    IXP\Mail\Grapher
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PortsWithCounts extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var array
     */
    public $ports;

    /**
     * @var float
     */
    public $category;

    /**
     * Create a new message instance.
     *
     * @param array $ports
     * @param string $category
     */
    public function __construct( array $ports, string $category )
    {
        $this->ports    = $ports;
        $this->category = $category;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): self
    {
        $c = Graph::resolveCategory( $this->category );
        return $this->view('services.grapher.email.ports-with-counts')
            ->subject( env('IDENTITY_NAME') . " :: Ports with " . $c )
            ->with( 'categoryDesc', $c );
    }
}