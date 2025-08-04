const department = localStorage.getItem("selectedDepartment") || "S";

const departmentNames = {
  S: "Senior / PWD",
  CI: "Account Solutions",
  FC: "File Complaint"
};

document.getElementById("cashier-title").innerText = departmentNames[department] || "Department";

const currentTicketEl = document.getElementById("current-ticket");
const queueList = document.getElementById("queue-list");

let skippedTickets = []; // support multiple undos

function fetchQueue() {
  fetch(`php/queue-list.php?department=${encodeURIComponent(department)}`)
    .then(res => res.json())
    .then(data => {
      queueList.innerHTML = "";
      if (data.length === 0) {
        queueList.innerHTML = "<div style='text-align:center;'>No queue</div>";
        currentTicketEl.textContent = "---";
        return;
      }

      data.forEach((item, index) => {
        const div = document.createElement("div");
        div.className = "queue-item" + (index === 0 ? " active" : "");
        div.innerHTML = `
          <div class="queue-content">
            <span class="label">${item.ticket_number}</span>
            <span>${item.status}</span>
          </div>`;
        queueList.appendChild(div);

        if (index === 0) {
          currentTicketEl.textContent = item.ticket_number;
        }
      });
    });
}

// NEXT button
document.getElementById("next-btn").addEventListener("click", () => {
  const skipped = currentTicketEl.textContent;
  if (skipped && skipped !== "---") {
    skippedTickets.push(skipped);
  }

  fetch("php/queue-next.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ department })
  })
    .then(() => {
      return fetch("php/queue-call.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ department })
      });
    })
    .then(() => {
      fetchQueue();
    });
});

// CALL button
document.getElementById("call-btn").addEventListener("click", () => {
  fetch("php/queue-call.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ department })
  });
});

// SKIP button
document.getElementById("skip-btn").addEventListener("click", () => {
  const skipped = currentTicketEl.textContent;
  if (skipped && skipped !== "---") {
    skippedTickets.push(skipped);
  }

  fetch("php/queue-skip.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ department })
  }).then(fetchQueue);
});

// UNDO button (supports multiple undos)
document.getElementById("undo-btn").addEventListener("click", () => {
  if (skippedTickets.length === 0) {
    alert("Nothing to undo.");
    return;
  }

  const ticketToUndo = skippedTickets.pop();

  fetch("php/queue-undo.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      department,
      ticket_number: ticketToUndo
    })
  })
    .then(res => res.json())
    .then(result => {
      if (result.success) {
        fetchQueue();
      } else {
        alert("Undo failed.");
        skippedTickets.push(ticketToUndo); // return it to stack if it failed
      }
    })
    .catch(error => {
      console.error("Undo error:", error);
      skippedTickets.push(ticketToUndo);
    });
});

// Auto refresh
setInterval(fetchQueue, 2000);
fetchQueue();
