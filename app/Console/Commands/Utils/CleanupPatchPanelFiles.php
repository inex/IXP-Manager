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

namespace IXP\Console\Commands\Utils;

use FilesystemIterator;
use Illuminate\Support\Facades\Storage;
use IXP\Console\Commands\Command;
use IXP\Models\PatchPanelPortFile;
use IXP\Models\PatchPanelPortHistoryFile;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToDeleteFile;

/**
 * Artisan command to remove orphaned patch panel files (due to failed expungement)
 */
class CleanupPatchPanelFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'utils:cleanup-patch-panel-files
                            { --dry-run : Run without deleting files }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command attempts to cleanup patch panel files (where expunging has failed). Empty directories will also be removed. A dry run mode is available which will identify and report orphaned files without deleting anything. Use the verbose flag to to see decision making for each file.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $baseDir   = "files/ppp";

        // Ensure the base directory exists
        if ( !Storage::directoryExists( $baseDir ) ) {
            $this->info( "Directory does not yet exist - $baseDir" );
            return 0;
        }

        $removedCount = 0;

        // Iterate over the first level of sharding in base dir
        foreach ( Storage::directories( $baseDir ) as $shard1Dir ) {
            if ( !$this->isPPPShardDirectory( $shard1Dir ) ) {
                continue;
            }

            // Iterate over second level of sharding
            foreach ( Storage::directories( $shard1Dir ) as $shard2Dir ) {
                if ( !$this->isPPPShardDirectory( $shard2Dir ) ) {
                    continue;
                }

                // Extract the shard names from the file path, our files should start with the same characters
                $shards = [ basename( $shard1Dir ), basename( $shard2Dir ) ];

                // Inspect files in the leaf directory to find candidates for removal
                foreach ( Storage::files( $shard2Dir ) as $pppFile ) {
                    if ( !$this->shouldRemoveFile( $pppFile, $shards ) ) {
                        continue;
                    }

                    $this->removeFile( $pppFile, $isDryRun );
                }
            }
        }

        return 0;
    }

    /**
     * Check if the directory is a valid shard directory, and something we want to inspect further. Returns
     * true if it should be traversed into.
     */
    private function isPPPShardDirectory( string $shardDir ): bool
    {

        // Shard directory name must be a single hex character
        if ( 1 !== preg_match( '/^[a-f0-9]$/', basename( $shardDir ) ) ) {
            $this->warn( "SKIP $shardDir - doesn't match shard format", 'v' );
            return false;
        }

        return true;
    }

    /**
     * Check if the provided file should be removed. Returns true if it should be removed.
     */
    private function shouldRemoveFile( string $filePath, array $shards ): bool
    {
        $filename = basename( $filePath );

        // Files in the sharded directory format are 64 hex characters.
        if ( 1 !== preg_match( '/^[a-f0-9]{64}$/', $filename ) ) {
            $this->warn( "SKIP $filePath - patch panel filenames are strictly 64 hex digits", 'v' );
            return false;
        }

        // Files are sharded per their first two hex characters. Check they match the outer directory structure.
        if ( $shards[0] !== $filename[0] || $shards[1] !== $filename[1] ) {
            $this->warn( "SKIP $filePath - patch panel files directory sharding doesn't match filename", 'v' );
            return false;
        }

        // Check if the file is referenced by any patch panel port file or patch panel port history file.
        if ( PatchPanelPortFile::whereStorageLocation( $filename )->exists() ) {
            $this->info( "SKIP $filePath - is a patch panel port file", 'v' );
            return false;
        } else if ( PatchPanelPortHistoryFile::whereStorageLocation( $filename )->exists() ) {
            $this->info( "SKIP $filePath - is a patch panel port history file", 'v' );
            return false;
        }

        return true;
    }

    private function removeFile( string $filePath, bool $isDryRun ): void
    {
        if ( $isDryRun ) {
            $this->info( "remove orphaned file: $filePath" );
        } else {
            try {
                $result = Storage::delete( $filePath );
            } catch (FilesystemException | UnableToDeleteFile $e) {
                $result = false;
            } finally {
                if (false === $result) {
                    $this->error( "Failed to remove $filePath" );
                } else {
                    $this->info( "Removed $filePath" );
                }
            }
        }
    }
}
