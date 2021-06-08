<?php

namespace IXP\Console\Commands\Customer;

/*
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use IXP\Console\Commands\Command;
use Illuminate\Database\Eloquent\Builder;
use IXP\Models\Customer;

/**
 * Artisan command to find customer by ASN or Name
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yanny@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Console\Commands\Customer
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */

class Find extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customer:find
                        {search : ASN or customer name fragment to search for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to find and print customer(s) details via the ASN or Name.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->table(
            ['ID', 'Name', 'ASN'],
            $this->customersViaNameOrASN( $this->argument('search') )
        );

        return 0;
    }
}
