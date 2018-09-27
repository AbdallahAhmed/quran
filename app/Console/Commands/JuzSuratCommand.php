<?php

namespace App\Console\Commands;

use App\Models\Ayat;
use App\Models\Surat;
use Illuminate\Console\Command;

class JuzSuratCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'juz:section';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Juz join';

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
        $juzs = [];
        for ($juz_number = 1; $juz_number <= 30; $juz_number++) {
            $juzs[$juz_number] = $juz = [];
            $juz['name_en'] = juz_name($juz_number, 'en');
            $juz['name_ar'] = juz_name($juz_number, 'ar');

            $grouped = Ayat::where('juz_id', $juz_number)->orderBy('number', 'ASC')->get(['surat_id', 'number', 'page_id', 'juz_id'])->groupBy('surat_id');

            $swar = [];
            foreach ($grouped as $item => $ayat) {
                $swar[] = Surat::find($item);
            }

            $juz['swar'] = $swar;
            $juzs[] = $juz;
        }

        file_put_contents(public_path('api/juz_surat.json'), json_encode($juzs));

        //
    }
}
