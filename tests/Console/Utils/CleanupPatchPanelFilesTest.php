<?php

namespace Tests\Console\Utils;

use Illuminate\Support\Facades\Storage;
use IXP\Models\PatchPanel;
use IXP\Models\PatchPanelPort;
use IXP\Models\PatchPanelPortFile;
use IXP\Models\PatchPanelPortHistory;
use IXP\Models\PatchPanelPortHistoryFile;
use League\Flysystem\UnableToDeleteFile;
use Tests\TestCase;

class CleanupPatchPanelFilesTest extends TestCase
{
    public function testDirRootNotCreated(): void
    {
        Storage::fake();

        $this->artisan( 'utils:cleanup-patch-panel-files', [ '-v' => true ] )
            ->expectsOutput( "Directory does not yet exist - files/ppp" );

        $this->artisan( 'utils:cleanup-patch-panel-files' )
            ->expectsOutput( "Directory does not yet exist - files/ppp") ;
    }

    public function testLevel1DirectoryWrongFormat(): void
    {
        Storage::fake();
        Storage::put( 'files/ppp/a-directory/somefile', 'hello world' );

        $this->artisan( 'utils:cleanup-patch-panel-files', ['-v' => true] )
            ->expectsOutput( "SKIP files/ppp/a-directory - doesn't match shard format" );

        $this->artisan( 'utils:cleanup-patch-panel-files' )
            ->doesntExpectOutput();

        Storage::assertExists( 'files/ppp/a-directory/somefile' );
    }

    public function testLevel2DirectoryWrongFormat(): void
    {
        Storage::fake();

        Storage::put('files/ppp/a/a-directory/somefile', 'hello world');

        $this->artisan('utils:cleanup-patch-panel-files', ['-v' => true] )
            ->expectsOutput("SKIP files/ppp/a/a-directory - doesn't match shard format");

        $this->artisan('utils:cleanup-patch-panel-files', [] )
            ->doesntExpectOutput();

        Storage::assertExists('files/ppp/a/a-directory/somefile');
    }

    public function testFileNameIsNotAHash(): void
    {
        Storage::fake();
        Storage::put('files/ppp/a/a/some-filename.txt', 'hello world');

        $this->artisan('utils:cleanup-patch-panel-files', ['-v' => true] )
            ->expectsOutput("SKIP files/ppp/a/a/some-filename.txt - patch panel filenames are strictly 64 hex digits");

        $this->artisan('utils:cleanup-patch-panel-files' )
            ->doesntExpectOutput();

        Storage::assertExists('files/ppp/a/a/some-filename.txt');

    }

    public function testFileDoesntMatchSharding1(): void
    {
        Storage::fake();
        Storage::put('files/ppp/a/2/4242424242424242424242424242424242424242424242424242424242424242', 'hello world');

        $this->artisan('utils:cleanup-patch-panel-files', ['-v' => true] )
            ->expectsOutput("SKIP files/ppp/a/2/4242424242424242424242424242424242424242424242424242424242424242 - patch panel files directory sharding doesn't match filename");

        $this->artisan('utils:cleanup-patch-panel-files' )
            ->doesntExpectOutput();

        Storage::assertExists('files/ppp/a/2/4242424242424242424242424242424242424242424242424242424242424242');
    }

    public function testFileDoesntMatchSharding2(): void
    {
        Storage::fake();
        Storage::put('files/ppp/4/a/4242424242424242424242424242424242424242424242424242424242424242', 'hello world');

        $this->artisan('utils:cleanup-patch-panel-files', ['-v' => true] )
            ->expectsOutput("SKIP files/ppp/4/a/4242424242424242424242424242424242424242424242424242424242424242 - patch panel files directory sharding doesn't match filename");

        $this->artisan('utils:cleanup-patch-panel-files' )
            ->doesntExpectOutput();

        Storage::assertExists('files/ppp/4/a/4242424242424242424242424242424242424242424242424242424242424242');
    }


    public function testIsPatchPanelPortFile(): void
    {
        $ppFile = $this->createPatchPanelPortFile();

        Storage::fake();
        Storage::put("files/" . $ppFile->path(), 'hello world');

        $this->artisan('utils:cleanup-patch-panel-files', [ '-v' => true ] )
            ->expectsOutput("SKIP files/{$ppFile->path()} - is a patch panel port file");

        $this->artisan('utils:cleanup-patch-panel-files')
            ->doesntExpectOutput();

        Storage::assertExists("files/" . $ppFile->path());
    }

    public function testIsPatchPanelPortHistoryFile(): void
    {
        $ppHistoryFile = $this->createPatchPanelPortHistoryFile();

        Storage::fake();
        Storage::put("files/" . $ppHistoryFile->path(), 'hello world');

        $this->artisan('utils:cleanup-patch-panel-files', [ '-v' => true ] )
            ->expectsOutput("SKIP files/{$ppHistoryFile->path()} - is a patch panel port history file");

        $this->artisan('utils:cleanup-patch-panel-files')
            ->doesntExpectOutput();

        Storage::assertExists("files/" . $ppHistoryFile->path());
    }

    public function testDeletesFile(): void
    {
        Storage::fake();
        Storage::put('files/ppp/4/2/4242424242424242424242424242424242424242424242424242424242424242', 'hello world');

        $this->artisan('utils:cleanup-patch-panel-files', [] )
            ->expectsOutput("Removed files/ppp/4/2/4242424242424242424242424242424242424242424242424242424242424242");

        Storage::assertMissing('files/ppp/4/2/4242424242424242424242424242424242424242424242424242424242424242');
    }

    public function testDryRunDoesntDeleteFile(): void
    {
        Storage::fake();
        Storage::put('files/ppp/4/2/4242424242424242424242424242424242424242424242424242424242424242', 'hello world');

        $this->artisan('utils:cleanup-patch-panel-files', [
            '--dry-run' => true
        ] )
            ->expectsOutput("remove orphaned file: files/ppp/4/2/4242424242424242424242424242424242424242424242424242424242424242");

        Storage::assertExists('files/ppp/4/2/4242424242424242424242424242424242424242424242424242424242424242');
    }

    public function testMock_FileDeleteFailsViaReturn(): void
    {
        // Pure mocks here, we need to spoof delete failure via return value (our config)

        Storage::shouldReceive('directoryExists')
            ->with('files/ppp')
            ->andReturn(true);

        Storage::shouldReceive('directories')
            ->with('files/ppp')
            ->andReturn(['files/ppp/4']);

        Storage::shouldReceive('directories')
            ->with('files/ppp/4')
            ->andReturn(['files/ppp/4/2']);

        Storage::shouldReceive('files')
            ->with('files/ppp/4/2')
            ->andReturn(['files/ppp/4/2/4242424242424242424242424242424242424242424242424242424242424242']);

        Storage::shouldReceive('delete')
            ->with('files/ppp/4/2/4242424242424242424242424242424242424242424242424242424242424242')
            ->andReturn(false);

        $this->artisan('utils:cleanup-patch-panel-files', [] )
            ->expectsOutput("Failed to remove files/ppp/4/2/4242424242424242424242424242424242424242424242424242424242424242");
    }

    public function testMock_FileDeleteFailsViaException(): void
    {
        // Pure mocks here, we need to spoof delete failure via exceptions

        Storage::shouldReceive('directoryExists')
            ->with('files/ppp')
            ->andReturn(true);

        Storage::shouldReceive('directories')
            ->with('files/ppp')
            ->andReturn(['files/ppp/4']);

        Storage::shouldReceive('directories')
            ->with('files/ppp/4')
            ->andReturn(['files/ppp/4/2']);

        Storage::shouldReceive('files')
            ->with('files/ppp/4/2')
            ->andReturn(['files/ppp/4/2/4242424242424242424242424242424242424242424242424242424242424242']);

        Storage::shouldReceive('delete')
            ->with('files/ppp/4/2/4242424242424242424242424242424242424242424242424242424242424242')
            ->andThrow(UnableToDeleteFile::atLocation('files/ppp/4/2/4242424242424242424242424242424242424242424242424242424242424242'));

        $this->artisan('utils:cleanup-patch-panel-files', [] )
            ->expectsOutput("Failed to remove files/ppp/4/2/4242424242424242424242424242424242424242424242424242424242424242");
    }

    public function testCleanup(): void
    {
        $ppFile1 = $this->createPatchPanelPortFile();
        $ppFile2 = $this->createPatchPanelPortFile();

        $ppHistoryFile1 = $this->createPatchPanelPortHistoryFile();
        $ppHistoryFile2 = $this->createPatchPanelPortHistoryFile();
        $ppHistoryFile3 = $this->createPatchPanelPortHistoryFile();

        Storage::fake();
        Storage::put('files/ppp/3/0/30e14955ebf1352266dc2ff8067e68104607e750abb9d3b36582b8af909fcb58', 'File 1');
        Storage::put('files/ppp/4/2/4242424242424242424242424242424242424242424242424242424242424242', 'File 2');
        Storage::put('files/ppp/5/6/5647f05ec18958947d32874eeb788fa396a05d0bab7c1b71f112ceb7e9b31eee', 'File 3');
        Storage::put('files/ppp/3/0/30-not-a-hash', 'File 4');
        Storage::put('files/ppp/directory/0/30-not-a-hash', 'File 5');
        Storage::put('files/ppp/3/directory/30-not-a-hash', 'File 6');
        Storage::put('files/ppp/a/b/4242424242424242424242424242424242424242424242424242424242424242', 'File 7');
        Storage::put("files/" . $ppFile1->path(), 'File 8');
        Storage::put("files/" . $ppFile2->path(), 'File 9');
        Storage::put("files/" . $ppHistoryFile1->path(), 'File 9');
        Storage::put("files/" . $ppHistoryFile2->path(), 'File 10');
        Storage::put("files/" . $ppHistoryFile3->path(), 'File 11');

        // Laravel asserts 'expectsOutput' data in the order it is called, and the order of output seems to vary from local
        // to Github Actions, so don't assert any particular order. Just assert what's deleted / still there.
        $this->artisan('utils:cleanup-patch-panel-files' )
            ->expectsOutput();

        Storage::assertMissing('files/ppp/3/0/30e14955ebf1352266dc2ff8067e68104607e750abb9d3b36582b8af909fcb58');
        Storage::assertMissing('files/ppp/4/2/4242424242424242424242424242424242424242424242424242424242424242');
        Storage::assertMissing('files/ppp/5/6/5647f05ec18958947d32874eeb788fa396a05d0bab7c1b71f112ceb7e9b31eee');
        Storage::assertExists('files/ppp/3/0/30-not-a-hash');
        Storage::assertExists('files/ppp/directory/0/30-not-a-hash');
        Storage::assertExists('files/ppp/3/directory/30-not-a-hash');
        Storage::assertExists('files/ppp/a/b/4242424242424242424242424242424242424242424242424242424242424242');
        Storage::assertExists("files/" . $ppFile1->path());
        Storage::assertExists("files/" . $ppFile2->path());
        Storage::assertExists("files/" . $ppHistoryFile1->path());
        Storage::assertExists("files/" . $ppHistoryFile2->path());
        Storage::assertExists("files/" . $ppHistoryFile3->path());
    }

    private function createPatchPanelPortFile(?string $location = null): PatchPanelPortFile
    {
        $pp = new PatchPanel();
        $pp->save();

        $ppPort = new PatchPanelPort();
        $ppPort->patch_panel_id = $pp->id;
        $ppPort->save();

        $ppFile = new PatchPanelPortFile();
        $ppFile->patch_panel_port_id = $ppPort->id;
        $ppFile->storage_location = $location ?? bin2hex(random_bytes(32));
        $ppFile->save();
        return $ppFile;
    }

    private function createPatchPanelPortHistoryFile(?string $location = null): PatchPanelPortHistoryFile
    {
        $pp = new PatchPanel();
        $pp->save();

        $ppPort = new PatchPanelPort();
        $ppPort->patch_panel_id = $pp->id;
        $ppPort->save();

        $ppHistory = new PatchPanelPortHistory();
        $ppHistory->patch_panel_port_id = $ppPort->id;
        $ppHistory->save();

        $ppHistoryFile = new PatchPanelPortHistoryFile();
        $ppHistoryFile->patch_panel_port_history_id = $ppHistory->id;
        $ppHistoryFile->storage_location = $location ?? bin2hex(random_bytes(32));
        $ppHistoryFile->save();
        return $ppHistoryFile;
    }
}