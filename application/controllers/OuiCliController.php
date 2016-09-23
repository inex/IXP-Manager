<?php

/*
 * Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee.
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
 * Controller: OUI CLI Actions
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP_Controller
 * @copyright  Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class OuiCliController extends IXP_Controller_CliAction
{
    /**
     * Update OUI database from named file or IEEE website - for issue #87
     *
     * Typically called as:
     *
     *     bin/ixptool.php -a oui-cli.update-database
     *
     * which will create / update the OUI database directly from the latest IEEE file from their website.
     *
     * A specific file can be passed via the `fromfile` parameter. You can also force a
     * database reset (drop all OUI entries and re-populate) via the `refresh` parameter.
     *
     * Neither of these options are typically necessary:
     *
     *     bin/ixptool.php -a oui-cli.update-database -p fromfile=/path/to/oui.txt,refresh=1
     *
     * Note that we bundle a recent OUI file in `date/oui` also.
     * 
     * @return null
     */
    public function updateDatabaseAction()
    {
        $ouitool = new IXP_OUI( $this->getParam( 'fromfile', false ) );

        $ouiRepo = $this->getD2R( '\\Entities\OUI' );

        if( $refresh = $this->getParam( 'refresh', false ) )
            $this->verbose( "Deleted " . $ouiRepo->clear() . " OUI entries during refresh" );

        $cnt = 0;
        foreach( $ouitool->loadList()->processRawData() as $oui => $organisation )
        {
            if( $cnt++ >= 1000 )
            {
                $this->getD2EM()->flush();
                $this->verbose( '.', false );
                $cnt = 0;
            }

            if( !$refresh && ( $o = $ouiRepo->findOneBy( [ 'oui' => $oui ] ) ) )
            {
                if( $o->getOrganisation() != $organisation )
                    $o->setOrganisation( $organisation );
                continue;
            }

            $o = new \Entities\OUI;
            $o->setOui( $oui );
            $o->setOrganisation( $organisation );
            $this->getD2EM()->persist( $o );
        }

        $this->getD2EM()->flush();
        $this->verbose("");
    }

}

