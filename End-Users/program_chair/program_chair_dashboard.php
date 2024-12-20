<?php
session_start();
include('../db/databasecon.php');

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../index.php");
    exit();
}

// Fetch the user's name and email from the session
$name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Program Chair';
$email = $_SESSION['email'];
$faculty_count= 0;


// For showing welcome only once when dashboard refreshes
if (!isset($_SESSION['welcome_shown'])) {
    $_SESSION['welcome_shown'] = true; // Set flag to true
} else {
    $_SESSION['welcome_shown'] = false; // Flag is already true, don't show the message
}

// Fetch Program Chair's profile image and department details
$stmt = $conn->prepare("SELECT d.department_name, d.department_code, d.department_id, p.profile_image
                        FROM departments d
                        JOIN program_chairs p ON d.department_id = p.department_id
                        WHERE p.email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($department_name, $department_code, $department_id, $profile_image);

// Fetch department details and profile image
if ($stmt->num_rows > 0) {
    $stmt->fetch();
} else {
    echo "Department not found.";
    exit();
}

// Now fetch faculty members and their courses using a single query with LEFT JOINs
$stmt_faculty_courses = $conn->prepare("
    SELECT f.faculty_id, f.first_name, f.last_name, f.email, 
           c.course_name, c.course_code, c.course_description, cs.section
    FROM faculty f
    LEFT JOIN faculty_courses fc ON f.faculty_id = fc.faculty_id
    LEFT JOIN course_sections cs ON fc.course_section_id = cs.course_section_id
    LEFT JOIN courses c ON cs.course_id = c.course_id
    WHERE f.department_id = ?"); // No need for faculty_departments join

$stmt_faculty_courses->bind_param("i", $department_id); // Binding the department ID
$stmt_faculty_courses->execute();
$stmt_faculty_courses->store_result();
$stmt_faculty_courses->bind_result($faculty_id, $faculty_first_name, $faculty_last_name, $faculty_email, 
                                    $course_name, $course_code, $course_description, $section);
// Create an array to hold faculty data, where each faculty will have an array of courses
$faculty_courses = [];



// Fetch courses for each faculty member
while ($stmt_faculty_courses->fetch()) {
    // Check if the faculty is already added to the array, if not, initialize
    if (!isset($faculty_courses[$faculty_email])) {
        $faculty_courses[$faculty_email] = [
            'faculty_id' => $faculty_id,
            'first_name' => $faculty_first_name,
            'last_name' => $faculty_last_name,
            'email' => $faculty_email,
            'courses' => [] // Initialize courses array
        ];
    }

    // If there is no course data (i.e., course fields are NULL), skip adding courses
    if ($course_name === NULL || $course_code === NULL || $course_description === NULL || $section === NULL) {
        continue; // This faculty member has no courses linked, so skip adding courses
    }

    // Append the course information to the faculty's courses list
    $faculty_courses[$faculty_email]['courses'][] = [
        'course_name' => $course_name,
        'course_code' => $course_code,
        'course_description' => $course_description,
        'section' => $section
    ];
}

// Count how many faculty members belong to the program chair's department
$stmt_faculty_count = $conn->prepare("
    SELECT COUNT(*) 
    FROM faculty 
    WHERE department_id = ?
");
$stmt_faculty_count->bind_param("i", $department_id);
$stmt_faculty_count->execute();
$stmt_faculty_count->store_result();
$stmt_faculty_count->bind_result($faculty_count);

$stmt_faculty_count->fetch();
$stmt_faculty_count->close();


// Close the statements
$stmt->close();
$stmt_faculty_courses->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Program Chair Dashboard</title>
    <link rel="stylesheet" href="../Styles/styles.css">
</head>

<body>

    <!-- Centered Header -->
    <div class="header">
        <div class="nav-title">
            <h1>Program Chair Dashboard</h1>
        </div>
        <?php include 'program_chair_navbar.php' ?>
    </div>

    <div class="container">
        <!-- Welcome Message -->
        <?php if ($_SESSION['welcome_shown']): ?>
            <div class="welcome-message" id="welcome-message">
                Welcome back, <?php echo htmlspecialchars($name); ?>!
                <button class="close-btn" onclick="closeWelcomeMessage()">X</button>
            </div>
        <?php endif; ?>

        <!-- Profile Section -->
        <div class="card">

            <div>
                <h2 class="department-info">
                    Department: <?php echo htmlspecialchars($department_name); ?>
                    <span>(<?php echo htmlspecialchars($department_code); ?>)</span>
                </h2>
            </div>
            <!-- Managed Courses Section -->
            <h3>Faculty Members in Your Department:</h3>
            <?php if (!empty($faculty_courses)): ?>
                <?php foreach ($faculty_courses as $faculty): ?>
                    <div class="course-card">
                        <h4><?php echo htmlspecialchars($faculty['first_name'] . " " . $faculty['last_name']); ?></h4>
                        <p>Email: <?php echo htmlspecialchars($faculty['email']); ?></p>

                        <h5>Courses Taught:</h5>
                        <?php foreach ($faculty['courses'] as $course): ?>
                            <p><?php echo htmlspecialchars($course['course_name']); ?>
                                    (<?php echo htmlspecialchars($course['course_code']); ?>) - Section:
                                    <?php echo htmlspecialchars($course['section']); ?></strong>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No faculty members found in your department.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function closeWelcomeMessage() {
            document.getElementById('welcome-message').style.display = 'none';
        }
    </script>
</body>

</html>