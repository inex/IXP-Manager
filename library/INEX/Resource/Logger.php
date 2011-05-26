<?php

/*
 * Copyright (C) 2009-2011 Internet Neutral Exchange Association Limited.
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
 * INEX Internet Exchange Point Application :: INEX IXP
 *
 * Copyright (c) 2009 Internet Neutral Exchange Limited <http://www.inex.ie/>
 * All rights reserved.
 *
 * @category INEX
 * @version $Id: Logger.php 447 2011-05-26 13:53:52Z barryo $
 * @package INEX_Bootstrap_Resources
 * @copyright Copyright (c) 2009 Internet Neutral Exchange Limited <http://www.inex.ie/>
 *
 */


/**
 * Class to instantiate Logger
 *
 * @category INEX
 * @package INEX_Bootstrap_Resources
 * @copyright Copyright (c) 2009 Internet Neutral Exchange Limited <http://www.inex.ie/>
 */
class INEX_Resource_Logger extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * Holds the Logger instance
     *
     * @var
     */
    protected $_logger;


    public function init()
    {
        // Return logger so bootstrap will store it in the registry
        return $this->getLogger();
    }


    public function getLogger()
    {
        if( null === $this->_logger )
        {
            // Get Doctrine configuration options from the application.ini file
            $options = $this->getOptions();

            $logger = new Zend_Log();

            if( $options['enabled'] )
            {
                foreach( $options['writers'] as $writer => $writerOptions )
                {
                    switch( $writer )
                    {
                        case 'stream':
                            $log_path = $writerOptions['path']
                            . DIRECTORY_SEPARATOR .  date( 'Y' )
                            . DIRECTORY_SEPARATOR . date( 'm' );

                            $log_file = $log_path . DIRECTORY_SEPARATOR . date( 'Ymd') . '.log';

                            if( !file_exists( $log_path ) )
                            {
                                mkdir( $log_path, 0770, true );
                                chmod( $log_path, 0770       );
                                chown( $log_path, $writerOptions['owner'] );
                                chgrp( $log_path, $writerOptions['group'] );
                            }

                            if( !file_exists( $log_file ) )
                            {
                                touch( $log_file             );
                                chmod( $log_file, 0660       );
                                chown( $log_file, $writerOptions['owner'] );
                                chgrp( $log_file, $writerOptions['group'] );
                            }

                            $streamWriter = new Zend_Log_Writer_Stream( $log_file );
                            $streamWriter->setFormatter(
                            new Zend_Log_Formatter_Simple(
					            	'%timestamp% %priorityName% (%priority%) ['
                                    . ( ( isset( $_SERVER ) && array_key_exists( 'REMOTE_ADDR', $_SERVER ) ) ? $_SERVER['REMOTE_ADDR'] : 'CLI' )
                                    . ']: %message%' . PHP_EOL
                            )
                            );
                            $logger->addWriter( $streamWriter );

                            if( isset( $writerOptions['level'] ) )
                            $logger->addFilter( (int)$writerOptions['level'] );

                            break;

                        case 'email':
                            $this->getBootstrap()->bootstrap( 'Mailer' );

                            $mail = new Zend_Mail();
                            $mail->setFrom( $writerOptions['from'] )
                            ->addTo( $writerOptions['to'] );

                            $mailWriter = new Zend_Log_Writer_Mail( $mail );

                            // Set subject text for use; summary of number of errors is appended to the
                            // subject line before sending the message.
                            $mailWriter->setSubjectPrependText( "[{$writerOptions['prefix']}]" );

                            // Only email entries with level requested and higher.
                            $mailWriter->addFilter( (int)$writerOptions['level'] );

                            $logger->addWriter( $mailWriter );
                            break;

                        case 'firebug':
                            if( $writerOptions['enabled'] )
                            {
                                $firebugWriter = new Zend_Log_Writer_Firebug();
                                $firebugWriter->addFilter( (int)$writerOptions['level'] );
                                $logger->addWriter( $firebugWriter );
                            }
                            break;

                        default:
                            try {
                                $logger->log( "Unknown log writer: {$writer}", Zend_Log::WARN );
                            } catch( Zend_Log_Exception $e ) {
                                die( "Unknown log writer [{$writer}] during application bootstrap" );
                            }
                            break;
                    }
                }

            }
            else
                $logger->addWriter( new Zend_Log_Writer_Null() );

            try
            {
                $logger->log( 'Logger instantiated', Zend_Log::DEBUG );
            }
            catch( Zend_Log_Exception $e )
            {
                die( "Unknown log writer [{$writer}] during application bootstrap" );
            }


            $this->_logger = $logger;
        }

        return $this->_logger;
    }


}
