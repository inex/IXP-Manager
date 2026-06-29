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
     * @var Customer
     */
    protected Customer $cust;

    /**
     * @var string
     */
    protected string $type;

    /**
     * Create a new event instance.
     *
     * @param  CustomerNote|null    $ocn
     * @param  CustomerNote         $cn
     * @param User                  $user
     */
    public function __construct(
        protected ?CustomerNote $ocn,
        protected CustomerNote $cn,
        protected User $user )
    {
        $this->cust = $ocn?->customer ?? $cn->customer;
    }

    /**
     * Get customer
     */
    public function customer(): Customer
    {
        return $this->cust;
    }

    /**
     * Get user
     */
    public function user(): User
    {
        return $this->user;
    }

    /**
     * Get old note
     */
    public function oldNote(): ?CustomerNote
    {
        return $this->ocn;
    }

    /**
     * Get note
     */
    public function note(): CustomerNote
    {
        return $this->cn;
    }

    /**
     * Get either note: get the new note if set, otherwise the old note
     */
    public function eitherNote(): CustomerNote
    {
        // for changed events, the new note is always set
        return $this->cn;
    }

    /**
     * Is the event type: a customer note was added
     */
    public function typeCreated(): bool
    {
        return get_class( $this ) === Created::class;
    }

    /**
     * Is the event type: a customer note was deleted
     */
    public function typeDeleted(): bool
    {
        return get_class( $this ) === Deleted::class;
    }

    /**
     * Is the event type: a customer note was edited
     */
    public function typeEdited(): bool
    {
        return get_class( $this ) === Edited::class;
    }

    /**
     * Resolve the type
     *
     * @psalm-return 'Created'|'Deleted'|'Edited'
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