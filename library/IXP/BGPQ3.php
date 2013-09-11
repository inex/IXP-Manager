<?php


/*
 * Copyright (C) 2009-2013 Internet Neutral Exchange Association Limited.
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
 * Interface for the BQPQ3 command line utility
 *
 * @see http://snar.spb.ru/prog/bgpq3/
 *
 * @author Barry O'Donovan <barry@opensolutions.ie>
 * @package IXP_BGPQ3
 */
class IXP_BGPQ3 extends Zend_Exception
{
    /**
     * Full executable path of the BGPQ3 utility
     * @var string Full executable path of the BGPQ3 utility
     */
    private $path = null;
    
    /**
     * Whois server - defaults to BGPQ's own default
     * @var string Whois server - defaults to BGPQ's own default
     */
    private $whois = false;
    
    /**
     * Whois server sources - defaults to BGPQ's own default
     * @var string Whois server sources - defaults to BGPQ's own default
     */
    private $sources = false;
    
    
    public function __construct( $path, $whois = false, $sources = false )
    {
        $this->path = $path;
        
        if( $whois )
            $this->whois = $whois;
        
        if( $sources )
            $this->sources = $sources;
    }
    
    public function getPrefixList( $asmacro, $proto = 4, $name = 'pl' )
    {
        $json = $this->execute( '-l ' . escapeshellarg( $name ) . ' -j ' . escapeshellarg( $asmacro ), $proto );
        $array = json_decode( $json, true );
        
        if( !isset( $array[ $name ] ) )
            throw new IXP_Exception( "Named prefix list [{$name}] expected but not found!" );
        
        $prefixes = [];
        // we're going to ignore the 'exact' for now.
        foreach( $array[ $name ] as $ar )
            $prefixes[] = $ar['prefix'];
        
        return $prefixes;
    }
    
    
    private function execute( $cmd, $proto = 4 )
    {
        if( $this->whois )
            $cmd = '-h ' . escapeshellarg( $this->whois ) . ' ' . $cmd;
        
        if( $this->sources )
            $cmd = '-S ' . escapeshellarg( $this->sources ) . ' ' . $cmd;
        
        if( $proto == 6 )
            $cmd = '-6 ' . $cmd;
        
        $cmd = $this->path . ' ' . $cmd;

        $output = shell_exec( $cmd );
        
        if( $output === null )
            throw new IXP_Exception( 'Error executed BGPQ3 with: ' . $cmd );
        
        return $output;
    }
    

    /**
     * The whois server to query
     *
     * @param string $whois The whois server to query
     * @return IXP_BGPQ3 For fluent interfaces
     */
    public function setWhois( $whois )
    {
        $this->whois = $whois;
        return $this;
    }

    /**
     * The whois server sources
     *
     * @param string $sources The whois server sources
     * @return IXP_BGPQ3 For fluent interfaces
     */
    public function setSources( $sources )
    {
        $this->sources = $sources;
        return $this;
    }

    /**
     * The executable path to the BGPQ executable
     *
     * @param string $path The executable path to the BGPQ executable
     * @return IXP_BGPQ3 For fluent interfaces
     */
    public function setPath( $path )
    {
        $this->path = $path;
        return $this;
    }

}
