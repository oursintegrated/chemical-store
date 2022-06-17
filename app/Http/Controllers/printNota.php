<?php

// include_once('TableText.php');

// $tmpdir = sys_get_temp_dir();   # ambil direktori temporary untuk simpan file.
// echo($tmpdir);
// $file =  tempnam($tmpdir, 'ctk');  # nama file temporary yang akan dicetak
// $handle = fopen($file, 'w');

// $tp = new TableText(100, 150);

// $tp->setColumnLength(0, 7)
//     ->setColumnLength(1, 7)
//     ->setColumnLength(2, 22)
//     ->setColumnLength(3, 22)
//     ->setColumnLength(4, 18)
//     ->setColumnLength(5, 20)
//     ->setUseBodySpace(false);

// $current_date = date("d M Y");
// $tp->addColumn("Bandung,  " . $current_date, 6, "right")
//     ->commit("right-greeting");
// $tp->addColumn("Kepada YTH     ", 6, "right")
//     ->commit("right-greeting");

// $tp->addColumn("", 4, "center")
//     ->addColumn("Bapak/Ibu/Toko", 2, "left")
//     ->commit("right-greeting");
// $tp->addColumn("", 4, "center")
//     ->addColumn($customer_name . " - " . $phone_number, 2, "left")
//     ->commit("right-greeting");
// $tp->addColumn("", 4, "center")
//     ->addColumn($address, 2, "left")
//     ->commit("right-greeting");

// $tp->addLine("header");

// $tp->addColumn("Qty. ", 1, "center")
//     ->addColumn("Sat.", 1, "center")
//     ->addColumn("Nama Barang", 2, "center")
//     ->addColumn("Harga Satuan", 1, "center")
//     ->addColumn("Jumlah (Rp.)", 1, "center")
//     ->commit("header");

// for($i=0; $i<count($data_product); $i++){
//     $tp->addColumn($data_product[$i]['qty'], 1, "left")
//     ->addColumn($data_product[$i]['unit'], 1, "left")
//     ->addColumn($data_product[$i]['product_name'], 2, "left")
//     ->addColumn(number_format($data_product[$i]['price'], 2, ',', '.'), 1, "right")
//     ->addColumn(number_format($data_product[$i]['total'], 2, ',', '.'), 1, "right")
//     ->commit("body");
// }

// $tp->addColumn("Jumlah (Rp.)", 4, "right")
//     ->addColumn($total, 2, "right")
//     ->commit("footer");

// $tp->addColumn("", 3, "center")
// ->addColumn("", 3, "center")
// ->commit("footer-sign");

// $tp->addColumn("Tanda Terima", 3, "center")
//     ->addColumn("Hormat Kami", 3, "center")
//     ->commit("footer-sign");

// $tp->addColumn("", 3, "center")
//     ->addColumn("", 3, "center")
//     ->commit("footer-sign");

// $tp->addColumn("", 3, "center")
//     ->addColumn("", 3, "center")
//     ->commit("footer-sign");

// $tp->addColumn("", 3, "center")
//     ->addColumn("", 3, "center")
//     ->commit("footer-sign");

// $tp->addColumn("", 3, "center")
//     ->addColumn("", 3, "center")
//     ->commit("footer-sign");

// $tp->addColumn("(....................)", 3, "center")
//     ->addColumn("(....................)", 3, "center")
//     ->commit("footer-sign");

// $tp->addColumn("", 6, "left")
// ->commit("footer-sign");

// $tp->addColumn("Catatan: untuk pembayaran transfer dapat dikirimkan ke: " . $rekening, 6, "left")
// ->commit("footer-sign");


// fwrite($handle, $tp->getText());
// fclose($handle);

// copy($file, "//localhost/xprinter");  # Lakukan cetak
// unlink($file);
