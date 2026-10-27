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
    public function testDoExpungeActivePP(): void
    {
        $pp = new PatchPanel();
        $pp->name = "TestPatchPanel";
        $pp->active = true;
        $pp->save();

        $ppPort1 = $this->createPatchPanelPort( $pp );
        $ppPort1File1 = $this->createPatchPanelPortFile( $ppPort1 );

        Storage::fake();
        Storage::put('files/' . $ppPort1File1->path(), "Hello World");

        $this->actingAs( $this->getSuperUser() )
            ->post(route("patch-panel@do-expunge", [
                'pp' => $pp->id,
            ]))
            ->assertRedirect(route( 'patch-panel@list', [ 'pp' => $pp ]));

        $alerts = app('session.store')->get('ixp.utils.view.alerts');
        $this->assertCount(1, $alerts);
        $this->assertEquals( 'TestPatchPanel is active and cannot be expunged.', $alerts[0]->message() );
        $this->assertEquals( 'danger', $alerts[0]->class() );

        $this->assertDatabaseHas('patch_panel', ['id' => $pp->id]);

        Storage::assertExists('files/' . $ppPort1File1->path(), "Hello World");
    }

    public function testDoExpungeSuccess(): void
    {
        $pp = new PatchPanel();
        $pp->active = false;
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
            ->post(route("patch-panel@do-expunge", [
                'pp' => $pp->id,
            ]))
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

    private function createPatchPanelPort(PatchPanel $pp): PatchPanelPort
    {
        $ppPort = new PatchPanelPort();
        $ppPort->patch_panel_id = $pp->id;
        $ppPort->save();
        return $ppPort;
    }

    private function createPatchPanelPortFile(PatchPanelPort $ppPort, ?string $location = null): PatchPanelPortFile
    {
        $pppFile = new PatchPanelPortFile();
        $pppFile->patch_panel_port_id = $ppPort->id;
        $pppFile->storage_location = $location ?? bin2hex(random_bytes(32));
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

    private function createPatchPanelPortHistoryFile( PatchPanelPortHistory $ppHistory, ?string $location = null): PatchPanelPortHistoryFile
    {
        $pppFile = new PatchPanelPortHistoryFile();
        $pppFile->patch_panel_port_history_id = $ppHistory->id;
        $pppFile->storage_location = $location ?? bin2hex(random_bytes(32));
        $pppFile->save();
        return $pppFile;
    }

}