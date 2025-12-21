
<?php
session_start();
require "config/database.php";

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch counts for stats cards
$patients = mysqli_query($conn, "SELECT * FROM patients");
$doctors = mysqli_query($conn, "SELECT * FROM doctors");
$departements = mysqli_query($conn, "SELECT * FROM departements");

// Fetch doctors per department for pie chart
$doctorCountsQuery = mysqli_query($conn, "
    SELECT departements.name, COUNT(doctors.id) as count
    FROM departements
    LEFT JOIN doctors ON doctors.department_id = departements.id
    GROUP BY departements.id
");

$departmentLabels = [];
$doctorCounts = [];

while($row = mysqli_fetch_assoc($doctorCountsQuery)) {
    $departmentLabels[] = $row['name'];
    $doctorCounts[] = $row['count'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard | Unity Care</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<!-- Header -->
<div class="bg-purple-950 text-white p-4 flex justify-between items-center">
    <h1 class="text-xl font-bold ">Unity Care Clinic</h1>
    <div class="flex items-center space-x-4">
        <div class="bg-white text-blue-800 rounded-full w-10 h-10 flex items-center justify-center font-bold">
            <?= strtoupper($_SESSION['user_name'][0]) ?>
        </div>
        <a href="logout.php" class="bg-red-600 px-4 py-1 rounded hover:bg-red-700">Logout</a>
    </div>
</div>

<!-- Layout wrapper -->
<div class="flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-blue-900 text-white min-h-screen p-6">

        <nav class="space-y-4">
            <a href="dashboard.php" class="block px-4 py-2 rounded hover:bg-blue-700">Dashboard</a>
            <a href="patients.php" class="block px-4 py-2 rounded hover:bg-blue-700">Patients</a>
            <a href="doctors.php" class="block px-4 py-2 rounded hover:bg-blue-700">Doctors</a>
            <a href="departements.php" class="block px-4 py-2 rounded hover:bg-blue-700">Departements</a>
        </nav>
    </aside>

    <!-- Main content -->
    <main class="flex-1 p-6">

       <!-- Stats cards -->
<div class="grid grid-cols-3 gap-6 mb-6">
    <!-- Patients Card -->
    <div class="bg-blue-100 p-6 rounded shadow text-center">
        <p class="text-blue-700 font-bold text-xl">Patients</p>
        <p class="text-3xl font-bold text-blue-800"><?= mysqli_num_rows($patients) ?></p>
    </div>

    <!-- Doctors Card -->
    <div class="bg-green-100 p-6 rounded shadow text-center">
        <p class="text-green-700 font-bold text-xl">Doctors</p>
        <p class="text-3xl font-bold text-green-800"><?= mysqli_num_rows($doctors) ?></p>
    </div>

    <!-- Departements Card -->
    <div class="bg-yellow-100 p-6 rounded shadow text-center">
        <p class="text-yellow-700 font-bold text-xl">Departements</p>
        <p class="text-3xl font-bold text-yellow-800"><?= mysqli_num_rows($departements) ?></p>
    </div>
</div>


        <!-- Charts -->
        <div class="grid grid-cols-2 gap-6">

            <!-- Bar Chart: Patients / Doctors / Departements -->
            <div class="bg-white p-6 rounded shadow">
                <h2 class="text-xl font-bold mb-4">Clinic Overview</h2>
                <canvas id="overviewChart" height="150"></canvas>
            </div>

            <!-- Pie Chart: Doctors per Department -->
            <div class="bg-white p-6 rounded shadow">
                <h2 class="text-xl font-bold mb-4">Doctors per Department</h2>
                <canvas id="doctorsDeptChart" height="150"></canvas>
            </div>

        </div>

    </main>
</div>

<!-- Chart.js Scripts -->
<script>
    // Bar chart data
    const overviewCtx = document.getElementById('overviewChart').getContext('2d');
    new Chart(overviewCtx, {
        type: 'bar',
        data: {
            labels: ['Patients', 'Doctors', 'Departements'],
            datasets: [{
                label: 'Count',
                data: [<?= mysqli_num_rows($patients) ?>, <?= mysqli_num_rows($doctors) ?>, <?= mysqli_num_rows($departements) ?>],
                backgroundColor: ['rgba(59,130,246,0.7)','rgba(16,185,129,0.7)','rgba(234,179,8,0.7)'],
                borderColor: ['rgba(59,130,246,1)','rgba(16,185,129,1)','rgba(234,179,8,1)'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, stepSize: 1 } }
        }
    });

    // Pie chart data
    const deptCtx = document.getElementById('doctorsDeptChart').getContext('2d');
    new Chart(deptCtx, {
        type: 'pie',
        data: {
            labels: <?= json_encode($departmentLabels) ?>,
            datasets: [{
                data: <?= json_encode($doctorCounts) ?>,
                backgroundColor: ['#3B82F6','#10B981','#FBBF24','#EF4444','#8B5CF6','#F472B6'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'right' } }
        }
    });
</script>

</body>
</html>
