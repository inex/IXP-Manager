<?php

namespace IXP\Observers;

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
use Illuminate\Support\Facades\Cache;

use IXP\Models\{
    DocstoreDirectory,
    User
};

class DocstoreDirectoryObserver
{
    private function clearCacheOfHierarchiesForUserClasses()
    {
        foreach( User::$PRIVILEGES_ALL as $priv => $privname ) {
            Cache::forget( DocstoreDirectory::CACHE_KEY_FOR_USER_CLASS_HIERARCHY . $priv );
        }
    }

    /**
     * Handle the docstore directory "created" event.
     *
     * @param  \IXP\Models\DocstoreDirectory  $docstoreDirectory
     * @return void
     */
    public function created(DocstoreDirectory $docstoreDirectory)
    {
        $this->clearCacheOfHierarchiesForUserClasses();
    }

    /**
     * Handle the docstore directory "updated" event.
     *
     * @param  \IXP\Models\DocstoreDirectory  $docstoreDirectory
     * @return void
     */
    public function updated(DocstoreDirectory $docstoreDirectory)
    {
        $this->clearCacheOfHierarchiesForUserClasses();
    }

    /**
     * Handle the docstore directory "deleted" event.
     *
     * @param  \IXP\Models\DocstoreDirectory  $docstoreDirectory
     * @return void
     */
    public function deleted(DocstoreDirectory $docstoreDirectory)
    {
        $this->clearCacheOfHierarchiesForUserClasses();
    }

    /**
     * Handle the docstore directory "restored" event.
     *
     * @param  \IXP\Models\DocstoreDirectory  $docstoreDirectory
     * @return void
     */
    public function restored(DocstoreDirectory $docstoreDirectory)
    {
        $this->clearCacheOfHierarchiesForUserClasses();
    }

    /**
     * Handle the docstore directory "force deleted" event.
     *
     * @param  \IXP\Models\DocstoreDirectory  $docstoreDirectory
     * @return void
     */
    public function forceDeleted(DocstoreDirectory $docstoreDirectory)
    {
        $this->clearCacheOfHierarchiesForUserClasses();
    }
}
