<?php

include_once('TableText.php');

$tp = new TableText(100, 150);

$tp->setColumnLength(0, 7)
    ->setColumnLength(1, 7)
    ->setColumnLength(2, 22)
    ->setColumnLength(3, 22)
    ->setColumnLength(4, 18)
    ->setColumnLength(5, 20)
    ->setUseBodySpace(false);

$current_date = date("d M Y");
$tp->addColumn("Bandung,  " . $current_date, 6, "right")
    ->commit("right-greeting");
$tp->addColumn("Kepada YTH     ", 6, "right")
    ->commit("right-greeting");

$tp->addColumn("", 4, "center")
    ->addColumn("Bapak/Ibu/Toko", 4, "left")
    ->commit("right-greeting");
$tp->addColumn("", 4, "center")
    ->addColumn("I Ketut Adi Wicaksana" . " - " . "081224164852", 2, "left")
    ->commit("right-greeting");
$tp->addColumn("", 4, "center")
    ->addColumn("Komp Kota Baru Jalan Terata No 196", 2, "left")
    ->commit("right-greeting");

$tp->addLine("header");

$tp->addColumn("Qty. ", 1, "center")
    ->addColumn("Sat.", 1, "center")
    ->addColumn("Nama Barang", 2, "center")
    ->addColumn("Harga Satuan", 1, "center")
    ->addColumn("Jumlah (Rp.)", 1, "center")
    ->commit("header");

$tp->addColumn("1", 1, "left")
    ->addColumn("Kg", 1, "left")
    ->addColumn("Product Name", 2, "left")
    ->addColumn("Rp 100.000,00", 1, "right")
    ->addColumn("Rp 100.000,00", 1, "right")
    ->commit("body");

$tp->addColumn("Jumlah (Rp.)", 4, "right")
    ->addColumn("-", 2, "right")
    ->commit("footer");


$tp->addColumn("        Tanda Terima", 3, "center")
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
    ->addColumn("(....................)", 3, "right")
    ->commit("footer-sign");

echo $tp->getText();
