<?php namespace IXP\Console\Commands\Helpdesk;

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

use App;

use IXP\Console\Commands\Command as IXPCommand;

abstract class HelpdeskCommand extends IXPCommand {


    private $helpdesk = null;

    protected function getHelpdesk() {
        if( $this->helpdesk === null ) {
            $this->helpdesk = App::make('IXP\Contracts\Helpdesk');
        }

        if( $this->helpdesk === false ) {
            $this->error( 'No helpdesk provider defined' );
            exit -1;
        }

        return $this->helpdesk;
    }

}
