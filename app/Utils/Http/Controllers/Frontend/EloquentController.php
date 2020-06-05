<?php

namespace IXP\Utils\Http\Controllers\Frontend;

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

/**
 * EloquentController Functions
 *
 * Based on Barry's original code from:
 *     https://github.com/opensolutions/OSS-Framework/blob/master/src/OSS/Controller/Action/Trait/Doctrine2Frontend.php
 *
 *
 * @see        http://docs.ixpmanager.org/dev/frontend-crud/
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Utils\Http\Controllers\Frontend
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
abstract class EloquentController extends Frontend {

    /**
     * Provide the Id of the object
     *
     * @return int
     */
    protected function getObjectId()
    {
        return $this->object->id;
    }

    /**
     * Provide the object for an ID
     *
     * @param int $id ID of the object to retrieve
     *
     * @return object|null
     */
    protected function getObject( int $id )
    {
        return $this->feParams->entity::find( $id );
    }

    /**
     * Delete the Object in parameter
     *
     * @param object $object
     *
     * @return void
     */
    protected function deleteObject( object $object )
    {
        $object->delete();
    }
}