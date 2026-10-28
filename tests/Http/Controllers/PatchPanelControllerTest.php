<?php
/*
 * Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee.
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

declare(strict_types=1);

namespace Tests\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use IXP\Models\PatchPanel;
use IXP\Models\PatchPanelPort;
use IXP\Models\PatchPanelPortFile;
use IXP\Models\PatchPanelPortHistory;
use IXP\Models\PatchPanelPortHistoryFile;
use Tests\TestCase;

class PatchPanelControllerTest extends TestCase
{
    /**
     * An active patch panel cannot be expunged - the user is redirected away with an alert.
     */
    public function testDoExpungeActivePP(): void
    {
        $pp = new PatchPanel();
        $pp->name = "TestPatchPanel";
        $pp->active = 1;
        $pp->save();

        $ppPort1      = $this->createPatchPanelPort( $pp );
        $ppPort1File1 = $this->createPatchPanelPortFile( $ppPort1 );

        Storage::fake();
        Storage::put('files/' . $ppPort1File1->path(), "Hello World");

        $this->actingAs( $this->getSuperUser() )
            ->post(route("patch-panel@do-expunge", [ 'pp' => $pp->id, ]))
            ->assertRedirect(route( 'patch-panel@list', [ 'pp' => $pp ]));

        $alerts = app('session.store')->get('ixp.utils.view.alerts');
        $this->assertCount(1, $alerts);
        $this->assertEquals( 'TestPatchPanel is active and cannot be expunged.', $alerts[0]->message() );
        $this->assertEquals( 'danger', $alerts[0]->class() );

        $this->assertDatabaseHas('patch_panel', ['id' => $pp->id]);

        Storage::assertExists('files/' . $ppPort1File1->path(), "Hello World");
    }

    /**
     * Test successful expunging of a patch panel. DB records + files are deleted, and the
     * relevant alert + logs are created.
     */
    public function testDoExpungeSuccess(): void
    {
        $pp = new PatchPanel();
        $pp->active = 0;
        $pp->name = "TestPatchPanel";
        $pp->save();

        $ppPort1 = $this->createPatchPanelPort( $pp );
        $ppPort2 = $this->createPatchPanelPort( $pp );
        $ppPort3 = $this->createPatchPanelPort( $pp );

        $ppPort1File1 = $this->createPatchPanelPortFile( $ppPort1 );
        $ppPort1File2 = $this->createPatchPanelPortFile( $ppPort1 );

        $pp2History1 = $this->createPatchPanelPortHistory( $ppPort2 );
        $pp2History1File1 = $this->createPatchPanelPortHistoryFile( $pp2History1 );

        Storage::fake();
        Storage::put('files/' . $ppPort1File1->path(), "Hello World");
        Storage::put('files/' . $ppPort1File2->path(), "Hello World x2");
        Storage::put('files/' . $pp2History1File1->path(), "Hello World x3");

        Log::spy();

        $this->actingAs( $this->getSuperUser() )
            ->post(route("patch-panel@do-expunge", [ 'pp' => $pp->id ]))
            ->assertRedirect(route( "patch-panel@list-inactive" ));

        Log::shouldHaveReceived('notice')
            ->with("Expunged patch panel {$pp->name} - with 3 ports, 2 port files, 1 port histories, and 1 port history files");

        Log::shouldHaveReceived('debug')
            ->with("Deleted all expunged patch panel files from filesystem (for panel {$pp->name})");

        $alerts = app('session.store')->get('ixp.utils.view.alerts');
        $this->assertCount(1, $alerts);
        $this->assertEquals( 'The patch panel has been expunged.', $alerts[0]->message() );
        $this->assertEquals( 'success', $alerts[0]->class() );

        $this->assertDatabaseMissing('patch_panel', ['id' => $pp->id]);

        $this->assertDatabaseMissing('patch_panel_port', ['id' => $ppPort1->id]);
        $this->assertDatabaseMissing('patch_panel_port', ['id' => $ppPort2->id]);
        $this->assertDatabaseMissing('patch_panel_port', ['id' => $ppPort3->id]);

        $this->assertDatabaseMissing('patch_panel_port_file', ['id' => $ppPort1File1->id]);
        $this->assertDatabaseMissing('patch_panel_port_file', ['id' => $ppPort1File2->id]);

        $this->assertDatabaseMissing('patch_panel_port_history', ['id' => $pp2History1->id]);

        $this->assertDatabaseMissing('patch_panel_port_history_file', ['id' => $pp2History1File1->id]);

        Storage::assertMissing('files/' . $ppPort1File1->path() );
        Storage::assertMissing('files/' . $ppPort1File2->path() );
        Storage::assertMissing('files/' . $pp2History1File1->path() );
    }

    /**
     * This test covers what happens if a file deletion fails with a return value. This is decided in config/filesystem.php
     * and matches our current configuration.
     * The endpoint handles the error by logging the condition and which files failed to delete. An alert is also shown.
     */
    public function testDoExpungeDeleteFail1(): void
    {
        $pp = new PatchPanel();
        $pp->active = 0;
        $pp->name = "TestPatchPanel";
        $pp->save();

        $ppPort1 = $this->createPatchPanelPort( $pp );
        $ppPort2 = $this->createPatchPanelPort( $pp );
        $ppPort3 = $this->createPatchPanelPort( $pp );

        $ppPort1File1 = $this->createPatchPanelPortFile( $ppPort1 );

        Storage::expects('delete')->with('files/' . $ppPort1File1->path())->andReturn(false);

        Log::spy();

        $this->actingAs( $this->getSuperUser() )
            ->post(route("patch-panel@do-expunge", [ 'pp' => $pp->id ]))
            ->assertRedirect(route( "patch-panel@list-inactive" ));

        Log::shouldHaveReceived('notice')
            ->with("Expunged patch panel {$pp->name} - with 3 ports, 1 port files, 0 port histories, and 0 port history files");

        Log::shouldHaveReceived('error')
            ->with("Error deleting 1 of 1 expunged patch panel files from filesystem (for panel TestPatchPanel)", [
                $ppPort1File1->path()
            ]);

        $alerts = app('session.store')->get('ixp.utils.view.alerts');
        $this->assertCount(2, $alerts);
        $this->assertEquals( 'The patch panel has been expunged.', $alerts[0]->message() );
        $this->assertEquals( 'success', $alerts[0]->class() );

        $this->assertEquals( 'Some errors occurred while deleting files from the filesystem. See application logs for details.', $alerts[1]->message() );
        $this->assertEquals( 'warning', $alerts[1]->class() );

        $this->assertDatabaseMissing('patch_panel', ['id' => $pp->id]);

        $this->assertDatabaseMissing('patch_panel_port', ['id' => $ppPort1->id]);
        $this->assertDatabaseMissing('patch_panel_port', ['id' => $ppPort2->id]);
        $this->assertDatabaseMissing('patch_panel_port', ['id' => $ppPort3->id]);

        $this->assertDatabaseMissing('patch_panel_port_file', ['id' => $ppPort1File1->id]);
    }

    /**
     * This test covers what happens if a file deletion fails with an exception - decided in config/filesystem.php. It doesn't
     * match our configuration but the test is included for completeness.
     * The endpoint handles the error by logging the condition and which files failed to delete. An alert is also shown.
     */
    public function testDoExpungeDeleteFail2(): void
    {
        $pp = new PatchPanel();
        $pp->active = 0;
        $pp->name = "TestPatchPanel";
        $pp->save();

        $ppPort1 = $this->createPatchPanelPort( $pp );
        $ppPort2 = $this->createPatchPanelPort( $pp );
        $ppPort3 = $this->createPatchPanelPort( $pp );

        $ppPort1File1 = $this->createPatchPanelPortFile( $ppPort1 );

        Storage::expects('delete')->with('files/' . $ppPort1File1->path())->andThrow( new \Exception() );

        Log::spy();

        $this->actingAs( $this->getSuperUser() )
            ->post(route("patch-panel@do-expunge", [ 'pp' => $pp->id, ]))
            ->assertRedirect(route( "patch-panel@list-inactive" ));

        Log::shouldHaveReceived('notice')
            ->with("Expunged patch panel {$pp->name} - with 3 ports, 1 port files, 0 port histories, and 0 port history files");

        Log::shouldHaveReceived('error')
            ->with("Error deleting 1 of 1 expunged patch panel files from filesystem (for panel TestPatchPanel)", [
                $ppPort1File1->path(),
            ]);

        $alerts = app('session.store')->get('ixp.utils.view.alerts');
        $this->assertCount(2, $alerts);
        $this->assertEquals( 'The patch panel has been expunged.', $alerts[0]->message() );
        $this->assertEquals( 'success', $alerts[0]->class() );

        $this->assertEquals( 'Some errors occurred while deleting files from the filesystem. See application logs for details.', $alerts[1]->message() );
        $this->assertEquals( 'warning', $alerts[1]->class() );

        $this->assertDatabaseMissing('patch_panel', ['id' => $pp->id]);

        $this->assertDatabaseMissing('patch_panel_port', ['id' => $ppPort1->id]);
        $this->assertDatabaseMissing('patch_panel_port', ['id' => $ppPort2->id]);
        $this->assertDatabaseMissing('patch_panel_port', ['id' => $ppPort3->id]);

        $this->assertDatabaseMissing('patch_panel_port_file', ['id' => $ppPort1File1->id]);
    }

    /**
     * Test loading the expunge form. Checks the right view is displayed and the data provided to the view is correct.
     */
    public function testExpunge(): void
    {
        $pp = new PatchPanel();
        $pp->active = 0;
        $pp->name = "TestPatchPanel";
        $pp->save();

        $ppPort1 = $this->createPatchPanelPort( $pp );
        $ppPort2 = $this->createPatchPanelPort( $pp );
        $ppPort3 = $this->createPatchPanelPort( $pp );

        $ppPort1File1 = $this->createPatchPanelPortFile( $ppPort1 );
        $ppPort1File2 = $this->createPatchPanelPortFile( $ppPort1 );

        $pp2History1 = $this->createPatchPanelPortHistory( $ppPort2 );
        $pp2History1File1 = $this->createPatchPanelPortHistoryFile( $pp2History1 );
        $pp2History1File2 = $this->createPatchPanelPortHistoryFile( $pp2History1 );
        $pp2History1File3 = $this->createPatchPanelPortHistoryFile( $pp2History1 );
        $pp2History1File4 = $this->createPatchPanelPortHistoryFile( $pp2History1 );

        Storage::fake();
        Storage::put('files/' . $ppPort1File1->path(), "Hello World");

        $this->actingAs( $this->getSuperUser() )
            ->get(route("patch-panel@expunge", [ 'pp' => $pp->id, ]))
            ->assertViewIs('patch-panel.expunge')
            ->assertViewHas('numPorts', 3)
            ->assertViewHas('numFiles', 2)
            ->assertViewHas('numHistories', 1)
            ->assertViewHas('numHistoryFiles', 4)
        ;
    }

    /**
     * Test loading the expunge form for an inactive patch panel. The user is redirected away with an alert.
     */
    public function testExpungeInactive(): void
    {
        $pp = new PatchPanel();
        $pp->active = 1;
        $pp->name = "TestPatchPanel";
        $pp->save();

        $this->actingAs( $this->getSuperUser() )
            ->post(route("patch-panel@do-expunge", [ 'pp' => $pp->id, ]))
            ->assertRedirect(route( 'patch-panel@list', [ 'pp' => $pp ]))
        ;

        $alerts = app('session.store')->get('ixp.utils.view.alerts');
        $this->assertCount(1, $alerts);
        $this->assertEquals( 'TestPatchPanel is active and cannot be expunged.', $alerts[0]->message() );
        $this->assertEquals( 'danger', $alerts[0]->class() );
    }

    private function createPatchPanelPort(PatchPanel $pp): PatchPanelPort
    {
        $ppPort = new PatchPanelPort();
        $ppPort->patch_panel_id = $pp->id;
        $ppPort->save();
        return $ppPort;
    }

    private function createPatchPanelPortFile(PatchPanelPort $ppPort ): PatchPanelPortFile
    {
        $pppFile = new PatchPanelPortFile();
        $pppFile->patch_panel_port_id = $ppPort->id;
        $pppFile->storage_location = bin2hex(random_bytes(32));
        $pppFile->save();
        return $pppFile;
    }

    private function createPatchPanelPortHistory(PatchPanelPort $ppPort): PatchPanelPortHistory
    {
        $pppHistory = new PatchPanelPortHistory();
        $pppHistory->patch_panel_port_id = $ppPort->id;
        $pppHistory->save();
        return $pppHistory;
    }

    private function createPatchPanelPortHistoryFile( PatchPanelPortHistory $ppHistory ): PatchPanelPortHistoryFile
    {
        $pppFile = new PatchPanelPortHistoryFile();
        $pppFile->patch_panel_port_history_id = $ppHistory->id;
        $pppFile->storage_location = bin2hex(random_bytes(32));
        $pppFile->save();
        return $pppFile;
    }
}