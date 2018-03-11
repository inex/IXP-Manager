<?php

namespace IXP\Events\Customer\Note;

/*
 * Copyright (C) 2009-2018 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Entities\{
    CustomerNote as CustomerNoteEntity,
    Customer     as CustomerEntity
};

use IXP\Exceptions\GeneralException;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

abstract class Changed
{
    use Dispatchable, SerializesModels;

    /**
     * old customer note
     * @var CustomerNoteEntity
     */
    protected $ocn;

    /**
     * new customer note
     * @var CustomerNoteEntity
     */
    protected $cn;

    /**
     * @var CustomerEntity
     */
    protected $cust;

    /**
     * @var string
     */
    protected $type;



    /**
     * Create a new event instance.
     *
     * @param CustomerNoteEntity|null   $ocn
     * @param CustomerNoteEntity        $cn
     *
     * @throws GeneralException
     */
    public function __construct( $ocn, $cn )
    {
        $this->ocn   = $ocn;
        $this->cn    = $cn;

        if( $ocn ) {
            $this->cust = $ocn->getCustomer();
        } else if( $cn ) {
            $this->cust = $cn->getCustomer();
        } else {
            throw new GeneralException( "Customer note is missing." );
        }
    }

    /**
     * Get customer
     *
     * @return CustomerEntity
     */
    public function getCustomer(): CustomerEntity {
        return $this->cust;
    }

    /**
     * Get old note
     *
     * @return CustomerNoteEntity|null
     */
    public function getOldNote() {
        return $this->ocn;
    }

    /**
     * Get note
     *
     * @return CustomerNoteEntity
     */
    public function getNote(): CustomerNoteEntity {
        return $this->cn;
    }

    /**
     * Is the event type: a customer note was added
     *
     * @return bool
     */
    public function isTypeAdded() {
        return get_class($this) == Added::class;
    }

    /**
     * Is the event type: a customer note was deleted
     *
     * @return bool
     */
    public function isTypeDeleted() {
        return get_class($this) == Deleted::class;
    }

    /**
     * Is the event type: a customer note was edited
     *
     * @return bool
     */
    public function isTypeEdited() {
        return get_class($this) == Edited::class;
    }
}
