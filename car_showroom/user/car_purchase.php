<?php
include '../db.php';
include 'header.php';

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM users WHERE user_id = $user_id");
$user = $result->fetch_assoc();

$car_id = $_GET['car_id'];
$carQuery = $conn->prepare("SELECT * FROM cars WHERE car_id = ?");
$carQuery->bind_param("i", $car_id);
$carQuery->execute();
$car = $carQuery->get_result()->fetch_assoc();

$citiesResult = $conn->query("SELECT * FROM cities");
$cities = [];
while ($row = $citiesResult->fetch_assoc()) {
    $cities[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $car_id = $_POST['car_id'];
    $payment_option = $_POST['payment_option'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $city_id = $_POST['city_id'];

    $carStmt = $conn->prepare("SELECT * FROM cars WHERE car_id = ?");
    $carStmt->bind_param("i", $car_id);
    $carStmt->execute();
    $car = $carStmt->get_result()->fetch_assoc();

    $cityStmt = $conn->prepare("SELECT * FROM cities WHERE city_id = ?");
    $cityStmt->bind_param("i", $city_id);
    $cityStmt->execute();
    $city = $cityStmt->get_result()->fetch_assoc();

    $installment_plan = ($payment_option === 'installment') ? (int)$_POST['installment_plan'] : 0;
    $installment_amount = ($installment_plan > 0) ? ($car['price'] + $city['delivery_charge']) / $installment_plan : 0;

    $_SESSION['cart'] = [
        'car_id' => $car_id,
        'model' => $car['model'],
        'price' => $car['price'],
        'image' => $car['image'],
        'features' => $car['features'],
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'city_id' => $city_id,
        'city_name' => $city['city_name'],
        'delivery_charge' => $city['delivery_charge'],
        'payment_option' => $payment_option,
        'installment_plan' => $installment_plan,
        'installment_amount' => $installment_amount,
        'total_amount' => $car['price'] + $city['delivery_charge']
    ];

    header("Location: checkout.php");
    exit;
}
?>

<div class="container mt-5 ">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card border rouned shadow p-3">
                <h3 class="mb-4 text-center">Purchase Car: <?= htmlspecialchars($car['model']) ?></h3>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <img src="<?= htmlspecialchars($car['image']) ?>" class="img-fluid rounded"
                            alt="<?= htmlspecialchars($car['model']) ?>">
                    </div>
                    <div class="col-md-6">
                        <p><strong>Base Price:</strong> $<?= number_format($car['price'], 2) ?></p>
                        <p><strong>Car Features:</strong></p>
                        <p><?= nl2br(htmlspecialchars($car['features'])) ?></p>
                        <p><strong>Total Price:</strong> $<span
                                id="total_price"><?= number_format($car['price'], 2) ?></span></p>
                    </div>
                </div>

                <form method="POST">
                    <input type="hidden" name="car_id" value="<?= $car_id ?>">

                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" value="<?= $user['name']; ?>" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="<?= $user['email']; ?>" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" value="<?= $user['phone']; ?>" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Select City</label>
                        <select name="city_id" id="city_select" class="form-select" onchange="updateDeliveryCharge()"
                            required>
                            <option value="">-- Select City --</option>
                            <?php foreach ($cities as $city): ?>
                            <option value="<?= $city['city_id'] ?>" data-charge="<?= $city['delivery_charge'] ?>">
                                <?= $city['city_name'] ?> (Delivery: Rs.
                                <?= number_format($city['delivery_charge'], 2) ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Option</label>
                        <select name="payment_option" id="payment_option" class="form-select"
                            onchange="toggleInstallmentPlan()" required>
                            <option value="full">Full Payment</option>
                            <option value="installment">Installment Plan</option>
                        </select>
                    </div>

                    <div id="installment_plan" class="mb-3" style="display: none;">
                        <label class="form-label">Installment Plan (Months)</label>
                        <select name="installment_plan" id="installment_plan_select" class="form-select"
                            onchange="updateInstallmentPrice()">
                            <option value="12">12 Months</option>
                            <option value="24">24 Months</option>
                            <option value="36">36 Months</option>
                        </select>
                    </div>
                    <div class="text-center">

                        <button type="submit" class="btn">Place Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const carPrice = <?= $car['price'] ?>;
let deliveryCharge = 0;

function toggleInstallmentPlan() {
    const option = document.getElementById("payment_option").value;
    const div = document.getElementById("installment_plan");

    if (option === "installment") {
        div.style.display = "block";
        updateInstallmentPrice();
    } else {
        div.style.display = "none";
        updateTotalPrice();
    }
}

function updateDeliveryCharge() {
    const citySelect = document.getElementById("city_select");
    const selectedOption = citySelect.options[citySelect.selectedIndex];
    deliveryCharge = parseFloat(selectedOption.getAttribute("data-charge")) || 0;

    updateTotalPrice();
}

function updateTotalPrice() {
    const total = carPrice + deliveryCharge;
    document.getElementById("total_price").textContent = total.toFixed(2);
}

function updateInstallmentPrice() {
    const months = parseInt(document.getElementById("installment_plan_select").value);
    const monthly = (carPrice + deliveryCharge) / months;
    document.getElementById("total_price").textContent = monthly.toFixed(2) + " per month";
}
</script>