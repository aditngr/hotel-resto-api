<?php
require "vendor/autoload.php";
require "Voucher.php";

use PHP2WSDL\PHPClass2WSDL;

// Lokasi endpoint server SOAP
$endpoint = "http://localhost/hotel-resto-api/soap/server.php";

// Generate WSDL
$gen = new PHPClass2WSDL("Voucher", $endpoint);
$gen->generateWSDL();

// Simpan ke file
file_put_contents("voucher.wsdl", $gen->dump());
echo "WSDL berhasil dibuat!";
?>
