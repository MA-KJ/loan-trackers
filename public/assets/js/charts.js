document.addEventListener("DOMContentLoaded", function () {
  if (typeof chartData !== "undefined") {
    const incomeBar = document.getElementById("incomeBarChart");
    const pieChart = document.getElementById("loanPieChart");

    if (incomeBar) {
      new Chart(incomeBar, {
        type: "bar",
        data: {
          labels: chartData.months,
          datasets: [
            {
              label: "Monthly Interest Earned",
              data: chartData.interest,
              backgroundColor: "#42a5f5",
            },
          ],
        },
      });
    }

    if (pieChart) {
      new Chart(pieChart, {
        type: "pie",
        data: {
          labels: ["Paid", "Unpaid"],
          datasets: [
            {
              data: chartData.status,
              backgroundColor: ["#66bb6a", "#ef5350"],
            },
          ],
        },
      });
    }
  }
});
