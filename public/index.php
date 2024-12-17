<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel & Resto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .booking-form, .food-order-form {
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .section-title {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }
        .menu-item {
            margin-bottom: 10px;
        }
        .menu-item button {
            margin-left: 10px;
        }
        #order-summary { 
            display: none; 
            margin-top: 20px; 
        }
        #order-summary table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        #order-summary th, #order-summary td { 
            border: 1px solid #ddd; 
            padding: 8px; 
            text-align: left; 
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Form Pemesanan Kamar -->
        <div class="booking-form" id="bookingForm">
            <h2 class="text-center mb-4">Pemesanan Kamar</h2>
            <form>
                <div class="mb-3">
                    <label for="roomType" class="form-label">Tipe Kamar</label>
                    <select name="roomType" id="roomType" class="form-select" required>
                        <option value="">Pilih Tipe Kamar</option>
                        <option value="Standard">Standard Room - Rp. 100.000/malam</option>
                        <option value="Deluxe">Deluxe Room - Rp. 150.000/malam</option>
                        <option value="Suite">Suite Room - Rp. 200.000/malam</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="nights" class="form-label">Jumlah Malam</label>
                    <input type="number" class="form-control" id="nights" name="nights" min="1" max="30" required>
                </div>
                <button type="button" onclick="showFoodOrderForm()" class="btn btn-primary w-100">Pesan Kamar</button>
            </form>
        </div>

        <!-- Form Pemesanan Makanan -->
        <div class="food-order-form" id="foodOrderForm" style="display: none;">
            <h2 class="text-center mb-4">Pemesanan Makanan</h2>
            <div id="menu"></div>

            <h4 class="section-title">Order Summary</h4>
            <div id="order-summary">
                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody id="order-items"></tbody>
                </table>
                <button onclick="placeOrder()" class="btn btn-primary w-100">Lanjut Pembayaran</button>
            </div>

            <!-- Tombol Tukar Voucher -->
            <button onclick="redeemVoucher()" class="btn btn-success w-100 mt-3">Tukarkan Voucher</button>

            <!-- Tombol Lewati dan Kembali -->
            <button type="button" onclick="skipFoodOrder()" class="btn btn-secondary w-100 mt-3">Lewati</button>
            <button type="button" onclick="backToRoomBooking()" class="btn btn-outline-primary w-100 mt-3">Kembali ke Pemesanan Kamar</button>
        </div>
    </div>

    <script>
        // Fungsi untuk menampilkan form pemesanan makanan
        function showFoodOrderForm() {
            document.getElementById('bookingForm').style.display = 'none';
            document.getElementById('foodOrderForm').style.display = 'block';
            loadMenu();
        }

        function backToRoomBooking() {
            document.getElementById('foodOrderForm').style.display = 'none';
            document.getElementById('bookingForm').style.display = 'block';
        }

        function skipFoodOrder() {
            document.getElementById('foodOrderForm').style.display = 'none';
            alert('Anda telah melewati pemesanan makanan.');
        }

        const menuContainer = document.getElementById('menu');
        const orderSummary = document.getElementById('order-summary');
        const orderItems = document.getElementById('order-items');
        let cart = [];

        async function loadMenu() {
            try {
                const response = await fetch('http://localhost/hotel-resto-api/public/menu.php');
                const menu = await response.json();

                menu.forEach(item => {
                    const div = document.createElement('div');
                    div.className = 'menu-item';
                    div.innerHTML = `
                        ${item.name} - Rp${item.price}
                        <button onclick="addToCart(${item.id}, '${item.name}', ${item.price})" class="btn btn-outline-primary btn-sm">Add to Cart</button>
                    `;
                    menuContainer.appendChild(div);
                });
            } catch (error) {
                console.error("Error loading menu:", error);
            }
        }

        function addToCart(id, name, price) {
            cart.push({ id, name, price });
            updateOrderSummary();
        }

        function updateOrderSummary() {
            orderItems.innerHTML = '';
            cart.forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `<td>${item.name}</td><td>Rp${item.price}</td>`;
                orderItems.appendChild(row);
            });
            orderSummary.style.display = 'block';
        }

    async function placeOrder() {
        const response = await fetch('http://localhost/hotel-resto-api/public/order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ items: cart.map(item => item.id) })
    });

    const result = await response.json();
    alert(`${result.message}\n${result.voucher}`);
    }


        async function redeemVoucher() {
            const voucherCode = prompt("Masukkan Kode Voucher:");
            if (voucherCode) {
                const response = await fetch('http://localhost/hotel-resto-api/public/redeem-voucher.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ voucherCode })
                });
                const result = await response.json();
                alert(result.message || result.error);
            }
        }
    </script>

</body>
</html>
