<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Exception;
use Storage;
use Log;
use DB;

class SyncSalesFromTDM extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:sales:psv:server {end} {todaydate?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize Sales From TDM';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /* set memory limit, -1 berarti tidak ada batasan untuk memory_limit */
        ini_set('memory_limit', '-1');

        try {
            DB::beginTransaction();

            /* Untuk menghitung berapa lama proses - start time */
            $executionStartTime = microtime(true);

            /* get mode dari argument */
            $end = $this->argument('end');
            $todaydate = $this->argument('todaydate');

            $dateNow = Carbon::now();
            $todayWithTime = $dateNow->format('Y-m-d H:i:s');
            $today = null;

            /**
             * jika command dijalankan dengan tanggal khusus
             * maka variable today diganti dengan tanggal yang menjadi parameter
             */
            if (isset($todaydate) && $todaydate != "") {
                $today = $todaydate;
            } else {
                $today = $dateNow->format('Ymd');
            }

            /**
             * set tanggal untuk menghapus file psv hari-hari sebelumnya dari server
             */
            $dayBeforeCleanUp = env('MINUS_DATE_DELETE_FILE_PSV');
            $dateForCleanUp = Carbon::now()->subDays($dayBeforeCleanUp)->format('Ymd');

            $fullPSVFileName = "sales-1-daily-" . $today . "-" . $end . ".psv";
            $fullLocalPSVFileNameForCleanUp = "sales-1-daily-" . $dateForCleanUp;

            /* start synchronize*/
            $this->info("[KERNEL-SERVICE] Start $this->description");
            Log::info("[KERNEL-SERVICE] Start $this->description");

            $this->info('[KERNEL-SERVICE] Truncate table temporary');
            Log::info('[KERNEL-SERVICE] Truncate table temporary');

            /* truncate pada table temporary */
            DB::statement('truncate table sales_temp restart identity');

            /* commit truncate pada table temporary */
            DB::commit();

            /* begin transaction kembali */
            DB::beginTransaction();

            /* ambil config server from env */
            $psvServerIP = env('SERVER_PSV_IP');
            $psvServerPath = env('SERVER_PSV_PATH') . $fullPSVFileName;

            /* cek exist file psv nya dari server psv */
            $fullLocalPathPSVFileName = "";
            $isExistsFilePsv = Storage::disk('psv_server')->exists($psvServerPath);

            if ($isExistsFilePsv) {
                $this->info("[KERNEL-SERVICE] Take file data psv from TDM " . $psvServerIP);
                Log::info("[KERNEL-SERVICE] Take file data psv from TDM " . $psvServerIP);

                /* simpan psv yang sudah didownload dari TDM ke folder storage/app */
                Storage::disk('file_psv_tdm')->put("sales/" . $fullPSVFileName, Storage::disk('psv_server')->get($psvServerPath));

                $this->info('[KERNEL-SERVICE] Finish downloading file psv and save file psv to storage/app/tdm');
                Log::info('[KERNEL-SERVICE] Finish downloading file psv and save file psv to storage/app/tdm');

                /* proses insert data dari file psv ke table temporary */
                $fullLocalPathPSVFileName = Storage::disk('file_psv_tdm')->path("sales/" . $fullPSVFileName);
            } else {
                $this->info("[KERNEL-SERVICE] Proses TIDAK DAPAT DIMULAI karena FILE TIDAK DITEMUKAN pada path: $psvServerPath");
                Log::info("[KERNEL-SERVICE] Proses TIDAK DAPAT DIMULAI karena FILE TIDAK DITEMUKAN pada path: $psvServerPath");

                /* terjadi error dan akan kirim notifikasi ke telegram jika file psv nya ga ada */
                throw new Exception("File " . $psvServerPath . " tidak ditemukan!");
            }

            /* mengecek isi file kosong atau ga dari file TDM */
            $check_file_is_empty = true;
            $handle = fopen($fullLocalPathPSVFileName, "r");

            while (!feof($handle)) {
                $line = fgets($handle);
                if ($line == '' || $line == "\n") {
                    $check_file_is_empty = true;
                } else {
                    $check_file_is_empty = false;
                }
                break;
            }

            fclose($handle);

            if (!$check_file_is_empty) {
                /**
                 * copy data file psv ke table temporary
                 * tanda 2>&1 untuk mengembalikan error jadi bukan string kosong ketika error
                 */
                $this->info('[KERNEL-SERVICE] Processing copy data from TDM to table temporary');
                Log::info('[KERNEL-SERVICE] Processing copy data from TDM to table temporary');

                $command = "PGPASSWORD=" . env('DB_PASSWORD') . " psql -h " . env('DB_HOST') . " -U " . env('DB_USERNAME') . " -p " . env('DB_PORT') . " -d " . env('DB_DATABASE') . " -c \"\COPY sales_temp FROM '" . $fullLocalPathPSVFileName . "' CSV DELIMITER '|' ESCAPE '\' QUOTE '{' \" 2>&1";

                $this->info("[KERNEL-SERVICE] Execute command: $command");
                Log::info("[KERNEL-SERVICE] Execute command: $command");
                $output = shell_exec($command);

                $this->info("[KERNEL-SERVICE] Result output: " . strpos($output, 'ERROR'));
                Log::info("[KERNEL-SERVICE] Result output: " . strpos($output, 'ERROR'));

                if (strpos($output, 'ERROR') !== false) {
                    $this->info("[KERNEL-SERVICE] Terjadi Error pada saat COPY DATA PSV ke Database");
                    Log::info("[KERNEL-SERVICE] Terjadi Error pada saat COPY DATA PSV ke Database");

                    throw new Exception($output);
                } else {
                    $this->info("[KERNEL-SERVICE] Successfully process COPY data file psv $fullPSVFileName ke table temporary");
                    Log::info("[KERNEL-SERVICE] Successfully process COPY data file psv $fullPSVFileName ke table temporary");

                    /* insert atau update ke table utama dari table temporary */
                    $this->info('[KERNEL-SERVICE] Process insert or update data from temporary table to main table');
                    Log::info('[KERNEL-SERVICE] Process insert or update data from temporary table to main table');

                    DB::statement("
                        WITH get_temp as (
                            SELECT *
                            FROM sales_temp
                        )
                        INSERT INTO sales (id, plu, tillcode, article_code, quantity, subclass, store_code, transaction_date, created_at, updated_at) 
                        SELECT id, plu, barcode, article_code, quantity, subclass, store_code, transaction_date, current_timestamp, current_timestamp
                        FROM get_temp
                        ON CONFLICT (id)
                        DO UPDATE SET 
                        plu = excluded.plu,
                        tillcode = excluded.tillcode,
                        article_code = excluded.article_code,
                        quantity = excluded.quantity,
                        subclass = excluded.subclass,
                        store_code = excluded.store_code,
                        transaction_date = excluded.transaction_date,
                        updated_at = current_timestamp
                    ");
                }
            } else {
                /* isi file psv kosong */
                $this->warn("[KERNEL-SERVICE] Tidak ada data delta pada hari ini.");
                Log::warning("[KERNEL-SERVICE] Tidak ada data delta pada hari ini.");
            }

            DB::commit();

            /* hapus file psv pada storage untuk hari-hari sebelumnya */
            $this->info("[KERNEL-SERVICE] Clean up PSV on Generator Server - $dayBeforeCleanUp days ago : $fullLocalPSVFileNameForCleanUp");
            Log::info("[KERNEL-SERVICE] Clean up PSV on Generator Server - $dayBeforeCleanUp days ago : $fullLocalPSVFileNameForCleanUp");

            $files = Storage::disk('file_psv_tdm')->files('sales');
            foreach ($files as $file) {
                if (strpos($file, "sales-1-daily-" . $dateForCleanUp) !== false) {
                    $this->info("[KERNEL-SERVICE] Delete file: $file");
                    Log::info("[KERNEL-SERVICE] Delete file: $file");

                    Storage::delete($file);
                } else {
                    $this->info("[KERNEL-SERVICE] No files deleted: $file");
                    Log::info("[KERNEL-SERVICE] No files deleted: $file");
                }
            }
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
            $telegramURL = env('TELEGRAM_HOST') . "status=FAILED&message=$message&psv=$fullPSVFileName&servertime=" . urlencode($todayWithTime);

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
