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
use D2EM;

/**
 * Doctrine2Frontend Functions
 *
 * @see        http://docs.ixpmanager.org/dev/frontend-crud/
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Utils\Http\Controllers\Frontend
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
abstract class DoctrineController extends Frontend {

    /**
     * Provide the Id of the object
     *
     * @return int
     */
    protected function getObjectId()
    {
        return $this->object->getId();
    }

    /**
     * Provide the object via an ID
     *
     * @return object|null
     */
    protected function getObject( int $id )
    {
        return D2EM::getRepository( $this->feParams->entity )->find( $id );
    }

    /**
     * Delete the Object in parameter
     *
     * @param object $object
     *
     * @return void
     *
     * @throws
     */
    protected function deleteObject( object $object )
    {
        D2EM::remove( $object );
        D2EM::flush();
    }
}