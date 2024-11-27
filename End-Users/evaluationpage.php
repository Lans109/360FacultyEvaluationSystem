<?php
session_start();
include("databasecon.php");

// Ensure the user is logged in and is either a student, faculty, or program chair
if (!isset($_SESSION['loggedin']) || !in_array($_SESSION['user_type'], ['students', 'faculty', 'program_chair'])) {
    header("Location: ../index.php");
    exit();
}

// Get the evaluation ID from the URL
if (!isset($_GET['evaluation_id'])) {
    header("Location: students/student_evaluation.php");
    exit();
}

$evaluation_id = $_GET['evaluation_id'];
$user_email = $_SESSION['email'];
$user_type = $_SESSION['user_type'];

// Fetch the evaluation and its questions
$sql = "
    SELECT q.question_id, q.question_text 
    FROM questions q 
    WHERE q.survey_id = (SELECT survey_id FROM evaluations WHERE evaluation_id = ?)
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $evaluation_id);
$stmt->execute();
$result = $stmt->get_result();
$questions = $result->fetch_all(MYSQLI_ASSOC);

// Handle response submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_responses'])) {
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

    // Insert each response into the database
    foreach ($_POST['responses'] as $question_id => $rating) {
        $stmt = $conn->prepare("INSERT INTO responses (evaluation_id, question_id, rating) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $evaluation_id, $question_id, $rating);
        $stmt->execute();
        $stmt->close();
    }

    // Update evaluation status and save the comment
    if ($user_type == 'students') {
        $stmt = $conn->prepare("UPDATE students_evaluations SET is_completed = 1, comments = ? WHERE evaluation_id = ? AND student_id = (SELECT student_id FROM students WHERE email = ?)");
        $stmt->bind_param("sis", $comment, $evaluation_id, $user_email);
    } elseif ($user_type == 'faculty') {
        $stmt = $conn->prepare("UPDATE faculty_evaluations SET is_completed = 1, comments = ? WHERE evaluation_id = ? AND faculty_id = (SELECT faculty_id FROM faculty WHERE email = ?)");
        $stmt->bind_param("sis", $comment, $evaluation_id, $user_email);
    } elseif ($user_type == 'program_chair') {
        $stmt = $conn->prepare("UPDATE program_chair_evaluations SET is_completed = 1, comments = ? WHERE evaluation_id = ? AND chair_id = (SELECT chair_id FROM program_chairs WHERE email = ?)");
        $stmt->bind_param("sis", $comment, $evaluation_id, $user_email);
    }

    $stmt->execute();
    $stmt->close();

    // Redirect based on user type
    if ($user_type == 'students') {
        header("Location: students/student_evaluation.php");
    } elseif ($user_type == 'faculty') {
        header("Location: faculty/faculty_evaluation.php");
    } elseif ($user_type == 'program_chair') {
        header("Location: program_chair/program_chair_evaluation.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluation</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: "Poppins", Arial, sans-serif;
        background: linear-gradient(135deg, #7D0006, #D3D3D3);
        background-repeat: no-repeat;
        background-attachment: fixed;
        background-size: cover;
        color: #000;
        line-height: 1.6;
        margin: 0;
    }

    .header {
        background: #7D0006;
        padding: 1.5rem 0;
        text-align: center;
        color: #fff;
        position: sticky;
        top: 0;
        z-index: 1000;
        box-shadow: 2px 4px 8px rgba(0, 0, 0, 0.2);
    }

    .header h1 {
        font-size: 2.5rem;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }

    .container {
        max-width: 1100px;
        margin: 2rem auto;
        padding: 1rem;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.1);
    }

    .card {
        text-align: center;
        padding: 2rem;
        background: #f9f9f9;
        border-radius: 12px;
        box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.25);
    }

    .question-container {
        margin: 2rem 0;
        padding: 1rem;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .options-container {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin-top: 1rem;
    }

    .option-button {
        padding: 1rem 2rem;
        border: 2px solid #7D0006;
        background: #fff;
        color: #7D0006;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .option-button:hover {
        background: #f0f0f0;
    }

    .option-button.selected {
        background: #7D0006;
        color: #fff;
    }

    .navigation-buttons {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin-top: 2rem;
    }

    button {
        background: #7D0006;
        color: #fff;
        border: none;
        padding: 0.8rem 1.5rem;
        font-size: 1rem;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    button:hover {
        background: #D3D3D3;
        color: #7D0006;
    }

    button:disabled {
        background: #ccc;
        cursor: not-allowed;
    }

    textarea {
        width: 100%;
        padding: 1rem;
        margin-top: 1rem;
        border: 1px solid #ccc;
        border-radius: 8px;
        resize: vertical;
        min-height: 100px;
    }

    .progress-bar {
        width: 100%;
        height: 10px;
        background: #f0f0f0;
        border-radius: 5px;
        margin: 1rem 0;
        overflow: hidden;
    }

    .progress {
        height: 100%;
        background: #7D0006;
        transition: width 0.3s ease;
    }
    </style>
</head>

<body>
    <div class="header">
        <h1>Evaluation</h1>
    </div>
    <div class="container">
        <div class="card">
            <h2>Evaluation Questions</h2>
            <div class="progress-bar">
                <div id="progressBar" class="progress" style="width: 0%"></div>
            </div>
            <form id="evaluationForm" method="POST">
                <div id="evaluationQuestions" class="question-container"></div>
                <div id="commentSection" style="display:none;">
                    <label for="comment">Additional Comments (Optional):</label>
                    <textarea id="comment" name="comment" placeholder="Enter your comments here..."></textarea>
                </div>
                <div class="navigation-buttons">
                    <button type="button" id="prevBtn" onclick="prevQuestion()" style="display:none">Previous</button>
                    <button type="button" id="nextBtn" onclick="nextQuestion()">Next</button>
                    <button type="submit" id="submitBtn" name="submit_responses" style="display:none">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    let currentQuestionIndex = 0;
    let questions = <?php echo json_encode($questions); ?>;
    let responses = {};

    function initializeResponses() {
        questions.forEach(question => {
            responses[question.question_id] = null;
        });
        updateProgress();
    }

    function updateProgress() {
        const progress = (currentQuestionIndex / questions.length) * 100;
        document.getElementById('progressBar').style.width = `${progress}%`;
    }

    function displayQuestion() {
        const questionContainer = document.getElementById('evaluationQuestions');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');
        const commentSection = document.getElementById('commentSection');

        questionContainer.innerHTML = '';

        if (currentQuestionIndex < questions.length) {
            const question = questions[currentQuestionIndex];
            const optionsHtml = `
                    <div class="question-text">
                        <h3>Question ${currentQuestionIndex + 1} of ${questions.length}</h3>
                        <p>${question.question_text}</p>
                    </div>
                    <div class="options-container">
                        ${[1, 2, 3, 4, 5].map(i => `
                            <button type="button" 
                                class="option-button ${responses[question.question_id] === i ? 'selected' : ''}" 
                                onclick="selectOption(${question.question_id}, ${i})">${i}</button>
                        `).join('')}
                    </div>
                `;
            questionContainer.innerHTML = optionsHtml;

            prevBtn.style.display = currentQuestionIndex === 0 ? 'none' : 'inline-block';
            nextBtn.style.display = currentQuestionIndex === questions.length - 1 ? 'none' : 'inline-block';
            submitBtn.style.display = currentQuestionIndex === questions.length - 1 ? 'inline-block' : 'none';
            commentSection.style.display = currentQuestionIndex === questions.length - 1 ? 'block' : 'none';
        }

        updateProgress();
    }

    function selectOption(questionId, rating) {
        responses[questionId] = rating;

        // Update visual selection
        const options = document.querySelectorAll('.option-button');
        options.forEach(option => option.classList.remove('selected'));
        event.target.classList.add('selected');

        // Auto-advance to next question after a short delay
        if (currentQuestionIndex < questions.length - 1) {
            setTimeout(() => nextQuestion(), 500);
        }
    }

    function nextQuestion() {
        if (currentQuestionIndex < questions.length - 1) {
            currentQuestionIndex++;
            displayQuestion();
        }
    }

    function prevQuestion() {
        if (currentQuestionIndex > 0) {
            currentQuestionIndex--;
            displayQuestion();
        }
    }

    function validateForm() {
        const unansweredQuestions = questions.filter(q => responses[q.question_id] === null);
        if (unansweredQuestions.length > 0) {
            alert('Please answer all questions before submitting.');
            return false;
        }

        // Add responses to hidden form fields
        const form = document.getElementById('evaluationForm');
        Object.entries(responses).forEach(([questionId, rating]) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `responses[${questionId}]`;
            input.value = rating;
            form.appendChild(input);
        });

        return true;
    }

    window.onload = function() {
        initializeResponses();
        displayQuestion();
    };
    </script>
</body>
</html>