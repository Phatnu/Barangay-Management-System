const ctx = document.getElementById('myChart');

// Fetch monthly data from script.php
fetch("./Linegraph.php")
  .then((response) => response.json())
  .then((data) => {
    createChart(data, 'line'); // Changed to line chart
  })
  .catch((error) => {
    console.error("Fetch error:", error);
  });

// Format date to month name and year (e.g., "January 2025")
function formatMonthLabel(dateStr) {
  const date = new Date(dateStr);
  return date.toLocaleDateString('default', { month: 'long', year: 'numeric' });
}

// Create chart function
function createChart(chartData, type) {
  new Chart(ctx, {
    type: type,
    data: {
      labels: chartData.map(row => formatMonthLabel(row.month)),
      datasets: [{
        label: 'Residents every Month',
        data: chartData.map(row => row.total),
        borderColor: 'rgba(54, 162, 235, 1)',
        backgroundColor: 'rgba(54, 162, 235, 0.2)',
        borderWidth: 2,
        fill: false,
        tension: 0.4,
        pointRadius: 4
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          display: true,
          position: 'top'
        },
        tooltip: {
          mode: 'index',
          intersect: false
        }
      },
      scales: {
        x: {
          title: {
            display: true,
            text: 'Month'
          }
        },
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: 'Number of Residents'
          },
          ticks: {
            callback: function(value) {
              return value.toLocaleString(); // Removed dollar sign
            }
          }
        }
      }
    }
  });
}
