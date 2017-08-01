<?php

namespace IXP\Http\Controllers\Api\V4;

use Auth, D2EM, Storage;

use Entities\{
    PatchPanelPort              as PatchPanelPortEntity,
    PatchPanelPortHistory       as PatchPanelPortHistoryEntity,
    PatchPanelPortFile          as PatchPanelPortFileEntity,
    PatchPanelPortHistoryFile   as PatchPanelPortHistoryFileEntity,
    PhysicalInterface           as PhysicalInterfaceEntity
};

use GrahamCampbell\Flysystem\FlysystemManager;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;



/**
 * PatchPanelPortController
 *
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   APIv4
 * @package    IXP\Http\Controllers\Api\V4
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PatchPanelPortController extends Controller {


    /**
     * Get the details of a patch panel port
     *
     * @param   int $id    The ID of the patch panel port to query
     * @param   bool $deep Return a deep array by including associated objects
     * @return  JsonResponse JSON customer object
     */
    public function detail( int $id, bool $deep = false ): JsonResponse {

        if( !( $ppp = D2EM::getRepository( PatchPanelPortEntity::class )->find( $id ) ) ) {
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
     * @return  JsonResponse JSON customer object
     */
    public function setNotes( Request $request, int $id ) {

        if( !( $ppp = D2EM::getRepository( PatchPanelPortEntity::class )->find( $id ) ) ) {
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
                /** @var PhysicalInterfaceEntity $pi */
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
        if( !( $ppp = D2EM::getRepository( PatchPanelPortEntity::class )->find($id) ) ) {
            abort(404);
        }

        if( !$request->hasFile('upl') ) {
            abort(400);
        }

        $file = $request->file('upl');

        $pppFile = new PatchPanelPortFileEntity;
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
     * @param  int $fileid patch panel port file ID
     * @return  JsonResponse
     */
    public function deleteFile( int $fileid ){

        /** @var PatchPanelPortFileEntity $pppf */
        if( !( $pppf = D2EM::getRepository( PatchPanelPortFileEntity::class )->find( $fileid ) ) ) {
            abort( 404 );
        }

        $path = 'files/'.$pppf->getPath();

        if( Storage::exists( $path ) && Storage::delete( $path ) ) {
            $pppf->getPatchPanelPort()->removePatchPanelPortFile( $pppf );
            D2EM::remove( $pppf );
            D2EM::flush();
            return response()->json( ['success' => true, 'message' => 'File deleted' ] );
        } else {
            return response()->json( [ 'success' => false, 'message' => 'Error: file could not be deleted' ] );

        }
    }

    /**
     * Delete a patch panel port file history
     *
     * @param  int $fileid patch panel port history file ID
     * @return  JsonResponse
     */
    public function deleteHistoryFile( int $fileid ){

        /** @var PatchPanelPortHistoryFileEntity $ppphf */
        if( !( $ppphf = D2EM::getRepository( PatchPanelPortHistoryFileEntity::class )->find( $fileid ) ) ) {
            abort( 404 );
        }

        $path = 'files/'.$ppphf->getPath();

        if( Storage::exists( $path ) && Storage::delete( $path ) ) {
            $ppphf->getPatchPanelPortHistory()->removePatchPanelPortHistoryFile( $ppphf );
            D2EM::remove( $ppphf );
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
        /** @var PatchPanelPortFileEntity $pppFile */
        if( !( $pppFile = D2EM::getRepository( PatchPanelPortFileEntity::class )->find( $fileid ) ) ) {
            abort( 404 );
        }

        $pppFile->setIsPrivate( !$pppFile->getIsPrivate() );
        D2EM::flush();

        return response()->json( [ 'success' => true, 'isPrivate' => $pppFile->getIsPrivate() ] );
    }


    /**
     * Delete a patch panel port
     *
     * If the patch panel port has a duplex port then it will delete both ports.
     * Also deletes associated files and histories.
     *
     * @param  int $id ID of the patch panel port to delete
     * @return  JsonResponse
     */
    public function delete( int $id ) {

        /** @var PatchPanelPortEntity $ppp */
        if( !( $ppp = D2EM::getRepository( PatchPanelPortEntity::class )->find( $id ) ) ) {
            abort( 404 );
        }

        D2EM::getRepository( PatchPanelPortEntity::class )->delete( $ppp );

        return response()->json( [ 'success' => true ] );
    }


    /**
     * Remove the linked port from the master and reset it as available.
     *
     * @param  int $id ID of the patch panel **master** port from which to split the slave
     * @return  JsonResponse
     */
    public function split( int $id ){

        /** @var PatchPanelPortEntity $ppp */
        if( !( $ppp = D2EM::getRepository( PatchPanelPortEntity::class )->find( $id ) ) ) {
            abort( 404 );
        }

        if( !$ppp->hasSlavePort() ) {
            return response()->json( ['success' => false, 'message' => 'This patch panel port does not have any slave port.']) ;
        }

        $slavePort = $ppp->getDuplexSlavePort();

        $ppp->removeDuplexSlavePort( $slavePort );

        $ppp->setPrivateNotes(
            "### " . date('Y-m-d') . " - ". Auth::user()->getUsername() ."\n\nThis port had a slave port: "
            . $slavePort->getPrefix() . $slavePort->getNumber() . " which was split by " . Auth::user()->getUsername()
            . " on " . date('Y-m-d') . ".\n\n"
            . $ppp->getPrivateNotes()
        );

        $slavePort->resetPatchPanelPort();
        $slavePort->setPrivateNotes(
            "### " . date('Y-m-d') . " - ". Auth::user()->getUsername() ."\n\nThis port was a duplex slave port with "
                . $ppp->getPrefix() . $ppp->getNumber() . " and was split by " . Auth::user()->getUsername()
                . " on " . date('Y-m-d') . ".\n\n"
        );

        D2EM::flush();

        return response()->json( [ 'success' => true, 'message' => 'The patch Panel port has been successfully split.' ] );
    }
}
