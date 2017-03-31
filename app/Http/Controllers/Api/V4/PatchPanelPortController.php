<?php

namespace IXP\Http\Controllers\Api\V4;

use Auth;

use D2EM;

use Entities\{
    PatchPanelPort, PatchPanelPortFile, PhysicalInterface
};

use GrahamCampbell\Flysystem\FlysystemManager;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;



class PatchPanelPortController extends Controller {


    /**
     * Get the details of a patch panel port
     *
     * @param   int $id    The ID of the patch panel port to query
     * @param   bool $deep Return a deep array by including associated objects
     * @return  JsonResponse
     */
    public function detail( int $id, bool $deep = false ): JsonResponse {

        if( !( $ppp = D2EM::getRepository( PatchPanelPort::class )->find( $id ) ) ) {
            abort( 404, 'No such patch panel port' );
        }

        return response()->json( $ppp->jsonArray($deep) );
    }

    /**
     * Get extra details of a patch panel port
     *
     * @param   int $id    The ID of the patch panel port to query
     * @return  JsonResponse JSON customer object
     */
    public function detailDeep( int $id ): JsonResponse {
        return $this->detail( $id, true );
    }


    /**
     * Set the public and private notes of a patch panel
     *
     * @param   int $id    The ID of the patch panel port to query
     * @return  JsonResponse
     */
    public function setNotes( Request $request, int $id ) {

        if( !( $ppp = D2EM::getRepository( PatchPanelPort::class )->find( $id ) ) ) {
            abort( 404, 'No such patch panel port' );
        }

        if( $request->input('notes', null) !== null ) {
            $ppp->setNotes( clean( $request->input( 'notes' ) ) );
        }

        if( $request->input('private_notes', null) !== null ) {
            $ppp->setPrivateNotes( clean( $request->input( 'private_notes' ) ) );
        }
        D2EM::flush();

        // we may also pass a new state for a physical interface with this request
        // (because we call this function from set connected / set ceased / etc)
        if( $request->input('pi_status') ) {
            if( $ppp->getSwitchPort() && ( $pi = $ppp->getSwitchPort()->getPhysicalInterface() ) ) {
                /** @var PhysicalInterface $pi */
                $pi->setStatus( $request->input( 'pi_status' ) );
            }
            D2EM::flush();
        }

        return response()->json( [ 'success' => true ] );
    }

    /**
     * Upload a file to a patch panel port
     *
     * @param  int $id patch panel port ID
     * @param  Request $request instance of the current HTTP request
     * @param  FlysystemManager $filesystem instance of the file manager
     * @return  JsonResponse
     */
    public function uploadFile( Request $request, FlysystemManager $filesystem, int $id ): JsonResponse {

        if( !( $ppp = D2EM::getRepository( PatchPanelPort::class )->find($id) ) ) {
            abort(404);
        }

        if( !$request->hasFile('upl') ) {
            abort(400);
        }

        $file = $request->file('upl');

        $pppFile = new PatchPanelPortFile;
        $pppFile->setPatchPanelPort( $ppp );
        $pppFile->setStorageLocation( hash('sha256', $ppp->getId() . '-' . $file->getClientOriginalName() ) );
        $pppFile->setName( $file->getClientOriginalName() );
        $pppFile->setUploadedAt( new \DateTime );
        $pppFile->setUploadedBy( Auth::user()->getUsername() );

        $path = $pppFile->getPath();

        if( $filesystem->has( $path ) ) {
            return response()->json( [ 'success' => false, 'message' => 'File of the same name already exists for this port' ] );
        }

        $stream = fopen( $file->getRealPath(), 'r+' );
        if( $filesystem->writeStream( $path, $stream ) ) {

            $pppFile->setSize( $filesystem->getSize($path) );
            $pppFile->setType( $filesystem->getMimetype($path) );
            D2EM::persist( $pppFile );

            $ppp->addPatchPanelPortFile( $pppFile );
            D2EM::flush();
            $resp = [ 'success' => true, 'message' => 'File uploaded and saved.', 'id' => $pppFile->getId() ];
        } else {
            $resp = [ 'success' => false, 'message' => 'Could not save file ti storage location' ];
        }

        fclose($stream);
        return response()->json($resp);
    }


    /**
     * Delete a patch panel port file
     *
     * @param  FlysystemManager $filesystem instance of the file manager
     * @param  int $fileid patch panel port file ID
     * @return  JsonResponse
     */
    public function deleteFile( FlysystemManager $filesystem, int $fileid ){

        /** @var PatchPanelPortFile $pppFile */
        if( !( $pppFile = D2EM::getRepository( PatchPanelPortFile::class )->find( $fileid ) ) ) {
            abort( 404 );
        }

        $path = $pppFile->getPath();

        if( !$filesystem->has( $path ) || $filesystem->delete( $path ) ) {
            $pppFile->getPatchPanelPort()->removePatchPanelPortFile( $pppFile );
            D2EM::remove($pppFile);
            D2EM::flush();
            return response()->json( ['success' => true, 'message' => 'File deleted' ] );
        }

        return response()->json( [ 'success' => false, 'message' => 'Error: file could not be deleted' ] );
    }

    /**
     * Make a patch panel port file private
     *
     * @param  int $fileid patch panel port file ID
     * @return  JsonResponse
     */
    public function toggleFilePrivacy( int $fileid ){
        /** @var PatchPanelPortFile $pppFile */
        if( !( $pppFile = D2EM::getRepository( PatchPanelPortFile::class )->find( $fileid ) ) ) {
            abort( 404 );
        }

        $pppFile->setIsPrivate( !$pppFile->getIsPrivate() );
        D2EM::flush();

        return response()->json( [ 'success' => true, 'isPrivate' => $pppFile->getIsPrivate() ] );
    }

}
