<?php
include '../db.php';
include 'header.php';
$propertyPrice = '';
$propertyId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($propertyId > 0) {
    $stmt = $conn->prepare("SELECT price FROM properties WHERE id = ?");
    $stmt->bind_param("i", $propertyId);
    $stmt->execute();
    $stmt->bind_result($propertyPrice);
    $stmt->fetch();
    $stmt->close();
}
?>

<div class="container">
    <section class="section">

        <div class="section-header">

            <h2>Estimate Your Monthly Installment</h2>
        </div>


        <label for="propertyPrice">Total Property Price ($)</label>
        <input type="number" id="propertyPrice" value="<?= htmlspecialchars($propertyPrice) ?>" />


        <label for="downPayment">Down Payment ($)</label>
        <input type="number" id="downPayment" placeholder="e.g. 2,000,000" />

        <label for="installmentPeriod">Installment Period (Years)</label>
        <input type="number" id="installmentPeriod" placeholder="e.g. 15" />

        <label for="markupRate">Applied Markup Rate (%)</label>
        <input type="text" id="markupRate" readonly style="background-color:#f0f0f0;" disabled />

        <div class="text-center">

            <button onclick="calculateInstallment()" class="btn">Calculate Installment</button>
        </div>

        <div class="results" id="results"></div>

        <form action="checkout.php" method="POST" id="buyForm" style="display: none;">
            <input type="hidden" name="remaining_balance" id="loanAmountField">
            <input type="hidden" name="period_years" id="tenureField">
            <input type="hidden" name="markup_rate" id="rateField">
            <input type="hidden" name="monthly_installment" id="emiField">
            <input type="hidden" name="total_payment" id="totalPaymentField">
            <input type="hidden" name="property_id" value="<?= $propertyId ?>">
            <div class="text-center">

                <button type="submit" name="confirmBuy" class="btn">Proceed to Payment</button>
            </div>

        </form>
    </section>

</div>

<script>
function calculateInstallment() {
    const price = parseFloat(document.getElementById('propertyPrice').value);
    const down = parseFloat(document.getElementById('downPayment').value);
    const years = parseFloat(document.getElementById('installmentPeriod').value);

    if (isNaN(price) || isNaN(down) || isNaN(years)) {
        alert('Please fill all values correctly.');
        return;
    }

    if (down >= price) {
        alert('Down payment must be less than property price.');
        return;
    }

    let rate;
    if (years <= 5) rate = 11;
    else if (years <= 10) rate = 12;
    else rate = 13;

    document.getElementById('markupRate').value = rate + "%";

    const balance = price - down;
    const monthlyRate = rate / 12 / 100;
    const months = years * 12;

    const installment = (balance * monthlyRate * Math.pow(1 + monthlyRate, months)) /
        (Math.pow(1 + monthlyRate, months) - 1);
    const totalPayment = installment * months;
    const totalMarkup = totalPayment - balance;

    document.getElementById('results').innerHTML = `
            <p><strong>Remaining Balance:</strong> $ ${balance.toFixed(0)}</p>
            <p><strong>Monthly Installment:</strong> $ ${installment.toFixed(0)}</p>
            <p><strong>Total Markup Payable:</strong> $ ${totalMarkup.toFixed(0)}</p>
            <p><strong>Total Payment:</strong> $ ${totalPayment.toFixed(0)}</p>
        `;

    document.getElementById('loanAmountField').value = Math.round(balance);
    document.getElementById('tenureField').value = years;
    document.getElementById('rateField').value = rate;
    document.getElementById('emiField').value = Math.round(installment);
    document.getElementById('totalPaymentField').value = Math.round(totalPayment);
    document.getElementById('results').style.display = 'block';
    document.getElementById('buyForm').style.display = 'block';
}
</script>
</body>

</html>

<?php include '../footer.php';
if (isset($_POST['confirmBuy'])) {
    $stmt = $conn->prepare("INSERT INTO purchases (property_id, remaining_balance, period_years, markup_rate, monthly_installment, total_payment) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiidii", $_POST['property_id'], $_POST['remaining_balance'], $_POST['period'], $_POST['rate'], $_POST['installment'], $_POST['total_payment']);
    if ($stmt->execute()) {
        echo "<script>alert('Purchase confirmed!'); window.location.href='thankyou.php';</script>";
    } else {
        echo "<script>alert('Failed to confirm purchase');</script>";
    }
    $stmt->close();
}
?>