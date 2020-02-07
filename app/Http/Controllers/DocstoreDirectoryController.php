<?php

namespace IXP\Http\Controllers;

use Illuminate\Http\Request;

use IXP\Models\DocstoreDirectory;

class DocstoreDirectoryController extends Controller
{
    //
    public function list( Request $request, DocstoreDirectory $dir = null ) {

        if( $dir ) {
            $this->authorize( 'can-access', $dir );
        }

        return view( 'docstore/dir/list', [
            'dir'  => $dir ?? false,
            'dirs' => DocstoreDirectory::where( 'min_privs', '<=', $request->user()->getPrivs() )
                        ->where('parent_dir_id', $dir ? $dir->id : null )->orderBy('name')->get(),
            'files' => $dir ? $dir->files()->where('min_privs', '<=', $request->user()->getPrivs() )->orderBy('name')->get() : [],
        ] );
    }

    public function listFiles( Request $request, DocstoreDirectory $dir ) {
        $this->authorize( 'can-access', $dir );

        return view( 'docstore/dir/list-files', [
            'dir'   => $dir,
            'files' => $dir->files()->where('min_privs', '<=', $request->user()->getPrivs() )->orderBy('name')->get()
        ]);
    }
}
