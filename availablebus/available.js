// Show Bus Info Modal
function showBusInfo(busId) {
    const modal = document.getElementById("busInfoModal");

    // Example bus data - Replace with dynamic data as needed
    const buses = {
        bus1: {
            number: "1234",
            driver: "Sujay Chaudhary",
            details: "This is a luxury bus with AC and reclining seats."
        },
        bus2: {
            number: "5678",
            driver: "Binayak Aryal",
            details: "This is a standard bus with comfortable seating."
        }
        // Add more buses as needed
    };

    if (buses[busId]) {
        document.getElementById("busNumber").textContent = "Bus Number: " + buses[busId].number;
        document.getElementById("driverName").textContent = "Driver: " + buses[busId].driver;
        document.getElementById("busDetails").textContent = buses[busId].details;
    }

    modal.style.display = "flex";
}

// Close Bus Info Modal
function closeBusInfoModal() {
    document.getElementById("busInfoModal").style.display = "none";
}


const locations = [
    "Kathmandu",
    "Pokhara",
    "Biratnagar",
    "Chitwan",
    "Lumbini",
    "Dhulikhel",
    "Nepalgunj",
  ];

  function showSuggestions(input, type) {
    const query = input.value.toLowerCase();
    const listId = type === "pickup" ? "pickup-list" : "destination-list";
    const suggestionList = document.getElementById(listId);

    suggestionList.innerHTML = ""; // Clear old suggestions
    suggestionList.style.display = query ? "block" : "none";

    if (!query) return;

    const filteredLocations = locations.filter((loc) =>
      loc.toLowerCase().includes(query)
    );

    filteredLocations.forEach((loc) => {
      const item = document.createElement("div");
      item.classList.add("autocomplete-item");
      item.textContent = loc;
      item.onclick = () => {
        input.value = loc;
        suggestionList.style.display = "none";
      };
      suggestionList.appendChild(item);
    });
  }

  function selectDay(element, date) {
    document.querySelectorAll('.day').forEach(day => {
      day.classList.remove('active');
    });
    element.classList.add('active');

    // Update the travel-date input with the selected date
    const travelDateInput = document.getElementById('travel-date');
    travelDateInput.value = date;
  }
