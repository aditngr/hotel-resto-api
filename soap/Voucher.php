<?php
require_once '../db/connection.php';

class Voucher {
    /**
     * Menukarkan kode voucher untuk mendapatkan diskon.
     * @param string $voucherCode
     * @return string
     */
    public function redeemVoucher($voucherCode) {
        global $pdo;

        try {
            // Cek kode voucher
            $stmt = $pdo->prepare("SELECT * FROM vouchers WHERE code = ? AND is_used = 0");
            $stmt->execute([$voucherCode]);
            $voucher = $stmt->fetch();

            if ($voucher) {
                // Tandai voucher sudah digunakan
                $update = $pdo->prepare("UPDATE vouchers SET is_used = 1 WHERE id = ?");
                $update->execute([$voucher['id']]);

                return "Voucher berhasil ditukar! Anda mendapatkan diskon.";
            }
            return "Kode voucher tidak valid atau sudah digunakan.";
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    /**
     * Generate voucher setelah pembayaran berhasil
     * @return string
     */
    public function generateVoucher() {
        global $pdo;
    
        try {
            // Generate kode voucher unik
            $voucherCode = strtoupper(substr(md5(uniqid()), 0, 8));
    
            // Simpan kode voucher ke database
            $stmt = $pdo->prepare("INSERT INTO vouchers (code, is_used) VALUES (?, 0)");
            $stmt->execute([$voucherCode]);
    
            // Debug voucher untuk memastikan
            $result = "Voucher berhasil dibuat! Kode Voucher Anda: $voucherCode";
            error_log("Generated Voucher: " . $result);
            return $result;
        } catch (Exception $e) {
            error_log("Voucher Generation Error: " . $e->getMessage());
            return "Error: " . $e->getMessage();
        }
    }    
}
?>
