
    document.getElementById('filterBtn').addEventListener('click', fetchReports);

function fetchReports() {
  const branch = document.getElementById('branchFilter').value;
  const category = document.getElementById('categoryFilter').value;
  const fromDate = document.getElementById('fromDate').value;
  const toDate = document.getElementById('toDate').value;

  fetch(`get_reports.php?branch=${branch}&category=${category}&fromDate=${fromDate}&toDate=${toDate}`)
    .then(res => res.json())
    .then(data => {
      updateTable(data.tableData);
      updateCharts(data.chartData);
    })
    .catch(err => console.error(err));
}

function updateTable(rows) {
  const tbody = document.querySelector('#reportTable tbody');
  tbody.innerHTML = '';
  rows.forEach(row => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td class="px-4 py-2">${row.title}</td>
      <td>${row.branch}</td>
      <td>${row.category}</td>
      <td>${row.report_date}</td>
      <td>${row.fixed_date || '-'}</td>
      <td>${row.days_to_fix || '-'}</td>
      <td>${row.assigned_staff || '-'}</td>
    `;
    tbody.appendChild(tr);
  });
}

function updateCharts(data) {
  if (window.incidentChart) window.incidentChart.destroy();
  if (window.staffChart) window.staffChart.destroy();

  const ctx1 = document.getElementById('incidentCountChart').getContext('2d');
  window.incidentChart = new Chart(ctx1, {
    type: 'bar',
    data: {
      labels: data.incident.labels,
      datasets: [{
        label: 'Incidents',
        data: data.incident.counts,
        backgroundColor: '#3b82f6'
      }]
    }
  });

  const ctx2 = document.getElementById('staffPerformanceChart').getContext('2d');
  window.staffChart = new Chart(ctx2, {
    type: 'doughnut',
    data: {
      labels: data.staff.labels,
      datasets: [{
        data: data.staff.counts,
        backgroundColor: ['#10b981', '#f87171', '#fbbf24']
      }]
    }
  });
}

function exportCSV() {
  const table = document.getElementById('reportTable');
  let csv = [];
  for (let row of table.rows) {
    let cols = Array.from(row.cells).map(td => td.innerText);
    csv.push(cols.join(","));
  }
  const blob = new Blob([csv.join("\n")], { type: 'text/csv' });
  saveAs(blob, 'incident_report.csv');
}

function exportPDF() {
  const doc = new jspdf.jsPDF();
  doc.autoTable({ html: '#reportTable' });
  doc.save('incident_report.pdf');
}
