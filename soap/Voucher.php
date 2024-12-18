<?php
require_once '../db/connection.php';

class HotelService {
    private $userPoints;

    public function __construct($initialPoints) {
        $this->userPoints = $initialPoints; // Inisialisasi poin pengguna
    }

    /**
     * Menukarkan poin untuk mendapatkan makanan.
     * @param int $points Jumlah poin yang ingin ditukarkan
     * @param string $item Nama makanan yang ingin ditukarkan
     * @return string
     */
    public function redeemPoints($points, $item) {
        global $pdo;

        try {
            // Memeriksa apakah poin cukup
            if ($this->verifyPoints($points)) {
                // Mengurangi jumlah poin yang dimiliki pengguna
                $this->userPoints -= $points;

                // Simpan informasi penukaran ke database (misal: riwayat penukaran)
                $stmt = $pdo->prepare("INSERT INTO redemptions (item, points_used) VALUES (?, ?)");
                $stmt->execute([$item, $points]);

                return "Anda berhasil menukar $item dengan $points poin.";
            }
            return "Poin tidak cukup untuk menukarkan $item.";
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    /**
     * Memverifikasi apakah poin pengguna mencukupi.
     * @param int $points Jumlah poin yang diperlukan
     * @return bool
     */
    public function verifyPoints($points) {
        return $this->userPoints >= $points;
    }

    /**
     * Memesan makanan menggunakan poin.
     * @param string $item Nama makanan yang akan dipesan
     * @param int $points Jumlah poin yang akan digunakan
     * @return string
     */
    public function orderFoodWithPoints($item, $points) {
        global $pdo;

        try {
            // Verifikasi apakah pengguna memiliki poin yang cukup
            if ($this->verifyPoints($points)) {
                return $this->redeemPoints($points, $item);
            }
            return "Poin tidak cukup untuk menukarkan $item.";
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }
}

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
