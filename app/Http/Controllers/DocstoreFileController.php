<?php

namespace IXP\Http\Controllers;

use Illuminate\Http\Request;

use IXP\Models\{DocstoreFile, DocstoreLog};

use Storage;

class DocstoreFileController extends Controller
{
    public function download( Request $request, DocstoreFile $file ) {
        $this->authorize( 'view', $file );

        $file->logs()->save( new DocstoreLog(['downloaded_by' => $request->user()->getId()]));

        return Storage::disk($file->disk)->download($file->path, $file->name);
    }




}
