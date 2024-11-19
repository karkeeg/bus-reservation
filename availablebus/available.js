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
