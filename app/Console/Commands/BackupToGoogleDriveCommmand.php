<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

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
    protected $description = 'Backup all recipes in the database to Designated Storage';

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
        $storage = Storage::disk(config('filesystems.cloud'));
        
        $this->info("Backing of Recipes to Cloud Storage");
    }
}
