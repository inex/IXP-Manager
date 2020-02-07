<?php

namespace IXP\Http\Controllers;

use Illuminate\Http\Request;

use IXP\Models\DocstoreDirectory;

class DocstoreDirectoryController extends Controller
{
    //
    public function list( Request $request ) {
        return view( 'docstore/dir/list', [
            'dirs' => DocstoreDirectory::where( 'min_privs', '<=', $request->user()->getPrivs() )->orderBy('name')->get()
        ]);
    }

    public function listFiles( Request $request, DocstoreDirectory $dir ) {
        $this->authorize( 'can-access', $dir );

        return view( 'docstore/dir/list-files', [
            'dir'   => $dir,
            'files' => $dir->files()->where('min_privs', '<=', $request->user()->getPrivs() )->orderBy('name')->get()
        ]);
    }
}
