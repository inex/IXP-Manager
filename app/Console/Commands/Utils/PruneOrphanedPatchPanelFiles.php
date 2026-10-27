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

/**
 * Artisan command to remove orphaned patch panel files (due to failed expungement)
 */
class PruneOrphanedPatchPanelFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'utils:prune-orphaned-patch-panel-files
                            { --dry-run : Run without deleting files }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command attempts to remove orphaned patch panel files (due to failed expungement). Left over empty directories will also be removed. A dry run mode is available which will identify and report orphaned files without deleting anything. Use the verbose flag to to see decision making for each file.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $isVerbose = $this->isVerbosityVerbose();

        $this->removeOrphanFilesAndDirectories( Storage::path( 'files/ppp'), $isDryRun, $isVerbose );

        return 0;
    }

    /**
     * Traverse filesystem tree from the specified base directory. Iterate over first level and second level of sharding
     * and identify orphaned patch panel files (skip files / directories not following sharding convention). After scanning
     */
    private function removeOrphanFilesAndDirectories(string $baseDir, bool $isDryRun, bool $isVerbose): void
    {
        // Ensure the base directory exists
        if ( !is_dir( $baseDir ) ) {
            $this->info( "Directory does not yet exist - $baseDir", 'v' );
            return;
        }

        // Iterate over the first level of sharding in base dir
        foreach ( new FilesystemIterator( $baseDir, FilesystemIterator::SKIP_DOTS ) as $shard1Dir ) {
            if ( !$this->isPPPShardDirectory( $shard1Dir ) ) {
                continue;
            }

            // Iterate over second level of sharding
            foreach ( new FilesystemIterator( $shard1Dir->getPathname(), FilesystemIterator::SKIP_DOTS ) as $shard2Dir ) {
                if ( !$this->isPPPShardDirectory( $shard2Dir ) ) {
                    continue;
                }

                // Extract the shard names from the file path, our files should start with the same characters
                $shards = [ basename( $shard1Dir->getPathName() ), basename( $shard2Dir->getPathName() ) ];

                // Inspect files in the leaf directory to find candidates for removal
                foreach ( new FilesystemIterator( $shard2Dir->getPathname(), FilesystemIterator::SKIP_DOTS ) as $leafFile ) {
                    if ( !$this->shouldRemoveFile( $leafFile, $shards ) ) {
                        continue;
                    }

                    $this->removeFile( $leafFile->getPathname(), $isDryRun );
                }

                // Rescan leaf directory, if it's empty, it can be removed
                $leafCheckIterator = new FilesystemIterator($shard2Dir->getPathname(), FilesystemIterator::SKIP_DOTS);
                if (!$leafCheckIterator->valid()) {
                    $this->removeDirectory( $shard2Dir->getPathname(), $isDryRun );
                }
            }

            // After checking all leaf directories, check if the parent shard directory is now empty
            $shard1CheckIterator = new FilesystemIterator( $shard1Dir->getPathname(), FilesystemIterator::SKIP_DOTS );
            if (!$shard1CheckIterator->valid()) {
                // Parent shard directory is empty, safe to delete
                $this->removeDirectory( $shard1Dir->getPathname(), $isDryRun );
            }
        }
    }


    /**
     * Check if the directory is a valid shard directory, and something we want to inspect further. Returns
     * true if it should be traversed into.
     */
    private function isPPPShardDirectory( \SplFileInfo $shardDir ): bool
    {
        if ( !$shardDir->isDir() ) {
            $this->warn( "SKIP $shardDir - not a directory", 'v' );
            return false;
        }

        // Shard directory name must be a single hex character
        if ( 1 !== preg_match( '/^[a-f0-9]$/', basename( $shardDir->getPathname() ) ) ) {
            $this->warn( "SKIP $shardDir - doesn't match shard format", 'v' );
            return false;
        }

        return true;
    }

    /**
     * Check if the provided file should be removed. Returns true if it should be removed.
     */
    private function shouldRemoveFile( \SplFileInfo $file, array $shards ): bool
    {
        $filePath = $file->getPathname();
        $filename = basename( $filePath );

        if ( !$file->isFile() ) {
            $this->warn( "SKIP $filePath - not a file", 'v' );
            return false;
        }

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

    private function removeDirectory( string $directoryPath, bool $isDryRun ): void
    {
        // Leaf directory is empty, safe to delete
        if ( $isDryRun ) {
            $this->info( "remove empty directory: $directoryPath" );
        } else {
            $result = rmdir( $directoryPath );
            if (false === $result) {
                $this->error( "Failed to remove $directoryPath" );
            } else {
                $this->info( "Removed $directoryPath" );
            }
        }
    }

    private function removeFile( string $filePath, bool $isDryRun ): void
    {
        if ( $isDryRun ) {
            $this->info( "remove orphaned file: $filePath" );
        } else {
            $result = unlink( $filePath);
            if (false === $result) {
                $this->error( "Failed to remove $filePath" );
            } else {
                $this->info( "Removed $filePath" );
            }
        }
    }
}
