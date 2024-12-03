
const form = document.getElementById("busSearchForm");
const fromInput = document.getElementById("from");
const toInput = document.getElementById("to");
const dateInput = document.getElementById("date");
const errorMsg = document.getElementById("error");

form.addEventListener("submit", function (event) {
  // Clear previous error messages
  errorMsg.style.display = "none";

  const fromValue = fromInput.value.trim();
  const toValue = toInput.value.trim();
  const dateValue = dateInput.value;

  // Validate that 'From' and 'To' fields are not the same
  if (fromValue === toValue) {
    event.preventDefault(); // Prevent form submission
    errorMsg.textContent =
      "The 'From' and 'To' destinations cannot be the same.";
    errorMsg.style.display = "block";
    return;
  }

  // Validate that all fields are filled
  if (!fromValue || !toValue || !dateValue) {
    event.preventDefault(); // Prevent form submission
    errorMsg.textContent = "Please fill in all the fields.";
    errorMsg.style.display = "block";
    return;
  }

  // Validate that the date is not in the past
  const currentDate = new Date().toISOString().split("T")[0]; // Today's date
  if (dateValue < currentDate) {
    event.preventDefault(); // Prevent form submission
    errorMsg.textContent = "The date cannot be in the past.";
    errorMsg.style.display = "block";
    return;
  }

  // If all validations pass, allow the form to be submitted
  alert(
    `Searching buses from ${fromValue} to ${toValue} on ${dateValue}`
  );
});


