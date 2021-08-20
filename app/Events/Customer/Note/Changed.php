<?php

namespace IXP\Events\Customer\Note;

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

use IXP\Exceptions\GeneralException;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use IXP\Models\{
    Customer,
    CustomerNote,
    User
};

/**
 * Changed
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Events\Customer\Note
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
abstract class Changed implements ShouldQueue
{
    use Dispatchable, SerializesModels, InteractsWithQueue;

    /**
     * old customer note
     *
     * @var CustomerNote
     */
    protected $ocn;

    /**
     * new customer note
     *
     * @var CustomerNote
     */
    protected $cn;

    /**
     * @var Customer
     */
    protected $cust;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var User
     */
    protected $user;

    /**
     * Create a new event instance.
     *
     * @param  CustomerNote|null    $ocn
     * @param  CustomerNote         $cn
     * @param User                  $user
     *
     * @throws GeneralException
     */
    public function __construct( CustomerNote $ocn = null , CustomerNote $cn, User $user )
    {
        $this->ocn      = $ocn;
        $this->cn       = $cn;
        $this->user     = $user;

        if( $ocn ) {
            $this->cust = $ocn->customer;
        } else if( $cn ) {
            $this->cust = $cn->customer;
        } else {
            throw new GeneralException( "Customer note is missing." );
        }
    }

    /**
     * Get customer
     *
     * @return Customer
     */
    public function customer(): Customer
    {
        return $this->cust;
    }

    /**
     * Get customer
     *
     * @return User
     */
    public function user(): User
    {
        return $this->user;
    }

    /**
     * Get old note
     *
     * @return CustomerNote|null
     */
    public function oldNote(): ?CustomerNote
    {
        return $this->ocn;
    }

    /**
     * Get note
     *
     * @return CustomerNote
     */
    public function note(): CustomerNote
    {
        return $this->cn;
    }

    /**
     * Get either note: get the new note if set, otherwise the old note
     *
     * @return CustomerNote
     */
    public function eitherNote(): CustomerNote
    {
        return $this->cn ?: $this->ocn;
    }

    /**
     * Is the event type: a customer note was added
     *
     * @return bool
     */
    public function typeCreated(): bool
    {
        return get_class( $this ) === Created::class;
    }

    /**
     * Is the event type: a customer note was deleted
     *
     * @return bool
     */
    public function typeDeleted(): bool
    {
        return get_class( $this ) === Deleted::class;
    }

    /**
     * Is the event type: a customer note was edited
     *
     * @return bool
     */
    public function typeEdited(): bool
    {
        return get_class( $this ) === Edited::class;
    }

    /**
     * Resolve the type
     *
     * @return string
     */
    public function actionDescription(): string
    {
        if( $this->typeCreated() ){
            return "Created";
        }
        if( $this->typeEdited() ){
            return "Edited";
        }
        return "Deleted";
    }
}