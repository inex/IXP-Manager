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
     * Only the address *type* is relaxed here, never the requirement. The wizard rule is
     * `ipv4|required` when the family is enabled; "auto" is not an IP, so the `ipv4` part has
     * to go, but the conditional `required` must stay. Dropping it lets a caller create a
     * VLAN interface with ipv4enabled=1 and no address, and that row goes on to render as
     * `neighbor  as 65551;` in the route server configuration - a parse error which invalidates
     * the generated config for the whole VLAN, not merely that peer.
     *
     * @return array<string,mixed>
     */
    public function overrides(): array
    {
        return [
            'ipv4address' => ( $this->boolean( 'ipv4enabled' ) ? 'required' : 'nullable' ) . '|string|max:255',
            'ipv6address' => ( $this->boolean( 'ipv6enabled' ) ? 'required' : 'nullable' ) . '|string|max:255',
        ];
    }

    /**
     * The fields overrides() replaces, without evaluating the rules.
     *
     * Lets callers and tests reason about which upstream rules are shadowed without needing a
     * populated request instance.
     *
     * @return array<int,string>
     */
    public static function overriddenFields(): array
    {
        return [ 'ipv4address', 'ipv6address' ];
    }

    /**
     * Check the address fields of a connection payload for syntax.
     *
     * Static and payload-driven so that the onboarding endpoint, whose connection lives in a
     * nested key, applies exactly the same check rather than a second copy of it.
     *
     * Non-string values are skipped: an array where a string belongs is already caught by the
     * inherited `string` rule, and casting one here would be a fatal error, turning a 422 into
     * a 500.
     *
     * @param  array  $payload
     *
     * @return array<string,string>  field => message, empty when everything is acceptable
     */
    public static function addressSyntaxErrors( array $payload ): array
    {
        $errors = [];

        foreach( [ 'ipv4' => FILTER_FLAG_IPV4, 'ipv6' => FILTER_FLAG_IPV6 ] as $proto => $flag ) {
            $field = $proto . 'address';
            $value = $payload[ $field ] ?? null;

            if( !is_string( $value ) || $value === '' || strtolower( trim( $value ) ) === 'auto' ) {
                continue;
            }

            if( filter_var( $value, FILTER_VALIDATE_IP, $flag ) === false ) {
                $errors[ $field ] = "The {$field} must be a valid {$proto} address or \"auto\".";
            }
        }

        return $errors;
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
        return array_merge( parent::rules(), self::delta(), $this->overrides() );
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
            foreach( self::addressSyntaxErrors( $this->all() ) as $field => $message ) {
                $validator->errors()->add( $field, $message );
            }
        } );
    }
}
