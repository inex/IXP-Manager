<?php

namespace IXP\Http\Controllers;

use Entities\User;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use IXP\Models\{DocstoreDirectory, DocstoreFile};

class DocstoreDirectoryController extends Controller
{
    //
    public function list( Request $request, DocstoreDirectory $dir = null ) {

        if( $dir ) {
            $this->authorize( 'view', $dir );
        }

        return view( 'docstore/dir/list', [
            'dir'  => $dir ?? false,
            'dirs' => DocstoreDirectory::getListing( $dir, $request->user() ),
            'files' => DocstoreFile::getListing( $dir, $request->user() ),
        ] );
    }
}
