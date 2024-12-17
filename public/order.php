<?php
require_once '../db/connection.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $items = $input['items'];

    if (empty($items)) {
        http_response_code(400);
        echo json_encode(["message" => "Order items cannot be empty"]);
        exit;
    }

    try {
        // Panggil SOAP server
        $client = new SoapClient("http://localhost/hotel-resto-api/soap/voucher.wsdl");
        $voucherResponse = $client->generateVoucher();
    
        // Debug respons untuk melihat isinya
        error_log("SOAP Response: " . print_r($voucherResponse, true));
    
        echo json_encode([
            "message" => "Order placed successfully",
            "voucher" => $voucherResponse
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["message" => "Failed to place order", "error" => $e->getMessage()]);
    }    
}
?>
