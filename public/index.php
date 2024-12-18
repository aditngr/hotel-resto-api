<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel & Resto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .booking-form, .food-order-form, .invoice {
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
        .points-section {
            margin-top: 20px;
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
                    <input type="number" class="form-control" id="nights" min="1" max="30" required>
                </div>
                <button type="button" onclick="showFoodOrderForm()" class="btn btn-primary w-100">Pesan Kamar</button>
            </form>
        </div>

        <!-- Form Pemesanan Makanan -->
        <div class="food-order-form" id="foodOrderForm" style="display: none;">
            <h2 class="text-center mb-4">Pemesanan Makanan</h2>
            <div id="menu"></div>

            <h4 class="section-title">Order Summary</h4>
            <div id="order-summary"></div>

            <!-- Redeem Points Section -->
            <div class="points-section">
                <h4 class="section-title">Tukar Poin</h4>
                <p>Poin Anda: <span id="userPoints">500</span></p>
                <input type="number" class="form-control mb-2" id="redeemPoints" placeholder="Masukkan jumlah poin" min="100">
                <button onclick="redeemPointsForFood()" class="btn btn-outline-success w-100">Tukar Poin untuk Makanan</button>
            </div>

            <button onclick="showInvoice(true)" class="btn btn-primary w-100">Lanjut Pembayaran</button>
            <button onclick="showInvoice(false)" class="btn btn-secondary w-100 mt-3">Lewati</button>
            <button onclick="backToRoomBooking()" class="btn btn-outline-primary w-100 mt-3">Kembali</button>
        </div>

        <!-- Halaman Tagihan -->
        <div class="invoice" id="invoicePage" style="display: none;">
            <h2 class="text-center mb-4">Tagihan Anda</h2>
            <div id="invoiceDetails"></div>
            <button onclick="location.reload()" class="btn btn-success w-100">Selesai</button>
        </div>
    </div>

    <script>
        let cart = [];
        let roomTotal = 0;
        let userPoints = 500; 
        let foodTotal = 0; 

        document.getElementById('userPoints').textContent = userPoints;

        function showFoodOrderForm() {
            const roomType = document.getElementById('roomType').value;
            const nights = document.getElementById('nights').value;

            if (!roomType || nights <= 0) {
                alert('Harap pilih tipe kamar dan jumlah malam.');
                return;
            }

            let pricePerNight = 0;
            if (roomType === 'Standard') pricePerNight = 100000;
            if (roomType === 'Deluxe') pricePerNight = 150000;
            if (roomType === 'Suite') pricePerNight = 200000;

            roomTotal = pricePerNight * nights;

            document.getElementById('bookingForm').style.display = 'none';
            document.getElementById('foodOrderForm').style.display = 'block';
            loadMenu();
        }

        function backToRoomBooking() {
            document.getElementById('foodOrderForm').style.display = 'none';
            document.getElementById('bookingForm').style.display = 'block';
        }

        async function loadMenu() {
            const menuContainer = document.getElementById('menu');
            menuContainer.innerHTML = ''; 

            try {
                const response = await fetch('http://localhost/hotel-resto-api/public/menu.php');
                const menu = await response.json();

                menu.forEach(item => {
                    const div = document.createElement('div');
                    div.className = 'menu-item';
                    div.innerHTML = `
                        ${item.name} - Rp${item.price}
                        <button onclick="addToCart('${item.name}', ${item.price})" class="btn btn-outline-primary btn-sm">Add</button>
                    `;
                    menuContainer.appendChild(div);
                });
            } catch (error) {
                console.error("Error loading menu:", error);
                menuContainer.innerHTML = '<p class="text-danger">Gagal memuat menu. Coba lagi nanti.</p>';
            }
        }

        function addToCart(name, price) {
            cart.push({ name, price });
            foodTotal += price;
            updateOrderSummary();
        }

        function updateOrderSummary() {
            const summary = cart.map(item => `<div>${item.name} - Rp${item.price}</div>`).join('');
            document.getElementById('order-summary').innerHTML = summary + `<div><strong>Total Makanan: Rp${foodTotal}</strong></div>`;
        }

        function redeemPointsForFood() {
            const points = parseInt(document.getElementById('redeemPoints').value);

            if (points <= 0 || points > userPoints) {
                alert("Poin tidak mencukupi.");
                return;
            }

            let pointValue = points * 100;

            foodTotal -= pointValue;

            userPoints -= points;
            document.getElementById('userPoints').textContent = userPoints;
            updateOrderSummary();
        }

        function showInvoice(includeFood) {
            document.getElementById('foodOrderForm').style.display = 'none';
            document.getElementById('invoicePage').style.display = 'block';

            let total = roomTotal + (includeFood ? foodTotal : 0);

            let invoiceHtml = `
                <p><strong>Tagihan Kamar:</strong> Rp${roomTotal}</p>
                ${includeFood ? `<p><strong>Tagihan Makanan:</strong> Rp${foodTotal}</p>` : ''}
                <hr>
                <h4>Total: Rp${total}</h4>
            `;

            document.getElementById('invoiceDetails').innerHTML = invoiceHtml;
        }
    </script>
</body>
</html>
