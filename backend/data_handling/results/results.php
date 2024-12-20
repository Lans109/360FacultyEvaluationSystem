<?php
// Include configuration and database connection
include_once "../../../config.php";
include ROOT_PATH . '/backend/db/dbconnect.php';

// Authentication check
include '../authentication.php';

// Fetch departments for filtering
$departmentQuery = "SELECT department_id, department_name FROM departments";
$departmentResult = mysqli_query($con, $departmentQuery);

// Set filters
$department_filter = isset($_GET['department_filter']) ? $_GET['department_filter'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Fetch filtered faculty members with department names
$facultyQuery = "
    SELECT 
        f.faculty_id, 
        CONCAT(f.first_name, ' ', f.last_name) AS faculty_name, 
        d.department_name, 
        f.phone_number, 
        d.department_code, 
        f.email,
        f.profile_image
    FROM faculty f
    LEFT JOIN departments d ON f.department_id = d.department_id";

if ($search) {
    $facultyQuery .= " WHERE (
    f.faculty_id LIKE '%$search%' OR 
    CONCAT(f.first_name, ' ', f.last_name) LIKE '%$search%' OR
    f.phone_number LIKE '%$search%' OR
    f.email LIKE '%$search%'
    )";
}

// Add department filter condition if a department is selected
if ($department_filter) {
    $facultyQuery .= $search ? " AND d.department_id = '$department_filter'" : " WHERE d.department_id = '$department_filter'";
}

$facultyResult = mysqli_query($con, $facultyQuery);

if (!$facultyResult) {
    die("Query failed: " . mysqli_error($con));
}

$num_rows = mysqli_num_rows($facultyResult);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include '../../../frontend/layout/navbar.php'; ?>
    <link rel='stylesheet' href='../../../frontend/templates/admin-style.css'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Faculty Results</title>
</head>

<body>
    <?php include ROOT_PATH . '/frontend/layout/sidebar.php'; ?>
    <div id="loader" class="loader"></div>
    <main>
        <div class="upperMain">
            <div><h1>Faculty Results</h1></div>
        </div>
        <div class="content">
            <div class="upperContent">
            <div>
                    <p>Showing <?= $num_rows ?> <?= $num_rows == 1 ? 'Faculty Member' : 'Faculty Members' ?></p>
                </div>
                    <!-- Filter and Search Form -->
                    <div class="search-filter">
                    <form method="GET" action="">
                        <div class="form-group">
                            
                        <div class="search-container">
                            <input type="text" placeholder="Search..." id="search" name="search" class="search-input">
                            <button type="submit" class="search-button">
                                <i class="fa fa-search"></i>  <!-- Magnifying Glass Icon -->
                            </button>
                        </div>
                        <div class="select-container">
                            <div class="select-wrapper">
                                <select id="department_filter" name="department_filter" class="custom-select">
                                    <option value="" selected>All Departments</option>
                                    <?php
                                    // Fetch all departments to populate the filter dropdown
                                    $departments_query = "SELECT department_id, department_code FROM departments";
                                    $departments_result = mysqli_query($con, $departments_query);

                                    // Fetch and display department options
                                    while ($department = mysqli_fetch_assoc($departments_result)) {
                                        $selected = (isset($_GET['department_filter']) && $_GET['department_filter'] == $department['department_id']) ? 'selected' : '';
                                        echo "<option value='" . $department['department_id'] . "' . $selected>" . $department['department_code'] . "</option>";
                                    }
                                    ?>
                                </select>
                                <i class="fa fa-chevron-down select-icon"></i>  <!-- Icon for dropdown -->
                            </div>
                        </div>
                            <button type="submit" class="fitler-btn"><i class="fa fa-filter" aria-hidden="true"></i> Filter</button>
                            <a href="results.php" class="fitler-btn"><i class="fa fa-eraser"></i> Clear</a>
                        </div>
                    </form>
                </div>
            </div>

                    <!-- Faculty Table -->
                     
                    <div class="table">
                    <table>
                        <thead>
                            <tr>
                                <th width="150px">Faculty ID</th>
                                <th width="50px">P.P</th>
                                <th width="400px">Faculty INFO</th>
                                <th width="260px">CONTACT No.</th>
                                <th>Department</th>
                                <th width="150px">Results</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($facultyResult) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($facultyResult)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['faculty_id']); ?></td>
                                        <td><img class="profile-icon" src="../../../<?= $row['profile_image'] ?>" alt="Profile Image"></td>
                                        <td>
                                            <?php echo htmlspecialchars($row['faculty_name']); ?><br>
                                            <?php echo htmlspecialchars($row['email']); ?>
                                    
                                        </td>
                                        <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                                        <td><?php echo htmlspecialchars($row['department_name']); ?></td>
                                        <td>
                                            <!--test-->
                                            <a href="faculty_summary.php?facultyId=<?php echo $row['faculty_id']; ?>" class="view-btn">
                                                View Results
                                            </a>
                                        </td>   
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">No faculty members found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                </div>
            </div>
    </main>
</body>
<!-- for sidebar button -->
<script type="text/javascript" src="../../../frontend/layout/app.js" defer></script>
<script>
    window.addEventListener('load', function () {
    // Hide the loader after the page has fully loaded
    document.getElementById('loader').style.display = 'none';
});
</script>
</html>
