<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Exception;
use Storage;
use Log;
use DB;

class SyncStoresFromMDM extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:stores:psv:server {mode?} {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize Stores From MDM';

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
        // set memory limit, -1 berarti tidak ada batasan untuk memory_limit
        ini_set('memory_limit', '-1');

        try {
            DB::beginTransaction();

            // Untuk menghitung berapa lama proses - start time
            $executionStartTime = microtime(true);

            // get mode dari argument
            $modeArgument = $this->argument('mode');
            $dateArgument = $this->argument('date');

            $dateNow = Carbon::now();
            $todayWithTime = $dateNow->format('Y-m-d H:i:s');
            $todayDate = null;

            /**
             * jika command dijalankan dengan tanggal khusus
             * maka variable today diganti dengan tanggal yang menjadi parameter
             */
            if (isset($dateArgument) && $dateArgument != "") {
                $todayDate = $dateArgument;
            } else {
                $todayDate = $dateNow->format('Ymd');
            }

            /**
             * set tanggal untuk menghapus file psv hari-hari sebelumnya dari server
             */
            $dayBeforeCleanUp = env('MINUS_DATE_DELETE_FILE_PSV');
            $dateForCleanUp = Carbon::now()->subDays($dayBeforeCleanUp)->format('Ymd');

            /**
             * set mode sync berdasarkan mode saat menjalankan command
             * mengambil file PSV berdasarkan mode
             */
            $fullPSVFileName = "";
            $infoStartSync = "";
            if ($modeArgument == "daily") {
                $infoStartSync = "[KERNEL-SERVICE] Start $this->description (Mode Daily at " . $todayWithTime . ")";

                $fullPSVFileName = "stores-daily-" . $todayDate . ".psv";
                $fullLocalPSVFileNameForCleanUp = "stores-daily-" . $dateForCleanUp . ".psv";
            } elseif ($modeArgument == "initial") {
                $infoStartSync = "[KERNEL-SERVICE] Start $this->description (Mode Initial at " . $todayWithTime . ")";

                $fullPSVFileName = "stores-initial-" . $todayDate . ".psv";
                $fullLocalPSVFileNameForCleanUp = "stores-initial-" . $dateForCleanUp . ".psv";
            }
            else{
                throw new Exception("Tidak ditemukan mode " . $modeArgument);
            }

            $this->info($infoStartSync);
            Log::info($infoStartSync);

            $this->info('[KERNEL-SERVICE] Truncate table temporary');
            Log::info('[KERNEL-SERVICE] Truncate table temporary');

            /* truncate pada table temporary */
            DB::statement('truncate table store_mdm restart identity');

            /* commit truncate pada table temporary */
            DB::commit();

            /* begin transaction kembali */
            DB::beginTransaction();

            /**
             * ambil config server from env
             */
            $psvServerIP = env('SERVER_PSV_IP');
            $psvServerPath = env('SERVER_PSV_PATH') . $fullPSVFileName;

            /* cek exist file psv nya dari server psv */
            $fullLocalPathPSVFileName = "";
            $isExistsFilePsv = Storage::disk('psv_server')->exists($psvServerPath);

            if ($isExistsFilePsv) {
                $this->info("[KERNEL-SERVICE] Take file data psv from MDM " . $psvServerIP);
                Log::info("[KERNEL-SERVICE] Take file data psv from MDM " . $psvServerIP);

                /* simpan psv yang sudah didownload dari MDM ke folder storage/app */
                Storage::disk('file_psv_mdm')->put("stores/" . $fullPSVFileName, Storage::disk('psv_server')->get($psvServerPath));

                $this->info('[KERNEL-SERVICE] Finish downloading file psv and save file psv to storage/app/mdm');
                Log::info('[KERNEL-SERVICE] Finish downloading file psv and save file psv to storage/app/mdm');

                /* proses insert data dari file psv ke table temporary */
                $fullLocalPathPSVFileName = Storage::disk('file_psv_mdm')->path("stores/" . $fullPSVFileName);
            } else {
                $this->info("[KERNEL-SERVICE] Proses TIDAK DAPAT DIMULAI karena FILE TIDAK DITEMUKAN untuk alamat $psvServerPath");
                Log::info("[KERNEL-SERVICE] Proses TIDAK DAPAT DIMULAI karena FILE TIDAK DITEMUKAN untuk alamat $psvServerPath");

                /* terjadi error dan akan kirim notifikasi ke telegram jika file psv nya ga ada */
                throw new Exception("File " . $psvServerPath . " tidak ditemukan!");
            }

            /* mengecek isi file kosong atau ga dari file MDM */
            $check_file_is_empty = true;
            $handle = fopen($fullLocalPathPSVFileName, "r");

            while (!feof($handle)) {
                $line = fgets($handle);
                if ($line == '' || $line =="\n") {
                    $check_file_is_empty = true;
                } else {
                    $check_file_is_empty = false;
                }
                break;
            }

            fclose($handle);

            /* jika file psv tidak kosong maka di proses ke table utama dan temporary */
            if(!$check_file_is_empty){
                /**
                 * copy data file psv ke table temporary
                 * tanda 2>&1 untuk mengembalikan error jadi bukan string kosong ketika error
                 */
                $this->info('[KERNEL-SERVICE] Proccessing copy data from MDM to table temporary');
                Log::info('[KERNEL-SERVICE] Proccessing copy data from MDM to table temporary');

                $command = "PGPASSWORD=".ENV('DB_PASSWORD')." psql -h ".ENV('DB_HOST')." -U ".ENV('DB_USERNAME')." -p ".ENV('DB_PORT')." -d ".ENV('DB_DATABASE')." -c \"\COPY store_mdm FROM '".$fullLocalPathPSVFileName."' DELIMITER '|'\" 2>&1";
                $this->info($command);
                Log::info($command);

                $this->info("[KERNEL-SERVICE] Execute command: $command");
                Log::info("[KERNEL-SERVICE] Execute command: $command");
                $output = shell_exec($command);

                $this->info("[KERNEL-SERVICE] Result output: " . strpos($output, 'ERROR'));
                Log::info("[KERNEL-SERVICE] Result output: " . strpos($output, 'ERROR'));

                if(strpos($output, 'ERROR') !== false) {
                    $this->info("[KERNEL-SERVICE] Terjadi Error pada saat COPY PSV ke Database");
                    Log::info("[KERNEL-SERVICE] Terjadi Error pada saat COPY PSV ke Database");

                    throw new Exception($output);
                }
                else{
                    $this->info("[KERNEL-SERVICE] Successfully Process COPY file $fullPSVFileName ke tabel store_mdm");
                    Log::info("[KERNEL-SERVICE] Successfully Process COPY file $fullPSVFileName ke tabel store_mdm");

                    /* insert atau update ke table utama dari table temporary */
                    $this->info('[KERNEL-SERVICE] Process insert or update data from temporary table to main table');
                    Log::info('[KERNEL-SERVICE] Process insert or update data from temporary table to main table');

                    DB::statement(
                        "with get_store_mdm as (
                            select * from store_mdm
                        )
                        INSERT INTO stores (store_code, initials_code, store_desc, start_date, end_date, store_status, created_at, updated_at)
                        SELECT store_code, initials_code, store_desc, start_date, end_date, store_status, current_timestamp, current_timestamp 
                        FROM get_store_mdm
                        ON CONFLICT (store_code)
                        DO UPDATE SET initials_code = excluded.initials_code, store_desc = excluded.store_desc, start_date = excluded.start_date, end_date = excluded.end_date, store_status = excluded.store_status, updated_at = current_timestamp"
                    );
                }
            }
            else{
                /* isi file psv kosong */
                $this->warn("[KERNEL-SERVICE] Tidak ada data delta pada hari ini.");
                Log::warning("[KERNEL-SERVICE] Tidak ada data delta pada hari ini.");
            }

            /* hapus file psv pada storage untuk hari-hari sebelumnya */
            $this->info("[KERNEL-SERVICE] Clean up PSV on Generator Server - $dayBeforeCleanUp days ago : $fullLocalPSVFileNameForCleanUp");
            Log::info("[KERNEL-SERVICE] Clean up PSV on Generator Server - $dayBeforeCleanUp days ago : $fullLocalPSVFileNameForCleanUp");

            Storage::delete($fullLocalPSVFileNameForCleanUp);

            /* commit transaction */
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            /**
             * kirim pesan ke telegram
             * memotong pesan error dari exception sebanyak maksimal 300 karakter
             * urlencode = untuk memperbaiki karakter spesial yang dapat mengganggu format URL
             */
            $message = substr(urlencode($e), 0, 300);

            $this->error("[KERNEL-SERVICE] $this->description Failed. Please take attention : $message");
            Log::error("[KERNEL-SERVICE] $this->description Failed. Please take attention : $message");

            Log::error($e);

            /* combine URL Telegram yang sudah ditambah parameter */
            $telegramURL = env('TELEGRAM_HOST')."status=FAILED&message=$message&psv=$fullPSVFileName&servertime=".urlencode($todayWithTime);

            /* Panggil URL telegrambot untuk mengirimkan notifikasi ketika error exception terjadi */
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_URL, $telegramURL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
        }

        /**
         * Menangkap waktu end/waktu selesai script diproses
         * The result will be in seconds and milliseconds.
         * (selisihnya menghasilkan panjang durasi proses berjalan)
         */
        $executionEndTime = microtime(true);
        $seconds = $executionEndTime - $executionStartTime;

        $this->info("[KERNEL-SERVICE] Finish $this->description");
        Log::info("[KERNEL-SERVICE] Finish $this->description");

        $this->info("[KERNEL-SERVICE] Execution time finish $this->description: " . $seconds);
        Log::info("[KERNEL-SERVICE] Execution time finish $this->description: " . $seconds);

        $this->info(PHP_EOL);
        Log::info(PHP_EOL);
    }
}
