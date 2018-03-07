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

use Exception;
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
     * @var string
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
     * @throws
     */
    public function __construct( $ocn, $cn )
    {
        $this->ocn   = $ocn;
        $this->cn    = $cn;

        if( $ocn ){
            $cust = $ocn->getCustomer();
        } else if( $cn ) {
            $cust = $cn->getCustomer();
        } else {
            throw new Exception( "Customer note is missing." );
        }

        $this->cust  = $cust;

    }

    /**
     * Get customer
     *
     * @return CustomerEntity
     */
    public function getCustomer(){
        return $this->cust;
    }

    /**
     * Get old note
     *
     * @return CustomerNoteEntity
     */
    public function getOldNote(){
        return $this->ocn;
    }

    /**
     * Get note
     *
     * @return CustomerNoteEntity
     */
    public function getNote(){
        return $this->cn;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getType(){
        return $this->type;
    }
}
