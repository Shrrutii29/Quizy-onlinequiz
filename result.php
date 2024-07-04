<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

$user_id = $_SESSION['uid'];

// Database connection
$con = new mysqli('localhost', 'shru', 'shru', 'Quizy');
if ($con->connect_error) {
    die("Connection Failed: " . $con->connect_error);
}

// Fetch list of quizzes attempted by the user
$stmt = $con->prepare("
    SELECT DISTINCT q.quiz_id, q.quiz_title
    FROM quizzes q
    JOIN answers a ON q.quiz_id = a.quiz_id
    WHERE a.uid = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$quizzesResult = $stmt->get_result();
$quizzes = [];
while ($row = $quizzesResult->fetch_assoc()) {
    $quizzes[] = $row;
}
$stmt->close();

// Calculate scores for each quiz
$scores = [];
foreach ($quizzes as $quiz) {
    $quiz_id = $quiz['quiz_id'];
    
    // Fetch questions and correct answers for the quiz
    $stmt = $con->prepare("SELECT question_id, correct_answer FROM questions WHERE quiz_id = ?");
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $questionsResult = $stmt->get_result();
    $questions = [];
    while ($row = $questionsResult->fetch_assoc()) {
        $questions[] = $row;
    }
    $stmt->close();
    
    // Fetch user's answers for the quiz
    $stmt = $con->prepare("SELECT question_id, answer_text FROM answers WHERE uid = ? AND quiz_id = ?");
    $stmt->bind_param("ii", $user_id, $quiz_id);
    $stmt->execute();
    $answersResult = $stmt->get_result();
    $userAnswers = [];
    while ($row = $answersResult->fetch_assoc()) {
        $userAnswers[$row['question_id']] = $row['answer_text'];
    }
    $stmt->close();
    
    // Calculate the score
    $score = 0;
    foreach ($questions as $question) {
        if (isset($userAnswers[$question['question_id']]) && $userAnswers[$question['question_id']] === $question['correct_answer']) {
            $score++;
        }
    }
    $totalQuestions = count($questions);
    $scores[$quiz_id] = [
        'quiz_title' => $quiz['quiz_title'],
        'score' => $score,
        'totalQuestions' => $totalQuestions
    ];
}

$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results - Quizy</title>
    <link rel="stylesheet" href="quizstyle.css?v1">
</head>
<body>
    <div class="container">
        <h2>Your Quiz Results</h2>
        <div class="results">
            <?php if (empty($scores)) { ?>
                <p>No quizzes attempted yet.</p>
            <?php } else { ?>
                <table>
                    <thead>
                        <tr>
                            <th>Quiz Title</th>
                            <th>Score</th>
                            <th>View Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($scores as $quiz_id => $details) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($details['quiz_title']); ?></td>
                                <td><?php echo htmlspecialchars($details['score']) . ' / ' . htmlspecialchars($details['totalQuestions']); ?></td>
                                <td><a href="review.php?quiz_id=<?php echo htmlspecialchars($quiz_id); ?>">review</a></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
        </div>
        <button onclick="window.location.href='index.php'">Back to Home</button>
    </div>
</body>
</html>
