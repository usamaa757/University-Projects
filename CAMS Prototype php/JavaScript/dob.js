document.getElementById("dob").addEventListener("change", function () {
    var dobInput = document.getElementById("dob").value;
    var dob = new Date(dobInput);
    
    // Get today's date
    var today = new Date();
    
    // Calculate the age
    var age = today.getFullYear() - dob.getFullYear();
    var monthDiff = today.getMonth() - dob.getMonth();
    
    // Adjust if the birthday hasn't occurred yet this year
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
        age--;
    }
    
    // Show error if age is less than 18
    if (age < 18) {
        document.getElementById("dob-error").style.display = "inline";
    } else {
        document.getElementById("dob-error").style.display = "none";
    }
});