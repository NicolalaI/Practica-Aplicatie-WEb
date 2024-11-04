<?php
include('db.php');

if($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST["register"])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $check_name_query = "SELECT * FROM users WHERE name=?";
    $stmt_name = mysqli_prepare($conn, $check_name_query);
    mysqli_stmt_bind_param($stmt_name, "s", $name);
    mysqli_stmt_execute($stmt_name);
    $result_name = mysqli_stmt_get_result($stmt_name);

    
    $check_email_query = "SELECT * FROM users WHERE email=?";
    $stmt_email = mysqli_prepare($conn, $check_email_query);
    mysqli_stmt_bind_param($stmt_email, "s", $email);
    mysqli_stmt_execute($stmt_email);
    $result_email = mysqli_stmt_get_result($stmt_email);

    if (mysqli_num_rows($result_name) > 0) {
       $nameError = "Name already taken.";
    } elseif (mysqli_num_rows($result_email) > 0) {
        $emailError = "Email already used.";
    } else {
    
        $query = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";
        if (mysqli_query($conn, $query)) {
            header("Location: page.php");
                exit;
        } else {
            $registerError =  "Error: " . mysqli_error($conn);
        }
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST"  && isset($_POST["login"])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if ($password == $user['password']) {
            $_SESSION['user'] = $user['name'];
            header("Location: page.php");
            exit;
        } else {
            $error =  "Password incorect.";
        }
    } else {
        $error =  "The User does not exist.";
    }

}

$page= "login";
$page ="register";
include("header.php");
?>

    
    <div class="container login" id="container">
        <div class="form-container sign-up">
            <form action="login-reg.php" method="POST">
                <h1>Create Account </h1>
                <span>Use your email for registration</span>
                <input type="text" placeholder="Name" id="name" name="name" value="<?=(isset($_POST["name"]))?$_POST["name"]:""?>" required>
                <?php if (!empty($nameError)): ?>
                    <p class="error-message"><?php echo $nameError; ?></p>
                <?php endif; ?>
                <input type="email" placeholder="Email" id="email" name="email" required>
                <?php if (!empty($emailError)): ?>
                    <p class="error-message"><?php echo $emailError; ?></p>
                <?php endif; ?>
                <input type="password" placeholder="Password" id="password" name="password" required>
                <?php if (!empty($registerError)): ?>
                    <p class="error-message"><?php echo $registerErrorsssss; ?></p>
                <?php endif; ?>
                <button type = "submit" name="register">Sign Up</button>
            
            </form>
        </div>
        <div class="form-container sign-in">
            <form action="login-reg.php" method="POST">
                <h1>Sign In </h1>
                <?php if (!empty($error)): ?>
                    <p class="error-message"><?php echo $error; ?></p>
                <?php endif; ?>
              
                <input type="email" placeholder="Email" id="email"   name="email" required>
                <input type="password" placeholder="Password" id="password" name="password" required>
                <a href="#">Forgot Your Password?</a>
                <button type = "submit" name="login">Sign In</button>

            </form>
        </div>
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Welcome Back!</h1>
                    <p>Enter your personal details to use all of the features</p>
                    <button class="hidden" id="login">Sign In</button>
                </div>

                <div class="toggle-panel toggle-right">
                    <h1>Hello, there!</h1>
                    <p>Register with your personal details to use all of the features</p>
                    <button class="hidden" id="register">Sign Up</button>
                </div>
            </div>
        </div>
    </div>

    <script >
        const container = document.getElementById('container');
const registerBtn = document.getElementById('register');
const loginBtn = document.getElementById('login');

registerBtn.addEventListener('click', () => {
    container.classList.add("active");
});

loginBtn.addEventListener('click', () => {
    container.classList.remove("active");
});


    </script>

<? include("footer.php")?>