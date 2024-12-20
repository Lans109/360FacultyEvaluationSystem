<?php
// Include the database connection
include_once "../../../config.php";
include BACKEND_PATH . '/db/dbconnect.php';

// Authentication check
include '../authentication.php';

// Check if the request method is POST and if 'course_id' is set
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_id'])) {
    $course_id = $_POST['course_id'];

    // Sanitize the course_id to prevent SQL injection
    $course_id = mysqli_real_escape_string($con, $course_id);

    // SQL query to delete the course from the database
    $delete_course_query = "DELETE FROM courses WHERE course_id = '$course_id'";

    // Attempt to execute the query
    if (mysqli_query($con, $delete_course_query)) {
        // If successful, set session variables for success message
        $_SESSION['status'] = 'success';
        $_SESSION['message'] = 'Course deleted successfully!';
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    } else {
        // If query fails, log the error and set session variables for error message
        error_log("Database Error: " . mysqli_error($con)); // Log the error
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Error deleting course. Please try again later.';
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
} else {
    // If no course_id is passed or incorrect request method, set session variables for error message and redirect
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'Invalid course ID or request method.';
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
