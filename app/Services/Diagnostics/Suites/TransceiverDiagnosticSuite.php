<?php

namespace IXP\Services\Diagnostics\Suites;

/*
 * Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use IXP\Models\PhysicalInterface;
use IXP\Services\Diagnostics\DiagnosticResult;
use IXP\Services\Diagnostics\DiagnosticSuite;

use OSS_SNMP\SNMP;

/**
 * Diagnostics Service - Transceiver Suite
 *
 *
 * *************************************************************************************************************************
 * *************************************************************************************************************************
 *
 * THIS IS A PROOF OF CONCEPT FOR DISCOVERING TRANSCEIVER STATES VIA SNMP AND TO IDENTIFY HOW WE COULD INCIRPORATE IT
 * INTO IXP MANAGER STATS, ETC
 *
 * *************************************************************************************************************************
 * *************************************************************************************************************************
 *
 *
 * @author     Barry O'Donovan  <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP\Services\Diagnostics
 * @copyright  Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */

class TransceiverDiagnosticSuite extends DiagnosticSuite
{
    public const string DIAGNOSTIC_SUITE_NAME = 'Transceiver Diagnostic';

    public const string DIAGNOSTIC_SUITE_DESCRIPTION = "Transceiver diagnostics.";

    public const string DIAGNOSTIC_SUITE_TYPE = 'PHYSICAL_INTERFACE';

    private SNMP|bool|null $snmpClient = null;


    const OID_XCVR_DOM_TEMPERATURE              = '.1.3.6.1.2.1.47.1.1.1.1.2.1003%02d201';    // %d => XX port number
    const OID_XCVR_DOM_VOLTAGE                  = '.1.3.6.1.2.1.47.1.1.1.1.2.1003%02d202';    // %d => XX port number

    const OID_XCVR_DOM_LANE_SENSOR              = '.1.3.6.1.2.1.47.1.1.1.1.2.1003%02d2%d%d';  // %d => XX port number
                                                                                            // %d => 1-4: Lane number
                                                                                            // %d => 1: DOM TX Bias Sensor
                                                                                            //    => 2: DOM TX Power Sensor
                                                                                            //    => 3: DOM RX Power Sensor

    const OID_XCVR_DOM_TEMPERATURE_UNITS        = '.1.3.6.1.2.1.99.1.1.1.6.1003%02d201';      // %d => XX port number
    const OID_XCVR_DOM_VOLTAGE_UNITS            = '.1.3.6.1.2.1.99.1.1.1.6.1003%02d202';      // %d => XX port number

    const OID_XCVR_DOM_LANE_SENSOR_UNITS        = '.1.3.6.1.2.1.99.1.1.1.6.1003%02d2%D%D';    // %d => XX port number
                                                                                            // %d => 1-4: Lane number
                                                                                            // %d => 1: DOM TX Bias Sensor
                                                                                            //    => 2: DOM TX Power Sensor
                                                                                            //    => 3: DOM RX Power Sensor

    const OID_XCVR_DOM_TEMPERATURE_THRESHOLD    = '.1.3.6.1.4.1.30065.3.12.1.1.1.5.1003%02d201';   // %d => XX port number - "Sensor value 38.1 Celsius is within bounds"
    const OID_XCVR_DOM_VOLTAGE_UNITS_THRESHOLD  = '.1.3.6.1.4.1.30065.3.12.1.1.1.5.1003%02d202';   // %d => XX port number - "Sensor value 3.29 Volts is within bounds"


    // 1 - "Sensor value 42.21 mA is within bounds"
    // 2 - "Sensor value 1.0030 mW is within bounds"
    // 3 - "Sensor value 0.6926 mW is within bounds"
    const OID_XCVR_DOM_LANE_SENSOR_UNITS_THRESHOLD = '.1.3.6.1.4.1.30065.3.12.1.1.1.5.1003%02d2%d%d';     // %d => XX port number
                                                                                                        // %d => 1-4: Lane number
                                                                                                        // %d => 1: DOM TX Bias Sensor
                                                                                                        //    => 2: DOM TX Power Sensor
                                                                                                        //    => 3: DOM RX Power Sensor

    const OID_XCVR_SERIAL_NUMBER                = '.1.3.6.1.2.1.47.1.1.1.1.11.1003%02d100';   // %d => XX port number
    const OID_XCVR_MANUFACTURER                 = '.1.3.6.1.2.1.47.1.1.1.1.12.1003%02d100';   // %d => XX port number
    const OID_XCVR_MODEL                        = '.1.3.6.1.2.1.47.1.1.1.1.13.1003%02d100';   // %d => XX port number


    public function __construct(
        private PhysicalInterface $pi,
    ) {

        if( $pi?->switchPort ) {
            $this->name = 'Transceiver diagnostics for: ' . $pi->switchPort->switcher->name . ' :: ' . $pi->switchPort->name . ' [Physical Interface #' . $pi->id . ']';
        } else {
            $this->name        = 'Physical Interface #' . $pi->id;
        }

        parent::__construct();
    }



    /**
     * Get / instantiate the snmp client
     * @return SNMP|bool|null
     */
    private function snmpClient(): SNMP|bool|null {

        if( $this->snmpClient === null ) {
            if( empty( $pi?->switchPort->switcher->snmppasswd ) ) {
                $this->snmpClient = false;
            } else {
                $this->snmpClient = new SNMP( $pi->switchPort->switcher->hostname, $pi->switchPort->switcher->snmppasswd );
                $this->snmpClient->disableCache();
            }
        }

        return $this->snmpClient;
    }



    /**
     * Run the diagnostics suite
     */
    public function run(): TransceiverDiagnosticSuite
    {

        // check if we can even run these first
        if( !$this->snmpClient() ) {

            $this->results->add( new DiagnosticResult(
                name: "Transceiver diagnostics not available via SNMP for this switch.",
                result: DiagnosticResult::TYPE_WARN,
                narrative: "Please log into the switch and run transceiver diagnostics manually.",
            ) );

            return $this;
        }

        // check if we can even run these first
        if( $this->pi->switchPort->switcher->os !== 'EOS' ) {

            $this->results->add( new DiagnosticResult(
                name: "Transceiver diagnostics not available for non-Arista EOS switches currently.",
                result: DiagnosticResult::TYPE_WARN,
                narrative: "Please log into the switch and run transceiver diagnostics manually.",
            ) );

            return $this;
        }

        $this->results->add( $this->info( $this->pi ) );
        $this->results->add( $this->temperature( $this->pi ) );
        $this->results->add( $this->voltage( $this->pi ) );
        $this->results->add( $this->lightLevels( $this->pi ) );

        return $this;
    }


    /**
     * Get general information on the transceiver
     *
     * @return DiagnosticResult
     */
    private function temperature( PhysicalInterface $pi ): DiagnosticResult
    {
        $mainName = "Transceiver temperature - ";

        ## TRYCATCH - update to unknown
        try {
            $t = $this->snmpClient()->get( sprintf( self::OID_XCVR_DOM_TEMPERATURE_THRESHOLD, $this->oidPort( $this->pi->switchPort->name ) ) );
        } catch( \Exception $e ) {
            return new DiagnosticResult(
                name: $mainName . "could not find xcvr temperature thresholds",
                result: DiagnosticResult::TYPE_ERROR,
                narrative: $e->getMessage(),
            );
        }

        if( str_ends_with( $t, 'is within bounds' ) ) {

            return new DiagnosticResult(
                name: $mainName . "{$t}",
                result: DiagnosticResult::TYPE_DEBUG,
                narrative: "{$t}",
            );

        }

        return new DiagnosticResult(
            name: $mainName . "{$t}",
            result: DiagnosticResult::TYPE_ERROR,
            narrative: "{$t}",
        );

    }


    /**
     * Get general information on the transceiver
     *
     * @return DiagnosticResult
     */
    private function voltage( PhysicalInterface $pi ): DiagnosticResult
    {
        $mainName = "Transceiver voltage - ";

        ## TRYCATCH - update to unknown

        try {
            $v = $this->snmpClient()->get( sprintf( self::OID_XCVR_DOM_VOLTAGE_UNITS_THRESHOLD, $this->oidPort( $this->pi->switchPort->name ) ) );
        } catch( \Exception $e ) {
            return new DiagnosticResult(
                name: $mainName . "could not find xcvr voltage thresholds",
                result: DiagnosticResult::TYPE_ERROR,
                narrative: $e->getMessage(),
            );
        }

        if( str_ends_with( $v, 'is within bounds' ) ) {

            return new DiagnosticResult(
                name: $mainName . "{$v}",
                result: DiagnosticResult::TYPE_DEBUG,
                narrative: "{$v}",
            );

        }

        return new DiagnosticResult(
            name: $mainName . "{$v}",
            result: DiagnosticResult::TYPE_ERROR,
            narrative: "{$v}",
        );

    }


    /**
     * Get general information on the transceiver
     *
     * const OID_XCVR_DOM_LANE_SENSOR_UNITS_THRESHOLD = '.1.3.6.1.4.1.30065.3.12.1.1.1.5.1003%d2%D%D';
     * // %d => XX port number
     * // %d => 1-4: Lane number
     * // %d => 1: DOM TX Bias Sensor
     * //    => 2: DOM TX Power Sensor
     * //    => 3: DOM RX Power Sensor
     *
     * @return DiagnosticResult[]
     */
    private function lightLevels( PhysicalInterface $pi ): array
    {
        $mainName = "Light levels - ";
        $results  = [];

        $sensors = [
            1 => 'DOM TX Bias Sensor',
            2 => 'DOM TX Power Sensor',
            3 => 'DOM RX Power Sensor',
        ];

        if( $pi->speed <= 10_000 ) {
            $lanes = [1];
        }  else {
            $lanes = [ 1, 2, 3, 4 ];
        }

        $readings = [];

        foreach( array_keys( $sensors ) as $sensor ) {
            foreach( $lanes as $lane ) {
                $v = null;

                ## TRYCATCH - update to unknown

                try {
                    $v = $this->snmpClient()->get( sprintf( self::OID_XCVR_DOM_LANE_SENSOR_UNITS_THRESHOLD, $this->oidPort( $this->pi->switchPort->name ), $lane, $sensor ) );
                } catch( \Exception $e ) {
                    if( $lane > 1) {
                        continue;
                    }

                    $results[] = new DiagnosticResult(
                        name: $mainName . "could not find {$sensors[$sensor]} for lane {$lane} thresholds",
                        result: DiagnosticResult::TYPE_ERROR,
                        narrative: $e->getMessage(),
                    );

                    $readings[ $sensor ][ $lane ] = 'ERR';

                    continue;
                }

                if( str_ends_with( $v, 'is within bounds' ) ) {

                    $results[] = new DiagnosticResult(
                        name: $mainName . "{$sensors[$sensor]} for lane {$lane} - {$v}",
                        result: DiagnosticResult::TYPE_DEBUG,
                        narrative: "{$v}",
                    );

                } else {

                    $results[] = new DiagnosticResult(
                        name: $mainName . "{$sensors[$sensor]} for lane {$lane} - {$v}",
                        result: DiagnosticResult::TYPE_ERROR,
                        narrative: "{$v}",
                    );

                }

                // "Sensor value 42.21 mA is within bounds"
                // "Sensor value 1.0030 mW is within bounds"
                // "Sensor value 0.6926 mW is within bounds"
                $matches = [];
                preg_match( "/Sensor value ([\d\.]+) ([a-zA-Z]+)\s.*/", $v, $matches );

                if( isset( $matches[2] ) && $matches[2] == 'mW') {
                    $matches[1] = 10 * log10($matches[1]);  // to dbm
                }
                $readings[ $sensor ][ $lane ] = sprintf( "%+2.2f", $matches[ 1 ] );

            }
        }

        $swoutput = <<<ENDSWOUTPUT
                Bias      Optical   Optical
                Current   Tx Power  Rx Power
            L   (mA)      (dBm)     (dBm)    
            -  --------  --------  -------- 

            ENDSWOUTPUT;

        foreach( $lanes as $lane ) {
            $swoutput .= "{$lane}  {$readings[1][$lane]}    {$readings[2][$lane]}     {$readings[3][$lane]}\n";
        }

        $results[] = new DiagnosticResult(
            name: $mainName . "click info for switch-like output",
            result: DiagnosticResult::TYPE_INFO,
            narrativeHtml: "<pre>{$swoutput}</pre>",
        );

        return $results;

    }

    /**
     * Get general information on the transceiver
     *
     * @return DiagnosticResult
     */
    private function info( PhysicalInterface $pi ): DiagnosticResult
    {
        $mainName = "Transceiver information - ";

        ## TRYCATCH
        try {
            $serial = trim( $this->snmpClient()->get( sprintf( self::OID_XCVR_SERIAL_NUMBER, $this->oidPort($pi->switchPort->name) ) ) );
            $manuf  = trim( $this->snmpClient()->get( sprintf( self::OID_XCVR_MANUFACTURER,  $this->oidPort($pi->switchPort->name) ) ) );
            $model  = trim( $this->snmpClient()->get( sprintf( self::OID_XCVR_MODEL,         $this->oidPort($pi->switchPort->name) ) ) );
        } catch( \Exception $e ) {
            return new DiagnosticResult(
                name: $mainName . "could not find xcvr serial, model and/or manufacturer",
                result: DiagnosticResult::TYPE_ERROR,
                narrative: $e->getMessage(),
            );
        }

        return new DiagnosticResult(
            name: $mainName . "{$manuf} {$model}, serial #{$serial}",
            result: DiagnosticResult::TYPE_INFO,
            narrativeHtml: "<b>Manufacturer:</b> {$manuf}<br><b>Model:</b> {$model}<br><b>Serial:</b> {$serial}",
        );
    }


    /**
     * PoC - assumes Arista right now
     * @param string $name
     * @return string
     */
    private function oidPort( string $name ): string {
        $matches = [];
        preg_match( '/Ethernet(\d+).*/', $name, $matches );
        return sprintf( '%02d', (int)$matches[1] );
    }


}