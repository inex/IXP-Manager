<?php

namespace IXP\Http\Middleware\Provisioning;

/*
 * Copyright (C) 2026 KleyReX. All Rights Reserved.
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

use Closure;

use Illuminate\Http\Request;

use IXP\Models\ProvisioningReference;

/**
 * Middleware: ShortCircuitKnownReference
 *
 * Answers a repeated onboarding request from the record of the first one.
 *
 * This has to run before validation, not in the controller. A caller retrying after a timeout
 * sends the identical payload, and by then the shortname it asks for exists - so validation
 * would reject the retry with a 422 about a duplicate shortname. Technically true, useless in
 * practice: the caller wants to know what happened to its order, and "you already have it" is
 * the answer, not "that name is taken".
 *
 * The controller keeps its own check as well. This one closes the common case; that one closes
 * the window between two simultaneous retries, where both pass here before either commits.
 *
 * @author     KleyReX
 * @category   IXP
 * @package    IXP\Http\Middleware\Provisioning
 * @copyright  Copyright (C) 2026 KleyReX
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ShortCircuitKnownReference
{
    /**
     * Return the earlier result if this reference has been seen before.
     *
     * @param  Request  $r
     * @param  Closure  $next
     *
     * @return mixed
     */
    public function handle( Request $r, Closure $next )
    {
        $reference = $r->input( 'reference' );

        if( !is_string( $reference ) || $reference === '' ) {
            return $next( $r );
        }

        if( !( $cust = ProvisioningReference::customerFor( $reference ) ) ) {
            return $next( $r );
        }

        return response()->json( [
            'created'   => false,
            'reference' => $reference,
            'member'    => [ 'id' => $cust->id, 'shortname' => $cust->shortname ],
        ] );
    }
}
