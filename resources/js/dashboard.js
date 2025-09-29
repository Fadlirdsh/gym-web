import ApexCharts from "apexcharts";

// Ambil data dari window (hasil inject dari Blade)
const { reservationsData, trainerData, pelangganData } = window.dashboardData || {};

// --- Bar Chart: Reservasi Per Bulan
const optionsBar = {
    series: [{ name: "Reservasi", data: reservationsData }],
    chart: { height: 350, type: "bar" },
    plotOptions: { bar: { borderRadius: 10, dataLabels: { position: "top" } } },
    dataLabels: {
        enabled: true,
        formatter: val => val,
        offsetY: -20,
        style: { fontSize: "12px", colors: ["#304758"] }
    },
    xaxis: {
        categories: ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"],
        position: "top",
        axisBorder: { show: false },
        axisTicks: { show: false }
    },
    yaxis: { labels: { show: false } },
    title: {
        text: "Reservasi Per Bulan",
        floating: true,
        offsetY: 330,
        align: "center",
        style: { color: "#444" }
    }
};
new ApexCharts(document.querySelector("#reservationsBar"), optionsBar).render();

// --- Donut Chart: Trainer vs Pelanggan
const optionsDonut = {
    series: [
        (trainerData || []).reduce((a, b) => a + b, 0),
        (pelangganData || []).reduce((a, b) => a + b, 0)
    ],
    chart: { type: "donut", width: 380 },
    labels: ["Trainer", "Pelanggan"],
    fill: { type: "gradient" },
    legend: { position: "bottom" },
    responsive: [{ breakpoint: 480, options: { chart: { width: 250 } } }]
};
new ApexCharts(document.querySelector("#usersDonut"), optionsDonut).render();
