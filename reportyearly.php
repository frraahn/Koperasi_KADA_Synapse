<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>


<script>
let anggotaChart, pinjamanChart, kewanganChart, berhentiChart;


function updateReport() {
    // Get the selected value
    const selectedReport = document.getElementById("laporanSelect").value;


    // Hide all charts
    document.getElementById("anggotaChartContainer").style.display = "none";
    document.getElementById("pinjamanChartContainer").style.display = "none";
    document.getElementById("kewanganChartContainer").style.display = "none";
    document.getElementById("berhentiChartContainer").style.display = "none";

    // Show the selected chart
    if (selectedReport === "anggota") {
        document.getElementById("anggotaChartContainer").style.display = "block";
    } else if (selectedReport === "pinjaman") {
        document.getElementById("pinjamanChartContainer").style.display = "block";
        pinjamanChart.render();
    } else if (selectedReport === "pinjamanDiluluskan") {
        document.getElementById("kewanganChartContainer").style.display = "block";
        kewanganChart.render();
    } else if (selectedReport === "berhenti") {
        document.getElementById("berhentiChartContainer").style.display = "block";
        berhentiChart.render();
    }
}


window.onload = function () {
    // Anggota (Members) Chart
    anggotaChart = new CanvasJS.Chart("anggotaChartContainer", {
        theme: "light2",
        animationEnabled: true,
        title: {
            text: "Statistik Permohonan Anggota KADA",
            fontSize: 16
        },
        axisX: {
            title: "Tahun",
            interval: 1,
        },
        axisY: {
            title: "Jumlah",
            includeZero: true,
        },
        legend: {
            cursor: "pointer",
            itemclick: function (e) {
                if (typeof (e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                    e.dataSeries.visible = false;
                } else {
                    e.dataSeries.visible = true;
                }
                e.chart.render();
            }
        },
        toolTip: {
            shared: false
        },
        data: [
            {
                type: "spline",
                name: "Jumlah Anggota",
                showInLegend: true,
                markerSize: 7,
                color: "#9b59b6",
                toolTipContent: "Tahun: {x}<br>Jumlah Anggota: {y}",
                dataPoints: <?php echo json_encode($memberCount, JSON_NUMERIC_CHECK); ?>
            },
            {
                type: "spline",
                name: "Jumlah Permohonan Anggota",
                showInLegend: true,
                markerSize: 7,
                color: "#4287f5",
                toolTipContent: "Tahun: {x}<br>Jumlah Permohonan Anggota: {y}",
                dataPoints: <?php echo json_encode($memberApplications, JSON_NUMERIC_CHECK); ?>
            },
            {
                type: "spline",
                name: "Permohonan Anggota Diluluskan",
                showInLegend: true,
                markerSize: 7,
                color: "#2ecc71",
                toolTipContent: "Tahun: {x}<br>Permohonan Anggota Diluluskan: {y}",
                dataPoints: <?php echo json_encode($approvedMembers, JSON_NUMERIC_CHECK); ?>
            },
            {
                type: "spline",
                name: "Permohonan Anggota Ditolak",
                showInLegend: true,
                markerSize: 7,
                color: "#e74c3c",
                toolTipContent: "Tahun: {x}<br>Permohonan Anggota Ditolak: {y}",
                dataPoints: <?php echo json_encode($rejectedMembers, JSON_NUMERIC_CHECK); ?>
            }
        ]
    });


    // Pinjaman (Loans) Chart
    pinjamanChart = new CanvasJS.Chart("pinjamanChartContainer", {
        theme: "light2",
        animationEnabled: true,
        title: {
            text: "Statistik Permohonan Pinjaman KADA",
            fontSize: 16
        },
        axisX: {
            title: "Tahun",
            interval: 1,
        },
        axisY: {
            title: "Jumlah",
            includeZero: true,
        },
        legend: {
            cursor: "pointer",
            itemclick: function (e) {
                if (typeof (e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                    e.dataSeries.visible = false;
                } else {
                    e.dataSeries.visible = true;
                }
                e.chart.render();
            }
        },
        toolTip: {
            shared: false
        },
        data: [
            {
                type: "spline",
                name: "Jumlah Permohonan Pinjaman",
                showInLegend: true,
                markerSize: 7,
                color: "#9b59b6",
                toolTipContent: "Tahun: {x}<br>Jumlah Permohonan Pinjaman: {y}",
                dataPoints: <?php echo json_encode($loanApplications, JSON_NUMERIC_CHECK); ?>
            },
            {
                type: "spline",
                name: "Permohonan Pinjaman Diluluskan",
                showInLegend: true,
                markerSize: 7,
                color: "#27ae60",
                toolTipContent: "Tahun: {x}<br>Permohonan Pinjaman Diluluskan: {y}",
                dataPoints: <?php echo json_encode($approvedLoans, JSON_NUMERIC_CHECK); ?>
            },
            {
                type: "spline",
                name: "Permohonan Pinjaman Ditolak",
                showInLegend: true,
                markerSize: 7,
                color: "#c0392b",
                toolTipContent: "Tahun: {x}<br>Permohonan Pinjaman Ditolak: {y}",
                dataPoints: <?php echo json_encode($rejectedLoans, JSON_NUMERIC_CHECK); ?>
            }
        ]
    });


    // Kewangan Chart
    kewanganChart = new CanvasJS.Chart("kewanganChartContainer", {
        theme: "light2",
        animationEnabled: true,
        title: {
            text: "Statistik Kewangan KADA (RM)",
            fontSize: 16
        },
        axisX: {
            title: "Tahun",
            interval: 1,
        },
        axisY: {
            title: "Jumlah (RM)",
            includeZero: true,
        },
        legend: {
            cursor: "pointer",
            itemclick: function (e) {
                if (typeof (e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                    e.dataSeries.visible = false;
                } else {
                    e.dataSeries.visible = true;
                }
                e.chart.render();
            }
        },
        toolTip: {
            shared: false,
            content: "Tahun: {x}<br>Jumlah Diluluskan: RM {y}"
        },
        data: [
            {
                type: "spline",
                name: "Pinjaman Diluluskan",
                showInLegend: true,
                markerSize: 7,
                color: "#27ae60",
                dataPoints: <?php echo json_encode($amountLoans, JSON_NUMERIC_CHECK); ?>
            }
        ]
    });

    berhentiChart = new CanvasJS.Chart("berhentiChartContainer", {
        theme: "light2",
        animationEnabled: true,
        title: {
            text: "Statistik Berhenti Keanggotaan KADA",
            fontSize: 16
        },
        axisX: {
            title: "Tahun",
            interval: 1,
        },
        axisY: {
            title: "Jumlah",
            includeZero: true,
        },
        legend: {
            cursor: "pointer",
            itemclick: function (e) {
                if (typeof (e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                    e.dataSeries.visible = false;
                } else {
                    e.dataSeries.visible = true;
                }
                e.chart.render();
            }
        },
        toolTip: {
            shared: false
        },
        data: [
            {
                type: "spline",
                name: "Jumlah Berhenti",
                showInLegend: true,
                markerSize: 7,
                color: "#9b59b6",
                toolTipContent: "Tahun: {x}<br>Jumlah Berhenti: {y}",
                dataPoints: <?php echo json_encode($terminateCount, JSON_NUMERIC_CHECK); ?>
            },
            {
                type: "spline",
                name: "Jumlah Permohonan Berhenti",
                showInLegend: true,
                markerSize: 7,
                color: "#4287f5",
                toolTipContent: "Tahun: {x}<br>Jumlah Permohonan Berhenti: {y}",
                dataPoints: <?php echo json_encode($terminateApplications, JSON_NUMERIC_CHECK); ?>
            },
            {
                type: "spline",
                name: "Permohonan Berhenti Diluluskan",
                showInLegend: true,
                markerSize: 7,
                color: "#2ecc71",
                toolTipContent: "Tahun: {x}<br>Permohonan Berhenti Diluluskan: {y}",
                dataPoints: <?php echo json_encode($approvedTerminate, JSON_NUMERIC_CHECK); ?>
            },
            {
                type: "spline",
                name: "Permohonan Berhenti Ditolak",
                showInLegend: true,
                markerSize: 7,
                color: "#e74c3c",
                toolTipContent: "Tahun: {x}<br>Permohonan Berhenti Ditolak: {y}",
                dataPoints: <?php echo json_encode($rejectedTerminate, JSON_NUMERIC_CHECK); ?>
            }
        ]
    });


    // Render both charts
    anggotaChart.render();
}
</script>