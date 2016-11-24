<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Recipe;
use Carbon\Carbon;
use App\Utils\Gzip;

class BackupToGoogleDriveCommmand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recipes:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup all recipes in the database to Configured Storage';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $this->info("Backing of Recipes to Cloud Storage...");
            
            $exportFileName = 'cooglerecipe-backup-' . Carbon::now()->format('m-d-Y') . '-' . uniqid() . '.xml.gz';
            $tempFile = tempnam(storage_path(), uniqid() . '_');
    
            $this->info("Exporting Recipes to temp file $tempFile");
            
            Recipe::exportAll($tempFile);
            
            $this->info("Compressing Backup");
            
            $compressedFile = Gzip::gzCompressFile($tempFile);
            
            $this->info("Uploading Backup to $exportFileName");
            
            $stream = fopen($compressedFile, 'r+');
            
            Storage::disk(config('filesystems.backup'))
                    ->getDriver()
                    ->writeStream($exportFileName, $stream);
            
            fclose($stream);
            unlink($tempFile);
            unlink($compressedFile);
            
            $this->info("Export Complete!");
        } catch(\Exception $e) {
            $this->error("Failed to complete backup: {$e->getMessage()}");
            
            if(file_exists($tempFile)) {
                unlink($tempFile);
            }
            
            if(file_exists($compressedFile)) {
                unlink($compressedFile);
            }
        }
    }
}
