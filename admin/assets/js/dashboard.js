document.addEventListener("DOMContentLoaded", () => {
  fetch("api/admin_dashboard_data.php")
    .then(res => res.json())
    .then(data => {
      document.getElementById("user-count").textContent = data.totals.users;
      document.getElementById("incident-count").textContent = data.totals.incidents;
      document.getElementById("assigned-count").textContent = data.totals.assigned;

      const userRoleChart = new Chart(document.getElementById("userRoleChart"), {
        type: "pie",
        data: {
          labels: Object.keys(data.userCounts),
          datasets: [{
            label: "Users",
            data: Object.values(data.userCounts),
            backgroundColor: ["#3b82f6", "#10b981", "#f59e0b"]
          }]
        }
      });

      const incidentStatusChart = new Chart(document.getElementById("incidentStatusChart"), {
        type: "doughnut",
        data: {
          labels: Object.keys(data.statusCounts),
          datasets: [{
            label: "Incidents",
            data: Object.values(data.statusCounts),
            backgroundColor: ["#22c55e", "#facc15", "#ef4444", "#6b7280"]
          }]
        }
      });
    });
});
