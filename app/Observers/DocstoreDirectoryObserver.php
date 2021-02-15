<?php

namespace IXP\Observers;

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

use Illuminate\Support\Facades\Cache;

use IXP\Models\{
    DocstoreDirectory,
    User
};

/**
 * DocstoreDirectoryObserver
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Observers
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class DocstoreDirectoryObserver
{
    /**
     * @return void
     */
    private function clearCacheOfHierarchiesForUserClasses(): void
    {
        foreach( User::$PRIVILEGES_ALL as $priv => $privname ) {
            Cache::forget( DocstoreDirectory::CACHE_KEY_FOR_USER_CLASS_HIERARCHY . $priv );
        }
    }

    /**
     * Handle the docstore directory "created" event.
     *
     * @param DocstoreDirectory $docstoreDirectory
     *
     * @return void
     */
    public function created( DocstoreDirectory $docstoreDirectory ): void
    {
        $this->clearCacheOfHierarchiesForUserClasses();
    }

    /**
     * Handle the docstore directory "updated" event.
     *
     * @param DocstoreDirectory $docstoreDirectory
     *
     * @return void
     */
    public function updated( DocstoreDirectory $docstoreDirectory ): void
    {
        $this->clearCacheOfHierarchiesForUserClasses();
    }

    /**
     * Handle the docstore directory "deleted" event.
     *
     * @param DocstoreDirectory $docstoreDirectory
     *
     * @return void
     */
    public function deleted( DocstoreDirectory $docstoreDirectory ): void
    {
        $this->clearCacheOfHierarchiesForUserClasses();
    }

    /**
     * Handle the docstore directory "restored" event.
     *
     * @param DocstoreDirectory $docstoreDirectory
     *
     * @return void
     */
    public function restored( DocstoreDirectory $docstoreDirectory ): void
    {
        $this->clearCacheOfHierarchiesForUserClasses();
    }

    /**
     * Handle the docstore directory "force deleted" event.
     *
     * @param DocstoreDirectory $docstoreDirectory
     *
     * @return void
     */
    public function forceDeleted( DocstoreDirectory $docstoreDirectory ): void
    {
        $this->clearCacheOfHierarchiesForUserClasses();
    }
}