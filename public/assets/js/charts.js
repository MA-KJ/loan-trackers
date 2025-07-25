// JS for Chart.js
// Include this script in any page where charts are rendered
// Example assumes chart canvas elements exist in the page with IDs: interestChart, loanStatusChart

document.addEventListener("DOMContentLoaded", () => {
  const ctx1 = document.getElementById("interestChart");
  const ctx2 = document.getElementById("loanStatusChart");

  if (ctx1) {
    new Chart(ctx1, {
      type: "bar",
      data: {
        labels: interestData.months,
        datasets: [
          {
            label: "Interest Earned (K)",
            data: interestData.values,
            borderWidth: 1,
          },
        ],
      },
      options: {
        responsive: true,
      },
    });
  }

  if (ctx2) {
    new Chart(ctx2, {
      type: "pie",
      data: {
        labels: ["Paid", "Unpaid"],
        datasets: [
          {
            label: "Loan Status",
            data: [loanStatus.paid, loanStatus.unpaid],
          },
        ],
      },
      options: {
        responsive: true,
      },
    });
  }
});
