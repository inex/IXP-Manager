<?php

namespace IXP\Http\Controllers;

use Entities\User;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use IXP\Models\{DocstoreDirectory, DocstoreFile};

use IXP\Utils\View\Alert\{Alert, Container as AlertContainer};

class DocstoreDirectoryController extends Controller
{
    //
    public function list( Request $request, DocstoreDirectory $dir = null ) {
        return view( 'docstore/dir/list', [
            'dir'  => $dir ?? false,
            'dirs' => DocstoreDirectory::getListing( $dir, $request->user() ),
            'files' => DocstoreFile::getListing( $dir, $request->user() ),
        ] );
    }


    public function create( Request $request )
    {
        $this->authorize( 'create', DocstoreDirectory::class );

        return view( 'docstore/dir/create', [
            'dir' => false,
            'parent_dir' => $request->input( 'parent_dir', false )
        ] );
    }


    public function store( Request $request ) {
        $this->authorize( 'create', DocstoreDirectory::class );

        $request->validate([
            'name' => 'required|max:100',
            'description' => 'nullable'
        ]);

        $dir = DocstoreDirectory::create( [ 'name' => $request->name, 'description' => $request->description ] );

        AlertContainer::push( "New directory <em>{$request->name}</em> created.", Alert::SUCCESS );
        return redirect( route( 'docstore-dir@list', [ 'dir' => $dir->parent_dir_id ] ) );
    }
}
