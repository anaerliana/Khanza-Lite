
    var ctx2 = document.getElementById("medical-treatment-chart");
    var myChart2 = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: ["{?= implode('","', $stats.RanapTahunChart.labels) ?}"],
            datasets: [
            {
                label: "Laki - Laki",
                borderColor: "#34316E",
                borderWidth: "2",
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                pointHighlightStroke: "#1e1e2d",
                pointRadius: 0,
                data: [{?= implode(',', $stats.RanapTahunChart.visits) ?}],
            },
            {
                label: "Perempuan",
                borderColor: "#34316E",
                borderWidth: "2",
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                pointHighlightStroke: "#1e1e2d",
                pointRadius: 0,
                data: [{?= implode(',', $stats.RujukTahunChart.visits) ?}],
            }
            ]
        },
        options: {
            legend: {
                display: true,
            },
            tooltips: {
                mode: 'index',
                intersect: false
            },
            hover: {
                mode: 'nearest',
                intersect: true
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });

//Heart surgery chart
var ctx1 = document.getElementById("heart-surgery-chart");
var myChart1 = new Chart(ctx1, {
    type: 'bar',
    data: {
        labels: ["{?= implode('","', $stats.KunjunganTahunChart.labels) ?}"],
        datasets: [
        {
            label: "Laki - Laki",
            borderColor: "#34316E",
            borderWidth: "2",
            backgroundColor: 'rgba(255, 159, 64, 0.2)',
            pointHighlightStroke: "#1e1e2d",
            pointRadius: 0,
            data: [{?= implode(',', $stats.KunjunganTahunChart.visits) ?}],
        },
        {
            label: "Perempuan",
            borderColor: "#34316E",
            borderWidth: "2",
            backgroundColor: 'rgba(153, 102, 255, 0.2)',
            pointHighlightStroke: "#1e1e2d",
            pointRadius: 0,
            data: [{?= implode(',', $stats.KunjunganChart.visits) ?}],
        }
        ]
    },
    options: {
        legend: {
            display: true,
        },
        tooltips: {
            mode: 'index',
            intersect: false
        },
        hover: {
            mode: 'nearest',
            intersect: true
        },
        responsive: true,
        maintainAspectRatio: false
    }
});
