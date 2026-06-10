document.getElementById('stud_admission').addEventListener('submit', function(event) {
    let obtainedMarks = document.querySelectorAll('input[name="obtained_marks[]"]');
    let totalMarks = document.querySelectorAll('input[name="total_marks[]"]');
    let valid = true;

    for (let i = 0; i < obtainedMarks.length; i++) {
        if (parseInt(obtainedMarks[i].value) > parseInt(totalMarks[i].value)) {
            alert('Obtained marks cannot be greater than total marks');
            valid = false;
            break;
        }
    }

    if (!valid) {
        event.preventDefault(); // Prevent form submission if validation fails
    }
});




// document.getElementById("admissionForm").addEventListener("submit", function (event) {
//     var dobInput = document.getElementById("dob").value;
//     var dob = new Date(dobInput);
    
//     var today = new Date();
//     var age = today.getFullYear() - dob.getFullYear();
//     var monthDiff = today.getMonth() - dob.getMonth();
    
//     if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
//         age--;
//     }
    
//     if (age < 18) {
//         event.preventDefault();  // Prevent form submission
//         document.getElementById("dob-error").style.display = "inline";
//         alert("You must be at least 18 years old to submit the form.");
//     }
// });
