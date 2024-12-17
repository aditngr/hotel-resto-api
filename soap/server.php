<?php
require_once 'Voucher.php';

try {
    // Membuat SOAP server dan menghubungkannya ke WSDL
    $server = new SoapServer("voucher.wsdl");
    $server->setClass('Voucher'); // Menggunakan class Voucher untuk service
    $server->handle();
} catch (Exception $e) {
    error_log("SOAP Server Error: " . $e->getMessage());
    echo "SOAP Server Error: " . $e->getMessage();
}
?>
