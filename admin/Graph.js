document.addEventListener('DOMContentLoaded', function () {
  const canvas = document.getElementById('blotterChart');

  if (!canvas) {
    console.error('blotterChart canvas not found');
    return;
  }

  fetch("Graph.php")
    .then(response => response.json())
    .then(data => {
      createBlotterChart(canvas, data);
    })
    .catch(error => console.error("Error fetching data:", error));
});

function createBlotterChart(canvas, data) {
  const ctx = canvas.getContext("2d");

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: data.map(row => formatMonthLabel(row.month)),
      datasets: [{
        label: 'Blotter Reports every Month',
        data: data.map(row => row.total),
        backgroundColor: 'rgba(54, 162, 235, 0.7)',
        borderColor: 'rgba(54, 162, 235, 0.7)',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'top' },
        title: { display: true, text: 'Blotter Report every Month' }
      },
      scales: {
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: 'Total Blotters'
          }
        }
      }
    }
  });
}

function formatMonthLabel(dateStr) {
  const date = new Date(dateStr);
  if (isNaN(date)) return "Invalid Date";
  return date.toLocaleDateString('default', { month: 'long', year: 'numeric' });
}
