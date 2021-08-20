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

use Carbon\Carbon;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable for port deltas
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Grapher
 * @package    IXP\Mail\Grapher
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class TrafficDeltas extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var array
     */
    public $ports;

    /**
     * @var float
     */
    public $stddev;

    /**
     * @var Carbon
     */
    public $day;

    /**
     * Create a new message instance.
     *
     * @param array     $ports
     * @param float     $stddev
     * @param Carbon    $day
     */
    public function __construct( array $ports, float $stddev, Carbon $day )
    {
        $this->ports    = $ports;
        $this->stddev   = $stddev;
        $this->day      = $day;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): self
    {
        return $this->view('services.grapher.email.traffic-deltas')
            ->subject( env('IDENTITY_NAME') . " :: Traffic Deltas Report" );
    }
}
