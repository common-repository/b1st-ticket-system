<?php

$this->require_once_includes('nocsrf.php');

?>

<!DOCTYPE html>
<html>
<head>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Email Connector</title>
    <link href='http://fonts.googleapis.com/css?family=Monda' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="./styles/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="./styles/bootstrap-responsive.css">
    <script language="javascript">

        var request = createRequestObject();
        var dataReturn = '';
        var ajaxTimeout = '';
        var enterChecker = false;

        function createRequestObject() {
            var ro;
            var browser = navigator.appName;
            if (browser == 'Microsoft Internet Explorer') {
                ro = new ActiveXObject('Microsoft.XMLHTTP');
            }
            else {
                ro = new XMLHttpRequest();
            }
            return ro;
        }

        function makeRequest(url, fun) {
            enterChecker = false;
            request.open('get', url);
            request.onreadystatechange = function () {
                handleResponse(fun);
            }

            try {
                request.send(null);
            }
            catch (err) {
                alert('Error occured: ' + err);
                showHidePreloader(false);
            }
        }

        function makePostRequest(url, params, fun) {
            request.open('POST', url, true);

            //Send the proper header information along with the request
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.setRequestHeader('Content-length', params.length);
            request.setRequestHeader('Connection', 'close');

            request.onreadystatechange = function () {//Call a function when the state changes.
                if (request.readyState == 4 && request.status == 200) {
                    dataReturn = request.responseText;
                    if (fun != '')
                        ajaxTimeout = setTimeout(fun + '()', 200);
                }
            }

            try {
                request.send(params);
            }
            catch (err) {
                alert('Error occured: ' + err);
                showHidePreloader(false);
            }
        }

        function handleResponse(fun) {
            if (request.readyState < 4) {
                ajaxTimeout = setTimeout('handleResponse(\'' + fun + '\')', 10);
            }
            else if (request.readyState == 4 && !enterChecker) {
                enterChecker = true;
                var response = request.responseText;
                dataReturn = response;

                if (fun != '')
                    ajaxTimeout = setTimeout(fun + '()', 200);
            }
        }

        function stopAjax() {
            clearTimeout('ajaxTimeout');
            ajaxTimeout = '';
        }


        function showContent() {
            showHidePreloader(false);
            document.getElementById('resultDiv').innerHTML = dataReturn;
            document.getElementById('resultDiv').style.display = 'block';
            //location.reload()
        }

        function showHidePreloader(show) {
            if (show)
                document.getElementById('preloader').style.display = 'block';
            else
                document.getElementById('preloader').style.display = 'none';
        }
    </script>
</head>
<body>

<?php
$this->include_functions("functions.php");

?>

<style>
    .container {
        margin-top: 30px;
        background-color: <?php echo $config->backgroundcolor ?>;
        color: <?php echo $config->inputtextcolor ?>;
        padding: 10px;
    }

    .test {
        background-color: <?php echo $config->loadercolor ?>;
    }

    input, textarea, select, button {
        background-color: <?php echo $config->inputcolor ?>;
        color: <?php echo $config->inputtextcolor ?>;
    }

    h3, contact-result, th {
        color: <?php echo $config->titlecolor ?>;
    }

    .breadcrumb {
        background-color: <?php echo $config->inputcolor ?>;
        color: <?php echo $config->inputtextcolor ?>;
    }

    .breadcrumb a {
        color: <?php echo $config->inputtextcolor ?>;
        text-shadow: none;
    }

    textarea {
        box-sizing: border-box; /* For IE and modern versions of Chrome */
        -moz-box-sizing: border-box; /* For Firefox                          */
        -webkit-box-sizing: border-box;
        resize: none;
        width: 100%;
    }

</style>


<div class="container">
    <div class="row">
        <div class="span12">

            <?php

            if (isset($_SESSION['username'])) {

            ?>


            <h3>Administration</h3>

            <a class="btn" style="margin-bottom: 15px; float:right;" href="./logout.php">Log out</a>
            <ul class="nav nav-tabs">
                <?php createMenu(5, $_SESSION['admin']); ?>
            </ul>

            <?php

            $_POST['csrf_token'] = $_SESSION['csrf_token_all'];

            if (isset($_SESSION['username'], $_SESSION['csrf_csrf_token'])) {

                try {
                    // Run CSRF check, on POST data, in exception mode, for 60 minutes, in multiple mode.
                    NoCSRF::check('csrf_token', $_POST, true, 60 * 60, true);

                } catch (Exception $e) {
                    // CSRF attack detected
                    $CSRF_error = $e->getMessage();
                    echo "<div class='alert alert-error'>" . $CSRF_error . "</div>";
                    exit;
                }

            }

            ?>


            <div class="row">
                <div class="span12">

                </div>
            </div>

            <div class="row">
                <div class="span10">

                    <div class="row">
                        <div class="span12">
                            <td style="text-align:left; width: 30px%;"><input type="button" class="btn btn-info"
                                                                              onclick="showHidePreloader(true); makeRequest('email_connector.php', 'showContent')"
                                                                              value="Start receiving messages"/></td>
                            <div><br>

                                <div>
                                    <div class="alert alert-info span10">
                                        Here you can receive your messages in your <?php echo htmlspecialchars(
                                            $config->emailUsername
                                        ); ?> Inbox. It will be retrieved automatically to your ticket system. You can
                                        click <a href='settings.php#email'>here</a> to change your email under email
                                        integration settings-tab.
                                    </div>
                                    <div id="preloader" style="display: none;"><img src="ajax-loader.gif"/></div>
                                    <div class="alert alert-success span10" id="resultDiv" style="display: none;"></div>
                                </div>
                            </div>

                            <?php
                            } else {
                                header("location:admin.php");
                            } ?>
                        </div>
                    </div>
                </div>

                <?php wp_enqueue_script('jquery'); ?>
                <script type="text/javascript">
                </script>

</body>
</html>
