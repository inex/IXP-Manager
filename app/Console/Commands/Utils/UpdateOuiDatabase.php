<?php

namespace IXP\Console\Commands\Utils;

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
use IXP\Console\Commands\Command as IXPCommand;

use IXP\Models\Oui;
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
 * @author Yann Robin <yann@islandbridgenetworks.ie>
 * @package IXP\Console\Commands\Utils
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
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
     *
     * @throws
     */
    public function handle()
    {
        $ouitool = new OUIUtil( $this->argument( 'file' ) );

        if( $refresh = $this->option( 'refresh' ) ) {
            $this->info( "Deleted " . Oui::count() . " OUI entries during refresh" );
            Oui::truncate();
        }

        $cnt = 0;
        foreach( $ouitool->loadList()->processRawData() as $oui => $organisation ) {
            if( $cnt++ >= 1000 ) {
                $this->isVerbosityVerbose() && $this->output->write('.', false);
                $cnt = 0;
            }

            if( !$refresh && ( $o = Oui::where( 'oui', $oui  )->first() ) ) {
                if( $o->organisation !== $organisation ){
                    $o->update( [ 'organisation' => $organisation ] );
                }
            } else {
                Oui::create([
                    'oui'           => $oui,
                    'organisation'  => $organisation
                ]);
            }
        }

        $this->isVerbosityVerbose() && $this->output->write('.', true);
        return 0;
    }
}