<?php

namespace IXP\Console\Commands\Audit;

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

use IXP\Console\Commands\Command;

use IXP\Models\PhysicalInterface;
use OSS_SNMP\MIBS\Iface as IfaceMIB;

/**
 * Artisan command to audit configured port speeds against actual switch speeds
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Audit
 * @package    IXP\Console\Commands
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PortSpeeds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audit:port-speeds
        {--cron : Running as a cronjob, only output if mismatched ports found}
        {--ignore-pids= : Comma separated physical interface database IDs to ignore}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Audit configured port speeds against actual switch speeds (as discovered by last SNMP run)';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        $mismatched = $ignorepiids = [];
        if( $this->option('ignore-pids' ) ) {
            $ignorepiids = explode( ',', $this->option('ignore-pids' ) );
        }

        foreach( PhysicalInterface::whereNotIn( 'id', $ignorepiids )->get() as $pi ) {
            if( $pi->isConnectedOrQuarantine()
                    && $pi->switchPort
                    && $pi->speed !== $pi->switchPort->ifHighSpeed
                    && $pi->switchPort->ifOperStatus === IfaceMIB::IF_OPER_STATUS_UP ) {

                $mismatched[] = [
                    $pi->virtualInterface->customer->getFormattedName(),
                    $pi->id,
                    $pi->switchPort->switcher->name,
                    $pi->switchPort->name,
                    $pi->switchPort->ifHighSpeed,
                    $pi->speed,
                ];
            }
        }

        if( $this->option('cron') && !count( $mismatched ) ) {
            return 0;
        }

        $this->info( "\nAudit of Configured Physical Interface Port Speeds Against SNMP Discovered Speeds\n" );

        if( !count( $mismatched ) ) {
            $this->info( "No mismatched ports found." );
        } else {
            $this->table( [ 'Customer', 'PI DB ID', 'Switch', 'Switchport', 'PI Speed', 'SNMP Speed' ], $mismatched );
        }

        return !count( $mismatched ) ? 0 : 1;
    }
}