<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quizy - Online Quiz Platform</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="header">
        <div class="top-row">
            <h1>Quizy</h1>
            <form class="search-form" action="quiz.html">
                <div class="input-group">
                    <input id="searchtext" type="text" placeholder="Search quiz..." aria-label="Search quiz">
                    <button type="submit" name="search" id="search"><i class="fas fa-search"></i></button>
                </div>
                <?php if (isset($_SESSION['uid'])): ?>
                    <button type="button" name="logout" id="logout">Logout</button>
                <?php else: ?>
                    <button type="button" name="signin" id="login">Log in / Sign up</button>
                <?php endif; ?>
            </form>
        </div>
        <nav>
            <a href="#">Home</a>
            <a href="#about">About Us</a>
            <a href="#feedback">Testimonial</a>
            <a href="#contactus">Contact Us</a>
        </nav>
    </header>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const login = document.getElementById('login');
            const logout = document.getElementById('logout');
            if (login) {
                login.addEventListener('click', function() {
                    window.location.href = 'login.php';
                });
            }
            if (logout) {
                logout.addEventListener('click', function() {
                    window.location.href = 'logout.php';
                });
            }
        });
    </script>
    
    <main class="main">
        <section class="text-col">
            <h1>Want to create a quiz?</h1>
            <form id="create-quiz">
                <button type="submit" name="createquiz">Create Quiz</button>
            </form>
            <h1>Want to take a quiz?</h1>
            <form id="take-quiz">
                <button type="submit" name="takequiz">Take Quiz</button>
            </form>
            <h1>Want to check result?</h1>
            <form id="result">
                <button type="submit" name="checkresult">Check Result</button>
                <button type="submit" name="leaderboard">Leaderboard</button>
            </form>
        </section>
        <section class="img-col">
            <img src="images/quiz1.png" alt="Quiz illustration">
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const createQuizForm = document.getElementById('create-quiz');
        
            createQuizForm.addEventListener('submit', function(event) {
                event.preventDefault();
            
                if (<?php echo isset($_SESSION['uid']) ? 'true' : 'false'; ?>) {
                    window.location.href = 'createquiz.php';
                } else {
                    alert("Please Login first !");
                    window.location.href = 'login.php';
                }
            });

            /*take quiz button */
            const takeQuizForm = document.getElementById('take-quiz');
        
            takeQuizForm.addEventListener('submit', function(event) {
                event.preventDefault();
            
                if (<?php echo isset($_SESSION['uid']) ? 'true' : 'false'; ?>) {
                    window.location.href = 'quiz_list.php';
                } else {
                    alert("Please Login first !");
                    window.location.href = 'login.php';
                }
            });

            /*check result button */
            const resultForm = document.getElementById('result');
            resultForm.addEventListener('submit', function(event) {
                event.preventDefault();
                if (<?php echo isset($_SESSION['uid']) ? 'true' : 'false'; ?>) {
                    window.location.href = 'result.php';
                } else {
                    alert("Please Login first !");
                    window.location.href = 'login.php';
                }
            });
        });
    </script>


    <section class="about" id="about">
        <div class="img-col">
            <img src="images/quiz.jpg" alt="quiz">
        </div>
        <div class="abt-text">
            <h2>About Quizy</h2>
            <p>Quizy is an online platform that allows users to create, share, and take quizzes on various topics. Our goal is to provide an easy-to-use and engaging experience for both quiz creators and participants.</p>
            <hr>
            <h2>Why Choose Quizy?</h2>
            <ul>
            <li><strong>Ease of Use:</strong> Our intuitive interface allows you to create and take quizzes with minimal effort.</li>
            <li><strong>Instant Feedback:</strong> Get immediate results and feedback to enhance your learning process.</li>
            <li><strong>Responsive Design:</strong> Access Quizy on any device, whether it's a computer, tablet, or smartphone.</li>
            <li><strong>Community Engagement:</strong> Share your quizzes with friends, colleagues, and the Quizy community.</li>
        </ul>
        <br>
        <p><i>Join Quizy today and start exploring the world of quizzes like never before!</i></p>
        </div>
    </section>

    <section class="features">
        <div>
            <h3>Easy to Use & Instant Feedback</h3>
        </div>
        <div>
            <h3>Intuitive Navigation & Responsive Design</h3>
        </div>
        <div>
            <h3>Clean and Simple Layout</h3>
        </div>
        <div>
            <h3>Social Sharing & Timed Quizzes</h3>
        </div>
    </section>

    <section class="feedback" id="feedback">
        <h2>Testimonials</h2>
        <div class="content">
            <article>
                <h3>Ramesh</h3>
                <p>This helped me create quizzes for my students' evaluations. It's indeed easy to use and implement.</p>
            </article>
            <article>
                <h3>Trupti Navale</h3>
                <p>I was searching for a long time for a website to help me create quizzes for my surveys, and finally, I found it!</p>
            </article>
            <article>
                <h3>Siddhi Jadhav</h3>
                <p>Happy to use this.</p>
            </article>
            <article>
                <h3>Shruti</h3>
                <p>Quizy is an amazing platform for creating quizzes. It's user-friendly and efficient!</p>
            </article>
            <article>
                <h3>Kamlesh kale</h3>
                <p>I love using Quizy for my classroom quizzes. It saves me so much time!</p>
            </article>
        </div>
        <div class="user-feedback">
            <textarea placeholder="Write your testimonial here..." aria-label="Write your feedback"></textarea>
            <button type="submit" name="submit">Submit</button>
        </div>
    </section>

    <section class="contactus" id="contactus">
        <h2>Contact us</h2>
        <div class="info-item">
            <a><i class="fas fa-phone icon"></i></a>
            <span>Phone: 9579958358</span>
        </div>
        
        <div class="info-item">
            <a ><i class="fas fa-envelope icon"></i></a>
            <span>Email: shrutinavale29@gmail.com</span>
        </div>

        <div class="info-item">
            <a href="https://linkedin.com/in/shrutinavale"><i class="fab fa-linkedin"></i></a>
            <span>Linkdeln: shrutinavale</span>
        </div>
        <div class="info-item">
            <a href="https://github.com/Shrrutii29"><i class="fab fa-github"></i></a>
            <span>GitHub: shrrutii29</span>
        </div>

    </div>
    </section>
    <footer class="footer">
        <div class="links">
            <a href="#">Privacy Policy</a>
            <a href="#about">About Us</a>
            <a href="#">Terms of Service</a>
            <a href="#contactus">Contact Us</a>
            <a href="https://linkedin.com/in/shrutinavale">Linkdeln</a>
            <a href="https://github.com/Shrrutii29">GitHub</a>
        </div>
        <div class="copyright">
            <h4>All &copy; reserved to shruti :]</h4>
        </div>
    </footer>
</body>
</html>
