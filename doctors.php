
<?php
session_start();
require "config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch departements for dropdown
$departements = mysqli_query($conn, "SELECT * FROM departements");

// Handle Create / Update
if(isset($_POST['save'])) {
    $name = $_POST['name'];
    $department_id = $_POST['department_id'];
    
    if(!empty($_POST['id'])) {
        $id = $_POST['id'];
        mysqli_query($conn, "UPDATE doctors SET name='$name', department_id='$department_id' WHERE id=$id");
    } else {
        mysqli_query($conn, "INSERT INTO doctors(name, department_id) VALUES('$name','$department_id')");
    }
}

// Handle Delete
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM doctors WHERE id=$id");
}

// Fetch doctors with departement names
$doctors = mysqli_query($conn, "
    SELECT doctors.id, doctors.name, departements.name AS dept_name, doctors.department_id
    FROM doctors
    LEFT JOIN departements ON doctors.department_id = departements.id
");

// For Edit
$editDoctor = null;
if(isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $res = mysqli_query($conn, "SELECT * FROM doctors WHERE id=$id");
    $editDoctor = mysqli_fetch_assoc($res);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Doctors | Unity Care</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<div class="flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-blue-900 text-white min-h-screen p-6">
        <h2 class="text-3xl font-bold  px-2 py-1">Unity Care</h2>
        <nav class="space-y-4">
            <a href="dashboard.php" class="block px-4 py-2 rounded hover:bg-blue-700">Dashboard</a>
            <a href="patients.php" class="block px-4 py-2 rounded hover:bg-blue-700">Patients</a>
            <a href="doctors.php" class="block px-4 py-2 rounded hover:bg-blue-700">Doctors</a>
            <a href="departements.php" class="block px-4 py-2 rounded hover:bg-blue-700">Departements</a>
        </nav>
    </aside>

    <!-- Main content -->
    <main class="flex-1 p-6">
        <h1 class="text-2xl font-bold mb-4">Doctors</h1>

        <!-- Add / Edit Form -->
        <div class="bg-white p-6 rounded shadow mb-6">
            <form method="POST">
                <input type="hidden" name="id" value="<?= $editDoctor['id'] ?? '' ?>">
                <div class="mb-2">
                    <input type="text" name="name" placeholder="Doctor Name" class="border p-2 w-full" value="<?= $editDoctor['name'] ?? '' ?>" required>
                </div>
                <div class="mb-2">
                    <select name="department_id" class="border p-2 w-full" required>
                        <option value="">Select Departement</option>
                        <?php while($dept = mysqli_fetch_assoc($departements)) : ?>
                            <option value="<?= $dept['id'] ?>" <?= (isset($editDoctor) && $editDoctor['department_id']==$dept['id']) ? 'selected' : '' ?>>
                                <?= $dept['name'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" name="save" class="bg-green-600 text-white px-4 py-2 rounded-full">
                    <?= isset($editDoctor) ? 'Update' : 'Add' ?>
                </button>
            </form>
        </div>

        <!-- Table -->
        <table class="w-full bg-white rounded shadow">
            <thead class="bg-gray-200">
                <tr>
                    <th class="p-2 border">ID</th>
                    <th class="p-2 border">Name</th>
                    <th class="p-2 border">Departement</th>
                    <th class="p-2 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($doctors)) : ?>
                    <tr>
                        <td class="p-2 border"><?= $row['id'] ?></td>
                        <td class="p-2 border"><?= $row['name'] ?></td>
                        <td class="p-2 border"><?= $row['dept_name'] ?? '-' ?></td>
                        <td class="p-2 border space-x-2">
                            <a href="?edit=<?= $row['id'] ?>" class="bg-orange-500 text-white px-2 py-1 rounded-full">Edit</a>
                            <a href="?delete=<?= $row['id'] ?>" class="bg-pink-500 text-white px-2 py-1 rounded-full " onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
</div>
</body>
</html>
