function pnFinanceManagerInitCharts() {
  var canvases = document.querySelectorAll('.pn-personal-finance-manager-statistics-chart[data-chart]');

  canvases.forEach(function(canvas) {
    if (typeof Chart === 'undefined') {
      return;
    }

    // Destroy existing chart instance if any
    var existingChart = Chart.getChart(canvas);
    if (existingChart) {
      existingChart.destroy();
    }

    var chartDataAttr = canvas.getAttribute('data-chart');
    if (!chartDataAttr) {
      return;
    }

    try {
      var chartData = JSON.parse(chartDataAttr);
    } catch (e) {
      return;
    }

    if (!chartData.labels || !chartData.values || chartData.values.length === 0) {
      return;
    }

    var currencySymbol = canvas.getAttribute('data-currency-symbol') || '';
    var total = chartData.values.reduce(function(sum, val) { return sum + val; }, 0);

    new Chart(canvas, {
      type: 'doughnut',
      data: {
        labels: chartData.labels,
        datasets: [{
          data: chartData.values,
          backgroundColor: chartData.colors,
          borderWidth: 2,
          borderColor: '#ffffff'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                var label = context.label || '';
                var value = context.parsed;
                var percentage = ((value / total) * 100).toFixed(1);
                return label + ': ' + currencySymbol + value.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' (' + percentage + '%)';
              }
            }
          }
        },
        cutout: '60%'
      },
      plugins: [{
        id: 'centerText',
        afterDraw: function(chart) {
          var ctx = chart.ctx;
          var chartArea = chart.chartArea;
          var centerX = (chartArea.left + chartArea.right) / 2;
          var centerY = (chartArea.top + chartArea.bottom) / 2;

          ctx.save();
          ctx.font = 'bold 16px sans-serif';
          ctx.fillStyle = '#212529';
          ctx.textAlign = 'center';
          ctx.textBaseline = 'middle';
          ctx.fillText(currencySymbol + total.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0}), centerX, centerY);
          ctx.restore();
        }
      }]
    });
  });
}

document.addEventListener('DOMContentLoaded', function() {
  // Wait for Chart.js to be available
  function waitForChartJs() {
    if (typeof Chart !== 'undefined') {
      pnFinanceManagerInitCharts();
    } else {
      setTimeout(waitForChartJs, 100);
    }
  }
  waitForChartJs();
});
