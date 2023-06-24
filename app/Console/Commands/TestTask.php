<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Mail\Mailable;
use App\Mail\PruebaCron;
use Illuminate\Support\Facades\Mail;
use DB;

class TestTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Primer command';

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
     * @return int
     */
    public function handle()
    {
        $visits = \DB::table('users')->where('pass', '123gaston')->get();
        Mail::to('gnfelippa@gmail.com')->send(new PruebaCron($visits));
    }
}
