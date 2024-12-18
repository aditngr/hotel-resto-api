<?php
header('Content-Type: application/json');

try {
    $client = new SoapClient("http://localhost/hotel-resto-api/soap/voucher.wsdl");
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['voucherCode'])) {
        throw new Exception("Kode voucher tidak ditemukan.");
    }

    // Panggil metode SOAP
    $response = $client->redeemVoucher($input['voucherCode']);
    echo json_encode(["message" => $response]);
} catch (SoapFault $e) {
    echo json_encode(["error" => "SOAP Error: " . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>

