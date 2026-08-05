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

use Auth;

use Illuminate\Foundation\Http\FormRequest;

use IXP\Models\User;

/**
 * Provisioning API - StoreOnboarding request
 *
 * Validates a composite order: a member, optionally a user, optionally a connection.
 *
 * The three sections are nested rather than flattened, so that field names cannot collide -
 * `name` means something different for a member, a user and a virtual interface. Each section
 * is validated with the same rules as its dedicated endpoint, re-keyed under its prefix, so
 * there is still exactly one definition of what a valid member is.
 *
 * @author     KleyReX
 * @category   IXP
 * @package    IXP\Http\Requests\Api\V4\Provisioning
 * @copyright  Copyright (C) 2026 KleyReX
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class StoreOnboarding extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        /** @var User $us */
        $us = Auth::getUser();

        // the route middleware already asserts superuser, so this can only agree with it:
        return $us->isSuperUser();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * Each section reuses the rules of its own endpoint, prefixed. Doing it this way means a
     * rule added upstream reaches this endpoint too, without anybody remembering to copy it.
     *
     * @return array<string,mixed>
     */
    public function rules(): array
    {
        $rules = [
            'reference'  => 'nullable|string|max:191',
            'member'     => 'required|array',
            'user'       => 'nullable|array',
            'connection' => 'nullable|array',
        ];

        foreach( $this->sectionRules() as $section => $sectionRules ) {
            foreach( $sectionRules as $field => $rule ) {
                $rules[ "{$section}.{$field}" ] = $rule;
            }
        }

        return $rules;
    }

    /**
     * The rules of each section, taken from the dedicated request classes.
     *
     * The sub-requests are constructed with this request's section payload so that rules which
     * depend on the input - the customer type, whether an address family is enabled - resolve
     * the same way they would at the dedicated endpoint.
     *
     * @return array<string,array<string,mixed>>
     */
    private function sectionRules(): array
    {
        $sections = [
            'member' => StoreCustomer::create( '/', 'POST', (array)$this->input( 'member', [] ) ),
        ];

        if( $this->has( 'user' ) ) {
            $sections[ 'user' ] = StoreUser::create( '/', 'POST', (array)$this->input( 'user', [] ) );
        }

        if( $this->has( 'connection' ) ) {
            $sections[ 'connection' ] = StoreConnection::create( '/', 'POST', (array)$this->input( 'connection', [] ) );
        }

        $out = [];

        foreach( $sections as $name => $request ) {
            $out[ $name ] = $request->rules();
        }

        // custid is supplied by the member we are about to create, so it cannot be required
        // of the caller here:
        unset( $out[ 'user' ][ 'custid' ], $out[ 'connection' ][ 'custid' ] );

        return $out;
    }

    /**
     * Apply the defaults the connection section would get at its own endpoint.
     *
     * @return void
     */
    #[\Override]
    protected function prepareForValidation(): void
    {
        if( !$this->has( 'connection' ) ) {
            return;
        }

        $connection = (array)$this->input( 'connection' );

        $connection[ 'status' ] ??= \IXP\Models\PhysicalInterface::STATUS_CONNECTED;
        $connection[ 'duplex' ] ??= 'full';

        $this->merge( [ 'connection' => $connection ] );
    }

    /**
     * Validate address syntax in the connection section, mirroring StoreConnection.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     *
     * @return void
     */
    public function withValidator( $validator ): void
    {
        $validator->after( function( $validator ) {
            if( !$this->has( 'connection' ) ) {
                return;
            }

            foreach( [ 'ipv4' => FILTER_FLAG_IPV4, 'ipv6' => FILTER_FLAG_IPV6 ] as $proto => $flag ) {
                $field = $proto . 'address';
                $value = $this->input( "connection.{$field}" );

                if( $value === null || $value === '' || strtolower( trim( (string)$value ) ) === 'auto' ) {
                    continue;
                }

                if( filter_var( $value, FILTER_VALIDATE_IP, $flag ) === false ) {
                    $validator->errors()->add(
                        "connection.{$field}",
                        "The {$field} must be a valid {$proto} address or \"auto\"."
                    );
                }
            }
        } );
    }
}
