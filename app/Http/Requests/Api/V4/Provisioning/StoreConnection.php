<?php

namespace IXP\Http\Requests\Api\V4\Provisioning;

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

use IXP\Http\Requests\StoreVirtualInterfaceWizard;
use IXP\Models\Customer;
use IXP\Models\PhysicalInterface;

/**
 * Provisioning API - StoreConnection request
 *
 * Extends the wizard request behind the web UI's "add a port" flow; see StoreCustomer for the
 * reasoning behind the DELTA split and RulesParityTest.
 *
 * Two differences from the web wizard are worth stating plainly, because both relax a rule
 * rather than tighten one:
 *
 *   - `ipv4address` and `ipv6address` accept the literal "auto" as well as an address. The
 *     wizard requires an explicit address because a human picks it from a list rendered in the
 *     browser; unattended provisioning has no such list. The address rule is therefore
 *     replaced by one which also permits "auto", and IpAllocator resolves it.
 *   - `rate_limit` and `autoneg` are accepted. Both are columns of PhysicalInterface and both
 *     are exported to the switch configuration generator, but v7.3.1's wizard request does not
 *     list them (upstream `main` has since added them). Without them a rate-limited port
 *     cannot be provisioned through the API at all.
 *
 * @author     KleyReX
 * @category   IXP
 * @package    IXP\Http\Requests\Api\V4\Provisioning
 * @copyright  Copyright (C) 2026 KleyReX
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class StoreConnection extends StoreVirtualInterfaceWizard
{
    /**
     * Rules added on top of the inherited wizard rules.
     *
     * @return array<string,mixed>
     */
    public static function delta(): array
    {
        return [
            'rate_limit'    => 'nullable|integer|min:0',
            'autoneg'       => 'boolean',
            'name'          => 'nullable|string|max:255',
            'description'   => 'nullable|string|max:255',
            'mtu'           => 'nullable|integer|min:0',
            'lag_framing'   => 'boolean',
            'fastlacp'      => 'boolean',
            'busyhost'      => 'boolean',
        ];
    }

    /**
     * Rules replaced outright, rather than added.
     *
     * Kept separate from delta() so RulesParityTest can tell "we added something upstream does
     * not have" from "we deliberately overrode upstream", and check the second is intentional.
     *
     * @return array<string,mixed>
     */
    public static function overrides(): array
    {
        return [
            'ipv4address' => 'nullable|string|max:255',
            'ipv6address' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string,mixed>
     *
     * @psalm-suppress LessSpecificImplementedReturnType
     *      The parent's return type is an inferred array shape. Merging the delta widens it
     *      by construction, so a narrower annotation here is not possible.
     */
    #[\Override]
    public function rules(): array
    {
        return array_merge( parent::rules(), self::delta(), self::overrides() );
    }

    /**
     * Take the customer from the route, and normalise the address fields.
     *
     * @return void
     */
    #[\Override]
    protected function prepareForValidation(): void
    {
        $merge = [];

        if( ( $cust = $this->route( 'cust' ) ) instanceof Customer ) {
            $merge[ 'custid' ] = $cust->id;
        }

        // The wizard's status/speed/duplex are required. Sensible defaults keep a caller from
        // having to restate what is true of every member port; anything unusual is still
        // explicit:
        if( !$this->has( 'status' ) ) {
            $merge[ 'status' ] = PhysicalInterface::STATUS_CONNECTED;
        }

        if( !$this->has( 'duplex' ) ) {
            $merge[ 'duplex' ] = 'full';
        }

        $this->merge( $merge );
    }

    /**
     * Validate the "auto" keyword and address syntax, which the replaced rules no longer cover.
     *
     * Note there is no parent::withValidator() call: StoreVirtualInterfaceWizard does not
     * define one, and Laravel only invokes the hook when it exists. Should upstream add one,
     * it has to be chained in here by hand - RulesParityTest compares rules(), not hooks, so
     * it would not catch that on its own.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     *
     * @return void
     */
    public function withValidator( $validator ): void
    {
        $validator->after( function( $validator ) {
            foreach( [ 'ipv4' => FILTER_FLAG_IPV4, 'ipv6' => FILTER_FLAG_IPV6 ] as $proto => $flag ) {
                $field = $proto . 'address';
                $value = $this->input( $field );

                if( $value === null || $value === '' || strtolower( trim( (string)$value ) ) === 'auto' ) {
                    continue;
                }

                if( filter_var( $value, FILTER_VALIDATE_IP, $flag ) === false ) {
                    $validator->errors()->add( $field, "The {$field} must be a valid {$proto} address or \"auto\"." );
                }
            }
        } );
    }
}
