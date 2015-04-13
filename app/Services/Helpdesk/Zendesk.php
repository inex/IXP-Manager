<?php namespace IXP\Services\Helpdesk;

/*
 * Copyright (C) 2009-2015 Internet Neutral Exchange Association Limited.
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



use IXP\Contracts\Helpdesk as HelpdeskContract;
use Zendesk\API\Client as ZendeskAPI;



/**
 * Helpdesk Backend -> Zendesk
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   Helpdesk
 * @package    IXP\Services
 * @copyright  Copyright (c) 2009 - 2015, Internet Neutral Exchange Association Ltd
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Zendesk implements HelpdeskContract {

    /**
     * The Zendesk Client
     * @var Zendesk\API\Client
     */
    private $client;


    public function __construct( $config ) {
        if( !isset( $config['subdomain'] ) || !isset( $config['token'] ) || !isset( $config['email'] ) )
            throw new ConfigurationException( "Zendesk requires that 'subdomain', 'token', 'email' be configured" );

        $this->client = new ZendeskAPI($config['subdomain'], $config['email']);
        $this->client->setAuth('token', $config['token']);
    }

    /**
     * Find all tickets on the helpdesk
     */
    public function ticketsFindAll() {
        try {
            return $this->client->tickets()->findAll();
        } catch( \Zendesk\API\ResponseException $re ) {
            var_dump( $this->client->getDebug() );
        }
    }


}
