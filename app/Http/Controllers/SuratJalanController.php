<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Role;
use App\Customer;
use Illuminate\Support\Facades\DB;
use Validator;
use App\Lib\TableText;

class SuratJalanController extends Controller
{
    public function index()
    {
        /* RBAC */
        if (!Role::authorize('surat-jalan.index')) {
            flash('Insufficient permission', 'warning');
            return redirect('dashboard');
        }

        $x = new HomeController;
        $data['menu'] = $x->getMenu();

        $data['customers'] = Customer::all();

        return view('suratjalan.index', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /* RBAC */
        if (!Role::authorize('surat-jalan.index')) {
            return response()->json(array('status' => 0, 'message' => 'Insufficient permission.'));
        }

        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'customer_name' => 'required',
                'address' => 'required',
                'data_product' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(array('status' => 0, 'message' => $validator->errors()->first()));
            } else {
                $customer_id = $request->input('customer_id');
                $customer_name = $request->input('customer_name');
                $phone_number = $request->input('phone_number');
                $address = $request->input('address');
                $products = $request->input('data_product');

                // ==================== PRINT SURAT JALAN
                // include_once('TableText.php');
                $tmpdir = sys_get_temp_dir();   # ambil direktori temporary untuk simpan file.

                $file =  tempnam($tmpdir, 'srt');  # nama file temporary yang akan dicetak
                $handle = fopen($file, 'w+');

                $tp = new TableText(75, 63);

                $tp->setColumnLength(0, 7)
                    ->setColumnLength(1, 7)
                    ->setColumnLength(2, 20)
                    ->setColumnLength(3, 12)
                    ->setColumnLength(4, 13)
                    ->setColumnLength(5, 13)
                    ->setUseBodySpace(false);

                $current_date = date("d M Y");
                $tp->addColumn("Bandung,  " . $current_date, 6, "right")
                    ->commit("right-greeting");
                $tp->addColumn("Kepada YTH     ", 6, "right")
                    ->commit("right-greeting");

                $tp->addColumn("Surat Jalan No.", 4, "left")
                    ->addColumn("Bapak/Ibu/Toko", 2, "left")
                    ->commit("right-greeting");
                $tp->addColumn("Bersama ini kendaraan ........ No ........", 4, "left")
                    ->addColumn($customer_name . " - " . $phone_number, 2, "left")
                    ->commit("right-greeting");
                $tp->addColumn("Kami kirimkan barang tersebut di bawah ini harap diterima dengan baik", 4, "left")
                    ->addColumn($address, 2, "left")
                    ->commit("right-greeting");

                $tp->addLine("header");

                $tp->addColumn("Qty. ", 1, "center")
                    ->addColumn("Sat.", 1, "center")
                    ->addColumn("Nama Barang", 4, "center")
                    ->commit("header");

                for ($i = 0; $i < count($products); $i++) {
                    $tp->addColumn($products[$i]['qty'], 1, "left")
                        ->addColumn($products[$i]['unit'], 1, "left")
                        ->addColumn($products[$i]['product_name'], 4, "left")
                        ->commit("body");
                }

                $tp->addColumn("", 3, "center")
                    ->addColumn("", 3, "center")
                    ->commit("footer-sign");

                $tp->addColumn("Tanda Terima yang terima", 3, "center")
                    ->addColumn("Hormat Kami", 3, "center")
                    ->commit("footer-sign");

                $tp->addColumn("", 3, "center")
                    ->addColumn("", 3, "center")
                    ->commit("footer-sign");

                $tp->addColumn("", 3, "center")
                    ->addColumn("", 3, "center")
                    ->commit("footer-sign");

                $tp->addColumn("", 3, "center")
                    ->addColumn("", 3, "center")
                    ->commit("footer-sign");

                $tp->addColumn("", 3, "center")
                    ->addColumn("", 3, "center")
                    ->commit("footer-sign");

                $tp->addColumn("(....................)", 3, "center")
                    ->addColumn("(....................)", 3, "center")
                    ->commit("footer-sign");

                $tp->addColumn("", 6, "left")
                    ->commit("footer-sign");

                fwrite($handle, $tp->getText());
                fclose($handle);

                $handleFile = file($file);

                $handle = fopen($file, 'w+');
                $linecount = 0;
                for ($i = 0; $i < count($handleFile); $i++) {
                    fwrite($handle, $handleFile[$i]);
                    if ($i != 0 && ($i % 28) == 0) {
                        fwrite($handle, "\n");
                        fwrite($handle, "\n");
                        fwrite($handle, "\n");
                        fwrite($handle, "\n");
                    }
                }
                fclose($handle);

                // copy($file, "//DESKTOP-BPD4EKO/EPSON LX-310");  # Lakukan cetak
                // unlink($file);

                DB::commit();

                return response()->json(array('status' => 1, 'message' => 'Successfully print surat jalan.', 'intended_url' => '/surat-jalan'));
            }
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(array('status' => 0, 'message' => 'Something went wrong.'));
        }
    }
}
