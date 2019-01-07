<?php namespace IXP\Console\Commands;

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use Symfony\Component\Console\Output\OutputInterface;
use Entities\IXP;

abstract class Command extends \Illuminate\Console\Command {

    /**
     * Checks if reseller mode is enabled.
     *
     * To enable reseller mode set the env variable IXP_RESELLER_ENABLED
     *
     * @see http://docs.ixpmanager.org/features/reseller/
     *
     * @return bool
     */
    protected function resellerMode(): bool
    {
        return boolval( config( 'ixp.reseller.enabled', false ) );
    }

    /**
     * Checks if multi IXP mode is enabled.
     *
     * To enable multi IXP mode set the env variable IXP_MULTIIXP_ENABLED
     *
     * NB: this functionality is deprecated in IXP Manager v4.0 and will be
     * removed piecemeal.
     *
     * @see https://github.com/inex/IXP-Manager/wiki/Multi-IXP-Functionality
     *
     * @return bool
     */
    protected function multiIXP(): bool
    {
        return boolval( config( 'ixp.multiixp.enabled', false ) );
    }

    /**
     * Checks if as112 is activated in the UI.
     *
     * To disable as112 in the UI set the env variable IXP_AS112_UI_ACTIVE
     *
     * @see http://docs.ixpmanager.org/features/as112/
     *
     * @return bool
     */
    protected function as112UiActive(): bool
    {
        return boolval( config( 'ixp.as112.ui_active', false ) );
    }

    /**
     * Get whatever IXP was specified on the command line
     *
     * We have not implemented multi-IXP in v4 but we're leaving the framework in
     * place as it's well constructed to allow for it.
     *
     * @param int id A specfic IXP id to load (otherwise command line option or default)
     * @return IXP
     */
    protected function ixp( $id = null ): IXP {
        // what IXP are we running on here?
        if( $this->multiIXP() ) {
            $this->error( 'Multi IXP support has not been ported to V4 due to no usage.' );
            exit -1;

            //$ixpid = $this->getParam( 'ixp', false );
            //if( !$ixpid || !( $ixp = $this->getD2R( '\\Entities\\IXP' )->find( $ixpid ) ) )
            //    die( "ERROR: Invalid or no IXP specified.\n" );
        } else {
            return d2r( 'IXP' )->getDefault();
        }
    }


     /**
      * Returns true if verbosity is EXACTLY: VERBOSITY_QUIET
      * @return bool
      */
     protected function isVerbosityQuiet() {
         return $this->getOutput()->getVerbosity() == OutputInterface::VERBOSITY_QUIET;
     }

     /**
      * Returns true if verbosity is at least: VERBOSITY_NORMAL
      * @return bool
      */
     protected function isVerbosityNormal() {
         return $this->getOutput()->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL;
     }

     /**
      * Returns true if verbosity is at least: VERBOSITY_VERBOSE
      * @return bool
      */
     protected function isVerbosityVerbose() {
         return $this->getOutput()->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE;
     }

     /**
      * Returns true if verbosity is at least: VERBOSITY_VERY_VERBOSE
      * @return bool
      */
     protected function isVerbosityVeryVerbose() {
         return $this->getOutput()->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE;
     }

     /**
      * Returns true if verbosity is at least: VERBOSITY_DEBUG
      * @return bool
      */
     protected function isVerbosityDebug() {
         return $this->getOutput()->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG;
     }

}
