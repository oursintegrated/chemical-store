<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Datatables;
use Carbon\Carbon;
use App\User;
use App\Store;
use DB;
use Validator;
use Hash;
use Auth;
use Excel;

class ImportCSVController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /* RBAC */
        if (! User::authorize('store.index')) {
            flash('Insufficient permission', 'warning');
            return redirect('home');
        }

        return view('importcsv.index');
    }

    /**
     * Read CSV File into array of data
     *
     * @return \Illuminate\Http\Response
     */
    public function readCSVtoArr(Request $request)
    {
        $data = [];
        $file = $request->file('store_csv');

        if (!file_exists($file) || !is_readable($file)) {
            return [];
        }

        if (($handle = fopen($file, 'r')) !== false) {
            fgetcsv($handle, 1000); // baca header (ignore)
            while (($row = fgetcsv($handle, 1000)) !== false) {
                $row[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $row[0]); // menghilangkan \ufeff di kolom pertama ketika baca file csv
                $col = explode(";", $row[0]);
                
                $dataTemp = [];
                $dataTemp['store_code'] = $col[0];
                $dataTemp['initial'] = $col[1];
                $dataTemp['store_name'] = $col[2];
                
                //format date
                $dataTemp['created_at'] = ($col[3] != '')? Carbon::createFromFormat('Y-m-d H:i:s', $col[3])->format('Y-m-d H:i') : '';
                $dataTemp['updated_at'] = ($col[4] != '')? Carbon::createFromFormat('Y-m-d H:i:s', $col[4])->format('Y-m-d H:i') : '';

                $store = Store::where('store_code', $dataTemp['store_code'])->first();

                if (!isset($store)) {
                    //jika tidak ada sku maka tidak dapat menambahkan ke tabel
                    continue;
                }

                array_push($data, $dataTemp);
            }

            fclose($handle);
        }

        return $data;
    }
}
