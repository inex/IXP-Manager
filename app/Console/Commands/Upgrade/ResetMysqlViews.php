<?php

namespace IXP\Console\Commands\Upgrade;

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
use Illuminate\Support\Facades\DB;
use IXP\Console\Commands\Command as IXPCommand;
/**
 * Reset MYSQL views
 *
 * @author      Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author      Yann Robin <yann@islandbridgenetworks.ie>
 * @package     IXP\Console\Commands\Upgrade
 * @copyright   Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ResetMysqlViews extends IXPCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:reset-mysql-views';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset MySQL views (idempotent and should be run after each upgrade)';

    /**
     * Execute the console command.
     *
     * Transfers data from the table 'customer' and 'user' to the table 'customer_to_users'
     *
     * @return mixed
     *
     * @throws
     *
     */
    public function handle(): int
    {
        DB::unprepared( resolve( 'Foil\Engine' )->render( 'database/views.foil.sql' ) );

        $sql = <<<END_SQL
CREATE TRIGGER bgp_sessions_update AFTER INSERT ON `bgpsessiondata` FOR EACH ROW

BEGIN

    IF NOT EXISTS ( SELECT 1 FROM bgp_sessions WHERE srcipaddressid = NEW.srcipaddressid AND protocol = NEW.protocol AND dstipaddressid = NEW.dstipaddressid ) THEN
			INSERT INTO bgp_sessions
                ( srcipaddressid, protocol, dstipaddressid, packetcount, last_seen, source )
			VALUES
                ( NEW.srcipaddressid, NEW.protocol, NEW.dstipaddressid, NEW.packetcount, NOW(), NEW.source );
    ELSE
			UPDATE bgp_sessions SET
				last_seen   = NOW(),
				packetcount = packetcount + NEW.packetcount
			WHERE
				srcipaddressid = NEW.srcipaddressid AND protocol = NEW.protocol AND dstipaddressid = NEW.dstipaddressid;
    END IF;

    IF NOT EXISTS ( SELECT 1 FROM bgp_sessions WHERE dstipaddressid = NEW.srcipaddressid AND protocol = NEW.protocol AND srcipaddressid = NEW.dstipaddressid ) THEN
			INSERT INTO bgp_sessions
                ( srcipaddressid, protocol, dstipaddressid, packetcount, last_seen, source )
			VALUES
                ( NEW.dstipaddressid, NEW.protocol, NEW.srcipaddressid, NEW.packetcount, NOW(), NEW.source );
    ELSE
			UPDATE bgp_sessions SET
				last_seen   = NOW(),
				packetcount = packetcount + NEW.packetcount
			WHERE
				dstipaddressid = NEW.srcipaddressid AND protocol = NEW.protocol AND srcipaddressid = NEW.dstipaddressid;
    END IF;

END
END_SQL;

        DB::unprepared( $sql );

        return 0;
    }
}