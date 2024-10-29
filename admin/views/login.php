<?php
session_start();
require_once('includes/nocsrf.php');

if (isset($_POST['username']) && isset($_POST['password'])) {
    try {
        // Run CSRF check, on POST data, in exception mode, for 60 minutes, in multiple mode.
        NoCSRF::check('csrf_token', $_POST, true, 60 * 60, true);

    } catch (Exception $e) {
        // CSRF attack detected
        $error = 2;
        $CSRF_error = $e->getMessage();
    }
}

// Generate CSRF token to use in form hidden field
$token = NoCSRF::generate('csrf_token');
$_SESSION['csrf_token_all'] = $token;
//exit;

?>

<!DOCTYPE html>
<html>
<head>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Login Form</title>
    <!-- Stylesheets -->
    <link href="styles/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/font-awesome.css">
    <link href="styles/login.css" rel="stylesheet">
    <link href="styles/bootstrap-responsive.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="images/favicon.png">

</head>
<body>

<?php
$config = simplexml_load_file("config.xml");

// Database
include("includes/config.php");
include("includes/functions.php");
?>



<?php

$msg_sent = "";
$msg_content = "";
$pageURL = dirname("http://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]) . "/";

if (isset($_POST['username']) && isset($_POST['password']) && !empty($_POST['username']) && !empty($_POST['password'])) {
    $qry = stopInjection(
        "SELECT *   FROM accounts WHERE username = '" . $_POST['username'] . "'  AND password = '" . $_POST['password'] . "'"
    );

    $loginReq = mysql_query($qry) or ($error = 1);

    $user = mysql_fetch_object($loginReq);


    if (mysql_num_rows($loginReq) > 0) {
        $_SESSION['id'] = $user->id;
        $_SESSION['username'] = $user->username;
        $_SESSION['email'] = $user->email;
        $_SESSION['close_right'] = $user->close_right;
        $_SESSION['delete_right'] = $user->delete_right;
        header('Location: admin.php');
    } else {
        $error = 1;
    }
}
//if( isset($_SESSION['username']) ) {

?>


<div class="admin-form">
    <div class="container-fluid">

        <div class="row-fluid">
            <div class="span12">

                <!-- Login Widget starts -->
                <div class="widget worange">
                    <!-- Widget head -->
                    <div class="widget-head">
                        <i class="icon-lock"></i> Login
                    </div>

                    <div class="widget-content">
                        <div class="padd">
                            <!-- Login form -->
                            <form class="form-horizontal" method="post">
                                <!-- Email -->
                                <div class="control-group">
                                    <label class="control-label" for="username">Username</label>

                                    <div class="controls">
                                        <input type="text" id="username" placeholder="Username" name="username">
                                    </div>
                                </div>
                                <!-- Password -->
                                <div class="control-group">
                                    <label class="control-label" for="inputPassword">Password</label>

                                    <div class="controls">
                                        <input type="password" id="password" placeholder="Password" name="password">
                                    </div>
                                </div>
                                <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
                                <!-- Alert message -->
                                <div class="control-group">
                                    <div class="controls">

                                    </div>
                                </div>

                                <!-- Remember me checkbox and sign in button -->
                                <div class="control-group">
                                    <div class="controls">
                                        <button type="submit" class="btn btn-danger" name="login">Sign in</button>
                                    </div>
                                </div>
                                <?php
                                if (isset($error) && $error == 1) {
                                    echo '<div class="alert alert-error">Bad informations : you are not logged in.</div>';
                                }
                                if (isset($error) && $error == 2) {
                                    echo '<div class="alert alert-error">' . $CSRF_error . '</div>';
                                }
                                ?>
                            </form>

                        </div>
                    </div>
                    <div class="widget-foot login-foot">
                        <!-- Not Registred? <a href="#">Register here</a> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
