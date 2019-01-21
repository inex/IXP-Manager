<?php

namespace IXP\Console\Commands\Utils;

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */


use D2EM;
use Entities\OUI as OUIEntity;
use IXP\Console\Commands\Command as IXPCommand;
use IXP\Utils\OUI as OUIUtil;


/**
 * Class UpdateOuiDatabase - update OUI database from named file or IEEE website.
 *
 * A specific file can be passed via the `fromfile` parameter. You can also force a
 * database reset (drop all OUI entries and re-populate) via the `refresh` option.
 *
 * Neither of these options are typically necessary:
 *
 * Note that we bundle a recent OUI file in `data/oui` also.
 *
 * @author Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @package IXP\Console\Commands\Utils
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class UpdateOuiDatabase extends IXPCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'utils:oui-update {file?} {--refresh}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update/populate the OUI database table.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        $ouitool = new OUIUtil( $this->argument( 'file' ) );
        $ouiRepo = D2EM::getRepository( OUIEntity::class );

        if( $refresh = $this->option( 'refresh' ) ) {
            $this->info( "Deleted " . $ouiRepo->clear() . " OUI entries during refresh" );
        }

        $cnt = 0;
        foreach( $ouitool->loadList()->processRawData() as $oui => $organisation ) {
            if( $cnt++ >= 1000 ) {
                D2EM::flush();
                $this->isVerbosityVerbose() && $this->output->write('.', false);
                $cnt = 0;
            }

            if( !$refresh && ( $o = $ouiRepo->findOneBy( [ 'oui' => $oui ] ) ) ) {
                if( $o->getOrganisation() != $organisation )
                    $o->setOrganisation( $organisation );
                continue;
            }

            $o = new OUIEntity();
            $o->setOui( $oui );
            $o->setOrganisation( $organisation );
            D2EM::persist( $o );
        }

        D2EM::flush();
        $this->isVerbosityVerbose() && $this->output->write('.', true);
        return 0;
    }
}
