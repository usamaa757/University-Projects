<?php
include 'header.php';
?>
<div class="container">
    <section class="section">
        <div class="section-header">

            <h2><i class="fas fa-calculator"></i> Installment Calculator</h2>
        </div>

        <label for="propertyPrice">Total Property Price (PKR)</label>
        <input type="number" id="propertyPrice" placeholder="e.g. 10,000,000" />

        <label for="downPayment">Down Payment (PKR)</label>
        <input type="number" id="downPayment" placeholder="e.g. 2,000,000" />

        <label for="installmentPeriod">Installment Period (Years)</label>
        <input type="number" id="installmentPeriod" placeholder="e.g. 15" />

        <label for="markupRate">Applied Markup Rate (%)</label>
        <input type="text" id="markupRate" readonly style="background-color:#f0f0f0;" />

        <div class="text-center">
            <button onclick="calculateInstallment()" class="btn">Calculate Installment</button>
        </div>

        <div class="results" id="results" style="margin-top: 20px;"></div>
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
            <p><strong>Monthly Installment:</strong> PKR ${installment.toFixed(0).toLocaleString()}</p>
            <p><strong>Total Markup Payable:</strong> PKR ${totalMarkup.toFixed(0).toLocaleString()}</p>
            <p><strong>Total Payment:</strong> PKR ${totalPayment.toFixed(0).toLocaleString()}</p>
            `;
}
</script>
<?php include 'footer.php'; ?>