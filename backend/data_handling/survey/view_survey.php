<?php
include_once "../../../config.php";
include '../../db/dbconnect.php';

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Get the survey_id from the URL
$survey_id = isset($_GET['survey_id']) ? mysqli_real_escape_string($con, $_GET['survey_id']) : '';

// Fetch survey details
$survey_query = "
    SELECT survey_name
    FROM surveys 
    WHERE survey_id = '$survey_id'
";
$survey_result = mysqli_query($con, $survey_query);
$survey = mysqli_fetch_array($survey_result);

// Fetch questions grouped by criteria
$questions_query = "
    SELECT 
        q.question_id,
        q.question_text,
        q.question_code,
        c.criteria_id,
        c.description
    FROM 
        questions q
    JOIN
        questions_criteria c ON q.criteria_id = c.criteria_id
    WHERE 
        q.survey_id = '$survey_id'
    ORDER BY c.description
";
$questions_result = mysqli_query($con, $questions_query);

// Group questions by criteria
$questions_by_criteria = [];
while ($question = mysqli_fetch_assoc($questions_result)) {
    $questions_by_criteria[$question['description']][] = $question;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survey Questions</title>
    <link rel="stylesheet" href="../../../frontend/templates/admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <?php include '../../../frontend/layout/navbar.php'; ?>
    <?php include '../../../frontend/layout/sidebar.php'; ?>
    <?php include '../../../frontend/layout/confirmation_modal.php'; ?>

    <main>
        <div class="upperMain">
            <div><h1><?= htmlspecialchars($survey['survey_name']); ?></h1></div>
        </div>
        <div class="content">
            <?php foreach ($questions_by_criteria as $criteria_description => $questions): ?>
                <div class="criteria-title"><?= htmlspecialchars($criteria_description); ?></div>
                <div class="table">
                    <table>
                        <thead>
                            <tr>
                                <th width="100px">Q. Code</th>
                                <th>Description</th>
                                <th width="500px"></th>
                                <th width="100px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($questions as $question): ?>
                                <tr>
                                    <td><?= htmlspecialchars($question['question_code']); ?></td>
                                    <td><?= htmlspecialchars($question['question_text']); ?></td>
                                    <td>
                                        <div class="visual-rating">
                                            <i class="fa fa-circle-thin" aria-hidden="true"></i>
                                            <i class="fa fa-circle-thin" aria-hidden="true"></i>
                                            <i class="fa fa-circle-thin" aria-hidden="true"></i>
                                            <i class="fa fa-circle-thin" aria-hidden="true"></i>
                                            <i class="fa fa-circle-thin" aria-hidden="true"></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="action-btns">
                                            <button class="edit-btn" data-toggle="modal" 
                                                data-target="#editModal<?= $question['question_id']; ?>" 
                                                data-question_id="<?= $question['question_id']; ?>" 
                                                data-question_text="<?= htmlspecialchars($question['question_text']); ?>" 
                                                data-criteria_id="<?= $question['criteria_id']; ?>" 
                                                data-criteria_description="<?= htmlspecialchars($criteria_description); ?>">
                                                <img src="../../../frontend/assets/icons/edit.svg">
                                            </button>
                                            <a href="delete_question.php?question_id=<?= $question['question_id']; ?>&survey_id=<?= $survey_id; ?>" 
                                            onclick="openDeleteConfirmationModal(event, this)" class="delete-btn">
                                                <img src="../../../frontend/assets/icons/delete.svg">
                                            </a>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal for editing question -->
                                <div class="modal fade" id="editModal<?= $question['question_id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Question</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="update_question.php" method="POST">
                                                    <input type="hidden" name="survey_id" value="<?= $survey_id; ?>">
                                                    <input type="hidden" name="question_id" value="<?= $question['question_id']; ?>">

                                                    <!-- Edit Question Code -->
                                                    <div class="form-group">
                                                        <label>Question Code</label>
                                                        <input type="text" class="form-control" name="question_code" value="<?= htmlspecialchars($question['question_code']); ?>" required>
                                                    </div>

                                                    <!-- Edit Question Text -->
                                                    <div class="form-group">
                                                        <label>Question Text</label>
                                                        <textarea class="form-control" name="question_text" rows="3" required><?= htmlspecialchars($question['question_text']); ?></textarea>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="cancel-btn" data-dismiss="modal">Close</button>
                                                        <button type="submit" class="save-btn">Save changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php endforeach; ?>
                            <tr>
                            <td colspan="4" class="add-question">
                                <div>
                                    <button class="insert-btn full-td-btn" data-toggle="modal" data-target="#addQuestionModal" 
                                        data-criteria_id="<?= $questions[0]['criteria_id']; ?>" 
                                        data-criteria_description="<?= htmlspecialchars($criteria_description); ?>"
                                        onclick="setCriteriaData(this)">
                                        <img src="../../../frontend/assets/icons/add.svg">
                                    </button>
                                </div>
                            </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Modal for adding new question -->
    <div class="modal fade" id="addQuestionModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Question</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="add_question.php" method="POST">
                        <input type="hidden" name="survey_id" value="<?= $survey_id; ?>">
                        <input type="hidden" name="criteria_id" id="criteria_id" value="">
                        <input type="hidden" name="criteria_description" id="criteria_description" value="">

                        <!-- New Question Code -->
                        <div class="form-group">
                            <label>Question Code</label>
                            <input type="text" class="form-control" name="question_code" required>
                        </div>

                        <!-- New Question Text -->
                        <div class="form-group">
                            <label>Question Text</label>
                            <textarea class="form-control" name="question_text" rows="3" required></textarea>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="cancel-btn" data-dismiss="modal">Close</button>
                            <button type="submit" class="save-btn">Save Question</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // JavaScript to dynamically set criteria data in the modal
        function setCriteriaData(button) {
            // Get criteria_id and criteria_description from the button's data attributes
            var criteriaId = $(button).data('criteria_id');
            var criteriaDescription = $(button).data('criteria_description');

            // Set these values in the hidden inputs in the modal
            $('#criteria_id').val(criteriaId);
            $('#criteria_description').val(criteriaDescription);
        }
    </script>
</body>

</html>