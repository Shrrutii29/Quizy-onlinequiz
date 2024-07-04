<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_SESSION['uid'])) {
    $uid = $_SESSION['uid'];
    error_log("User ID: $uid"); // Log the user ID for debugging
    // ... rest of the code
} else {
    error_log("User ID not set in session.");
    die("User ID not set. Please log in.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $quizTitle = trim($_POST['quizTitle']);
    $quizDescription = trim($_POST['quizDescription']);
    $timer = intval($_POST['timer']);

    // Database connection
    $con = new mysqli('localhost', 'shru', 'shru', 'Quizy');

    if ($con->connect_error) {
        die("Connection Failed: " . $con->connect_error);
    } 

    if (isset($_SESSION['uid'])) {
        $uid = $_SESSION['uid'];

        // Prepare the SQL statement
        $stmt = $con->prepare("INSERT INTO quizzes (uid, quiz_title, quiz_description, timer) VALUES (?, ?, ?, ?)");
        if ($stmt === false) {
            die("Prepare failed: " . $con->error);
        }

        // Bind parameters
        $stmt->bind_param("issi", $uid, $quizTitle, $quizDescription, $timer);

        // Execute the statement
        if ($stmt->execute()) {
            $quizId = $stmt->insert_id; // Get the last inserted quiz id

            foreach ($_POST['questions'] as $questionIndex => $questionData) {
                $questionText = trim($questionData['question']);
                $answerType = trim($questionData['answerType']);
                $correctAnswer = trim($questionData['correctAnswer']);

                // Prepare the SQL statement for inserting questions
                $stmt = $con->prepare('INSERT INTO questions (quiz_id, question_text, answer_type, correct_answer) VALUES (?, ?, ?, ?)');
                if ($stmt === false) {
                    die("Prepare failed: " . $con->error);
                }

                // Bind parameters
                $stmt->bind_param("isss", $quizId, $questionText, $answerType, $correctAnswer);

                // Execute the statement
                if ($stmt->execute()) {
                    $questionId = $stmt->insert_id; // Get the last inserted question id

                    // Insert options data if applicable
                    if ($answerType === 'radio' || $answerType === 'checkbox') {
                        foreach ($questionData['options'] as $optionLabel => $optionText) {
                            // Prepare the SQL statement for inserting options
                            $stmt = $con->prepare('INSERT INTO options (question_id, option_text) VALUES (?, ?)');
                            if ($stmt === false) {
                                die("Prepare failed: " . $con->error);
                            }

                            // Bind parameters
                            $stmt->bind_param("is", $questionId, $optionText);

                            // Execute the statement
                            $stmt->execute();
                        }
                    }
                } else {
                    die("Execute failed: " . $stmt->error);
                }
            }

            echo "<script>alert('Quiz created successfully.');</script>";
            echo "<script>setTimeout(function() { window.location.href = 'quiz_list.php'; }, 2000);</script>";
        } else {
            echo "<script>alert('Quiz creation failed. Try again later.');</script>";
            echo "<script>setTimeout(function() { window.location.href = 'index.php'; }, 2000);</script>";
        }

        $stmt->close();
        $con->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Quiz - Quizy</title>
    <link rel="stylesheet" href="style1.css?v2">
</head>
<body>
    <div class="container">
        <h2>Create a New Quiz</h2>
        <form method="post" id="quizForm">
            <div class="row">
                <label for="quizTitle">Quiz Title:</label>
                <input type="text" id="quizTitle" name="quizTitle" placeholder="Enter quiz title here.." required>
            </div>

            <div class="row">
                <label for="quizDescription">Description:</label>
                <textarea id="quizDescription" name="quizDescription" rows="4" placeholder="Quiz description.."></textarea>
            </div>

            <div class="row">
                <label for="timer">Timer (in minutes):</label>
                <input type="number" id="timer" name="timer" min="1" max="120" placeholder="Enter duration in minutes">
            </div>

            <div id="questionsContainer">
                <!-- Questions will be dynamically added here -->
            </div>

            <div class="row">
                <button type="button" id="addQuestion" class="add-button">Add Question</button>
                <button type="button" id="deleteQuestion" class="delete-button">Delete Last Question</button>
            </div>
            <button type="submit" class="submit-button">Create Quiz</button>
            <button type="submit" class="submit-button">Go back</button>
            <a href="index.php">Go back </a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const questionsContainer = document.getElementById('questionsContainer');
            const addQuestionButton = document.getElementById('addQuestion');
            const deleteQuestionButton = document.getElementById('deleteQuestion');
            let questionCount = 0;

            const optionLabels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('');

            addQuestionButton.addEventListener('click', function() {
                questionCount++;

                const newQuestionDiv = document.createElement('div');
                newQuestionDiv.classList.add('question');
                newQuestionDiv.setAttribute('data-question-id', questionCount);

                newQuestionDiv.innerHTML = `
                    <div class="row">
                        <label for="question${questionCount}">Question ${questionCount}:</label>
                        <input type="text" id="question${questionCount}" name="questions[${questionCount}][question]" required>
                    </div>
                    <div class="row">
                        <label for="answerType${questionCount}">Answer Type:</label>
                        <select id="answerType${questionCount}" name="questions[${questionCount}][answerType]" onchange="toggleOptions(${questionCount})" required>
                            <option value="">Select Answer Type</option>
                            <option value="text">Text</option>
                            <option value="radio">Radio Buttons</option>
                            <option value="checkbox">Checkboxes</option>
                        </select>
                    </div>
                    <div id="options${questionCount}" class="options" style="display: none;">
                        <!-- Options or file upload fields will be dynamically added here -->
                    </div>
                    <div class="row">
                        <label for="correctAnswer${questionCount}">Correct Answer:</label>
                        <input type="text" id="correctAnswer${questionCount}" name="questions[${questionCount}][correctAnswer]" required>
                    </div>
                `;

                questionsContainer.appendChild(newQuestionDiv);
            });

            deleteQuestionButton.addEventListener('click', function() {
                if (questionCount > 0) {
                    const lastQuestionDiv = document.querySelector(`div[data-question-id='${questionCount}']`);
                    if (lastQuestionDiv) {
                        lastQuestionDiv.remove();
                        questionCount--;
                    }
                } else {
                    alert('No questions to delete.');
                }
            });

            window.toggleOptions = function(questionIndex) {
                const answerType = document.getElementById(`answerType${questionIndex}`).value;
                const optionsDiv = document.getElementById(`options${questionIndex}`);

                optionsDiv.innerHTML = ''; // Clear previous options

                if (answerType === 'radio' || answerType === 'checkbox') {
                    const optionCount = prompt(`Enter number of options for Question ${questionIndex}`);
                    if (!isNaN(optionCount) && parseInt(optionCount) > 0) {
                        for (let i = 0; i < optionCount; i++) {
                            optionsDiv.innerHTML += `
                                <div class="row">
                                    <label for="option${optionLabels[i]}_${questionIndex}">Option ${optionLabels[i]}:</label>
                                    <input type="text" id="option${optionLabels[i]}_${questionIndex}" name="questions[${questionIndex}][options][${optionLabels[i]}]" required>
                                </div>
                            `;
                        }
                    } else {
                        alert('Invalid number of options. Please enter a valid number.');
                    }
                }

                optionsDiv.style.display = answerType !== 'text' ? 'block' : 'none';
            };
        });
    </script>
</body>
</html>
