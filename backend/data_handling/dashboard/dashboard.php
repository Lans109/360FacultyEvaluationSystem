<?php
include_once "../../../config.php";

include ROOT_PATH . '/backend/db/dbconnect.php';

// Fetch current evaluation status for the current date
$current_date = date('Y-m-d'); // Current date in YYYY-MM-DD format
$current_evaluation_query = "
    SELECT academic_year, semester, status 
    FROM evaluation_periods 
    WHERE status = 'active' 
    AND start_date <= '$current_date' 
    AND end_date >= '$current_date' 
    LIMIT 1"; // Fetch the active evaluation for today
$current_evaluation_result = mysqli_query($con, $current_evaluation_query);

// Check if any evaluation exists for the current date
if (mysqli_num_rows($current_evaluation_result) > 0) {
    $current_evaluation_data = mysqli_fetch_assoc($current_evaluation_result);
} else {
    $current_evaluation_data = null; // No active evaluation
}

// Fetch totals
$total_programs_query = "SELECT COUNT(*) AS total_programs FROM programs"; // Query for total programs
$total_courses_query = "SELECT COUNT(*) AS total_courses FROM courses";
$total_sections_query = "SELECT COUNT(*) AS total_sections FROM course_sections";
$total_students_query = "SELECT COUNT(*) AS total_students FROM students";
$total_faculty_query = "SELECT COUNT(*) AS total_faculty FROM faculty";

$total_programs_result = mysqli_query($con, $total_programs_query);
$total_courses_result = mysqli_query($con, $total_courses_query);
$total_sections_result = mysqli_query($con, $total_sections_query);
$total_students_result = mysqli_query($con, $total_students_query);
$total_faculty_result = mysqli_query($con, $total_faculty_query);

$total_programs = mysqli_fetch_assoc($total_programs_result)['total_programs'];
$total_courses = mysqli_fetch_assoc($total_courses_result)['total_courses'];
$total_sections = mysqli_fetch_assoc($total_sections_result)['total_sections'];
$total_students = mysqli_fetch_assoc($total_students_result)['total_students'];
$total_faculty = mysqli_fetch_assoc($total_faculty_result)['total_faculty'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' href='../../../frontend/templates/admin-style.css'>
    <title>Dashboard</title>
    <?php include '../../../frontend/layout/navbar.php'; ?>

</head>

<body>
    <?php include '../../../frontend/layout/sidebar.php'; ?>
    <main>

        <div class="upperMain">
            <h1>Dashboard</h1>
        </div>
        <div class="content">
            <h2> Current Evaluation </h2>
            <div class="banner">
                <?php if ($current_evaluation_data): ?>
                    <h3 class="card-title">Academic Year:
                        <?php echo $current_evaluation_data['academic_year']; ?>
                    </h3>
                    <p class="card-text">Semester: <?php echo $current_evaluation_data['semester']; ?></p>
                    <p class="card-text">Status: <?php echo $current_evaluation_data['status']; ?></p>
                <?php else: ?>
                    <div class="card-header">No Active Evaluation</div>
                <?php endif; ?>
            </div>
            <div class="reminder">
                <?php if ($current_evaluation_data): ?>
                    <p>Reminder: It's time to complete your faculty evaluation! Please take a few moments to provide
                    your feedback.</p>
                <?php else: ?>   
                    <p class="card-text">There is no active evaluation for today.</p>
                <?php endif; ?>
            </div>
            <div class="dashboard-cards">
                <div class="card">
                    <div class="card-info">
                        <h3><?php echo $total_programs; ?></h3>
                        <p> Total Programs</p>
                    </div>
                    <div class="card-icon">
                        <img src="../../../frontend/assets/programs.jpg">
                    </div>
                </div>
                <div class="card">
                    <div class="card-info">
                        <h3><?php echo $total_courses; ?></h3>
                        <p> Total Course</p>
                    </div>
                    <div class="card-icon">
                        <img src="../../../frontend/assets/courses.jpg">
                    </div>
                </div>
                <div class="card">
                    <div class="card-info">
                        <h3><?php echo $total_students; ?></h3>
                        <p> Total Students</p>
                    </div>
                    <div class="card-icon">
                        <img src="../../../frontend/assets/students.jpg">
                    </div>
                </div>
                <div class="card">
                    <div class="card-info">
                        <h3><?php echo $total_sections; ?></h3>
                        <p> Total Sections</p>
                    </div>
                    <div class="card-icon">
                        <img src="../../../frontend/assets/sections.jpg">
                    </div>
                </div>
                <div class="card">
                    <div class="card-info">
                        <h3><?php echo $total_faculty; ?></h3>
                        <p> Total Faculty</p>
                    </div>
                    <div class="card-icon">
                        <img src="../../../frontend/assets/faculty.jpg">
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script type="text/javascript" src="../../../frontend/layout/app.js" defer></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>