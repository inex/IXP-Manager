<?php

namespace IXP\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

use Illuminate\Database\Eloquent\Collection;
use IXP\Models\DocstoreDirectory;

class DocstoreDirectoryController extends Controller
{
    //
    public function list( Request $request, DocstoreDirectory $dir = null ) {

        if( $dir ) {
            $this->authorize( 'can-access', $dir );

            $files = $dir->files()->where('min_privs', '<=', $request->user()->getPrivs() )->orderBy('name')
                ->where('docstore_directory_id', $dir ? $dir->id : null )->orderBy('name')
                ->withCount([ 'logs as downloads_count', 'logs as unique_downloads_count' => function( Builder $query ) {
                    //$query->select([ 'docstore_file_id', 'downloaded_by'] )->distinct('downloaded_by');
                }])->get();

        } else {
            $files = new Collection;
        }

        return view( 'docstore/dir/list', [
            'dir'  => $dir ?? false,
            'dirs' => DocstoreDirectory::where( 'min_privs', '<=', $request->user()->getPrivs() )
                        ->where('parent_dir_id', $dir ? $dir->id : null )->orderBy('name')->get(),
            'files' => $files,
        ] );
    }
}
