const blotterCtx = document.getElementById('genderChart');

fetch("Piegraph.php")
  .then(res => res.json())
  .then(data => {
    console.log("Gender Data:", data);
    createGenderChart(data);
  })
  .catch(err => console.error("Fetch error:", err));

function createGenderChart(data) {
  new Chart(blotterCtx, {
    type: 'pie',
    data: {
      labels: data.map(row => row.gender),  // No more Invalid Date
      datasets: [{
        label: 'Residents by Gender',
        data: data.map(row => row.total),
        backgroundColor: [
          'rgba(54, 162, 235, 0.7)',   // Male
          'rgba(255, 99, 132, 0.7)'    // Female
        ]
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'top' },
        title: {
          display: true,
          text: 'Resident Gender Distribution'
        }
      }
    }
  });
}

