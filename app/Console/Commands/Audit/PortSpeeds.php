<?php namespace IXP\Console\Commands\Audit;

/*
 * Copyright (C) 2009-2018 Internet Neutral Exchange Association Company Limited By Guarantee.
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
use D2EM;

use Entities\PhysicalInterface;

/**
 * Artisan command to audit configured port speeds against actual switch speeds
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Audit
 * @package    IXP\Console\Commands
 * @copyright  Copyright (C) 2009-2018 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PortSpeeds extends Command {

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

        $mismatched = [];

        $ignorepiids = [];
        if( $this->option('ignore-pids' ) ) {
            $ignorepiids = explode( ',', $this->option('ignore-pids' ) );
        }

        /** @var PhysicalInterface $pi */
        foreach( D2EM::getRepository(PhysicalInterface::class )->findAll() as $pi ) {

            if( $pi->statusIsConnectedOrQuarantine() && $pi->getSwitchPort() && $pi->getSpeed() != $pi->getSwitchPort()->getIfHighSpeed() ) {

                if( in_array( $pi->getId(), $ignorepiids ) ) {
                    continue;
                }

                $mismatched[] = [
                    $pi->getVirtualInterface()->getCustomer()->getFormattedName(),
                    $pi->getId(),
                    $pi->getSwitchPort()->getSwitcher()->getName(),
                    $pi->getSwitchPort()->getName(),
                    $pi->getSwitchPort()->getIfHighSpeed(),
                    $pi->getSpeed(),
                ];
            }

        }

        if( $this->option('cron') && $mismatched === [] ) {
            return 0;
        }

        $this->info( "\nAudit of Configured Physical Interface Port Speeds Against SNMP Discovered Speeds\n" );

        if( $mismatched == [] ) {
            $this->info( "No mismatched ports found." );
        } else {
            $this->table( [ 'Customer', 'PI DB ID', 'Switch', 'Switchport', 'PI Speed', 'SNMP Speed' ], $mismatched );
        }

        return $mismatched == [] ? 0 : 1;
    }
}
