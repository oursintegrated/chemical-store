<?php

include_once('TableText.php');

$tp = new TableText(100, 150);

$tp->setColumnLength(0, 7)
    ->setColumnLength(1, 7)
    ->setColumnLength(2, 44)
    ->setColumnLength(3, 18)
    ->setColumnLength(4, 20)
    ->setUseBodySpace(false);

$current_date = date("d M Y");
$tp->addColumn("Bandung,  " . $current_date, 5, "right")
    ->commit("right-greeting");
$tp->addColumn("Kepada YTH     ", 5, "right")
    ->commit("right-greeting");

$tp->addColumn("", 3, "center")
    ->addColumn("Bapak/Ibu/Toko", 2, "left")
    ->commit("right-greeting");
$tp->addColumn("", 3, "center")
    ->addColumn("I Ketut Adi Wicaksana" . " - " . "081224164852", 2, "left")
    ->commit("right-greeting");
$tp->addColumn("", 3, "center")
    ->addColumn("Komp Kota Baru Jalan Terata No 196", 2, "left")
    ->commit("right-greeting");

$tp->addLine("header");

$tp->addColumn("Qty. ", 1, "center")
    ->addColumn("Sat.", 1, "center")
    ->addColumn("Nama Barang", 1, "center")
    ->addColumn("Harga Satuan", 1, "center")
    ->addColumn("Jumlah (Rp.)", 1, "center")
    ->commit("header");

$tp->addColumn("1", 1, "left")
    ->addColumn("Kg", 1, "left")
    ->addColumn("Product Name", 1, "left")
    ->addColumn("Rp 100.000,00", 1, "right")
    ->addColumn("Rp 100.000,00", 1, "right")
    ->commit("body");

$tp->addColumn("Jumlah (Rp.)", 4, "right")
    ->addColumn("-", 1, "right")
    ->commit("footer");


$tp->addColumn("        Tanda Terima", 1, "center")
    ->addColumn("Hormat Kami", 1, "center")
    ->commit("footer-sign");

$tp->addColumn("", 1, "center")
    ->addColumn("", 1, "center")
    ->commit("footer-sign");

$tp->addColumn("", 1, "center")
    ->addColumn("", 1, "center")
    ->commit("footer-sign");

$tp->addColumn("", 1, "center")
    ->addColumn("", 1, "center")
    ->commit("footer-sign");

$tp->addColumn("", 1, "center")
    ->addColumn("", 1, "center")
    ->commit("footer-sign");

$tp->addColumn("          (....................)", 1, "left")
    ->addColumn("(....................)                          ", 1, "right")
    ->commit("footer-sign");

echo $tp->getText();
