document.addEventListener("DOMContentLoaded", () => {
  const params = new URLSearchParams(window.location.search);
  const type = params.get('type');

  if (!type) {
    alert("Missing ticket type!");
    return;
  }

  fetch(`php/generate-ticket.php?type=${type}`)
    .then(res => res.json())
    .then(data => {
      if (data.ticket) {
        document.getElementById("ticketNumber").textContent = data.ticket;
        document.getElementById("ticketDate").textContent = data.date;
      } else {
        alert("Failed to generate ticket.");
      }
    })
    .catch(err => {
      console.error("Error:", err);
      alert("Server error");
    });
});
