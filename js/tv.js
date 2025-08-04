function updateClock() {
  const now = new Date();
  const clock = document.getElementById('clock');
  if (clock) clock.textContent = now.toLocaleTimeString();
}

function loadDisplayData() {
  fetch('php/fetch-cashier-status.php')
    .then(res => res.json())
    .then(data => {
      for (let i = 1; i <= 3; i++) {
        const match = data.find(item => item.cashier_id == i);
        const footerEl = document.getElementById('cashier' + i);
        if (footerEl && match) {
          footerEl.textContent = match.type + String(match.number).padStart(3, '0');
        }
      }
    });

  fetch('php/fetch-queue.php')
    .then(res => res.json())
    .then(queue => {
      const queueList = document.getElementById('queueList');
      if (queueList) {
        queueList.innerHTML = '';
        queue.forEach(item => {
          const div = document.createElement('div');
          div.textContent = item.type + String(item.number).padStart(3, '0');
          queueList.appendChild(div);
        });
      }
    });
}

// 🔊 Sound when a ticket is called
const ringtone = document.getElementById("ringtone");

// Save last displayed
let lastDisplayed = {
  type: null,
  number: null
};

// Save last ticket per cashier (for footer)
let lastTickets = {
  S: null,
  CI: null,
  FC: null
};

function fetchCalledTickets() {
  fetch("php/queue-called.php")
    .then(res => res.json())
    .then(data => {
      updateTicket("S", "cashier1", data.S);
      updateTicket("CI", "cashier2", data.CI);
      updateTicket("FC", "cashier3", data.FC);

      const latest = data.latest;

      // Show only the latest called ticket
      if (
        latest &&
        latest.type &&
        latest.number &&
        (lastDisplayed.type !== latest.type || lastDisplayed.number !== latest.number)
      ) {
        const formatted = latest.type + String(latest.number).padStart(3, '0');
        document.getElementById('currentNumber').textContent = formatted;
        document.getElementById('currentCashier').textContent = mapCashierName(latest.type);

        // 🔊 Play sound when number changes
        const ringtone = document.getElementById("ringtone");
        if (ringtone) ringtone.play();

        // Save last displayed
        lastDisplayed = { type: latest.type, number: latest.number };
      }
    })
    .catch(err => console.error("Error fetching called tickets:", err));
}

function updateTicket(type, elementId, number) {
  const el = document.getElementById(elementId);
  if (!el) return;

  if (number != null && lastTickets[type] !== number) {
    const formatted = type + String(number).padStart(3, '0');
    el.textContent = formatted;
    el.classList.add("flash");
    ringtone?.play();

    setTimeout(() => el.classList.remove("flash"), 1200);
    lastTickets[type] = number;
  }
}

function mapCashierName(type) {
  switch (type) {
    case 'S': return 'Cashier 1';
    case 'CI': return 'Cashier 2';
    case 'FC': return 'Cashier 3';
    default: return '--';
  }
}

// Start loops
setInterval(fetchCalledTickets, 2000);
setInterval(updateClock, 1000);
setInterval(loadDisplayData, 2000);

// Initial load
fetchCalledTickets();
updateClock();
loadDisplayData();
