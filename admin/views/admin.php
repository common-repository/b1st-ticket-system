<?php

session_start();
require_once('includes/nocsrf.php');

function get_gravatar($email, $s = 80, $d = 'mm', $r = 'g', $img = true, $atts = array())
{
    $url = 'http://www.gravatar.com/avatar/';
    $url .= md5(strtolower(trim($email)));
    $url .= "?s=$s&d=$d&r=$r";
    if ($img) {
        $url = '<img src="' . $url . '"';
        foreach ($atts as $key => $val) {
            $url .= ' ' . $key . '="' . $val . '"';
        }
        $url .= ' />';
    }

    return $url;
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Administration</title>
    <link href='http://fonts.googleapis.com/css?family=Monda' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="./styles/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="./styles/bootstrap-responsive.css">
    <link rel="shortcut icon" href="images/favicon.png">
</head>
<body>

<?php
$config = simplexml_load_file("config.xml");

// Database
include("includes/config.php");
include("includes/functions.php");
?>

<style>

    #uploadFrame {
        display: none;
    }

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

    .opened {
        display: block;
        width: 10px;

        transform: rotate(135deg);
        transform-origin: center center 0;
    }

    .description {
        border-bottom: 1px solid <?php echo $config->inputtextcolor ?>;
    }

    .details {
        border-bottom: 1px solid <?php echo $config->inputtextcolor ?>;
    }

    .description div {
        height: 34px;
        line-height: 34px;
        text-align: center;
    }
</style>


<div class="container">
<div class="row">
<div class="span12">

<?php
$msg_sent = "";
$msg_content = "";
$pageURL = dirname("http://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]) . "/";

if (isset($_POST['username']) && isset($_POST['password']) && !empty($_POST['username']) && !empty($_POST['password'])) {
    $loginReq = mysql_query(
        "SELECT *
                                                         FROM accounts
                                                         WHERE username = '" . $_POST['username'] . "'
                                                 AND password = '" . $_POST['password'] . "'"
    ) or die(mysql_error());

    $user = mysql_fetch_object($loginReq);


    if (mysql_num_rows($loginReq) > 0) {
        $_SESSION['id'] = $user->id;
        $_SESSION['username'] = $user->username;
        $_SESSION['email'] = $user->email;
        $_SESSION['close_right'] = $user->close_right;
        $_SESSION['delete_right'] = $user->delete_right;

    } else {
        $error = 1;
    }
}

if (isset($_SESSION['username'])) {

if (isset($_GET["action"]) && isset($_GET["msg_id"])) {
    if ($_GET["action"] == "delete") {
        if ($_SESSION['delete_right'] == 1) {
            $msg_id = intval($_GET["msg_id"]);

            // Verify if the message exists
            $messageById = mysql_query("SELECT msg_id FROM messages WHERE msg_id = " . $msg_id);

            // Ok
            if (mysql_num_rows($messageById) > 0) {
                mysql_query("DELETE FROM messages WHERE msg_id = " . $msg_id);
                mysql_query("DELETE FROM answers WHERE a_msg_id = " . $msg_id);
                $msg_sent = 1;
                $msg_content = "Your message has been well deleted";
            }
        } else {
            $righterror = 1;
        }
    }
}

$order = "desc";
if (isset($_GET['order'])) {
    $order = $_GET['order'];
}

$state = "all";
if (isset($_POST['state'])) {
    $state = $_POST['state'];
}

$priority = "all";
if (isset($_POST['priority'])) {
    $priority = $_POST['priority'];
}

$division = "all";
if (isset($_POST['division'])) {
    $division = $_POST['division'];
}

$product = "all";
if (isset($_POST['product'])) {
    $product = $_POST['product'];
}

$searchEmail = '';
if (isset($_POST['searchEmail'])) {
    $searchEmail = $_POST['searchEmail'];
}

$searchId = '';
if (isset($_POST['searchId'])) {
    $searchId = $_POST['searchId'];
}

$nbParPage = $config->nbPerPage;

$totalreq = mysql_query('SELECT COUNT(*) AS total FROM messages');
$data_total = mysql_fetch_assoc($totalreq);
$total = $data_total['total'];

$nbPages = ceil($total / $nbParPage);

if (isset($_POST['page'])) {
    $actualPage = intval($_POST['page']);

    if ($actualPage > $nbPages) {
        $actualPage = $nbPages;
    }
} else {
    $actualPage = 1;
}

if (isset($_POST['msg_id']) && isset($_POST['status_edit'])) {
    if ($_SESSION['close_right'] == 1) {
        mysql_query(
            "UPDATE messages
                                                         SET   msg_status = 'close'
                                                         WHERE msg_id     = '" . $_POST['msg_id'] . "'"
        );

        $msg_sent = 1;
        $msg_content = "The message has been closed.";
    } else {
        $righterror = 1;
    }
}

// Answer system
if ($_POST && isset($_POST["msg_answer"])) {
    //$ticket_id = htmlspecialchars($_POST["ticket_id"]);
    $ans_ticket_id = htmlspecialchars($_POST["ans_ticket_id"]);
    $msg_ticket_id = htmlspecialchars($_POST["msg_ticket_id"]);


    $id_msg = intval($_POST["msg_id"]);
    $email_msg = mysql_real_escape_string($_POST["msg_email"]);
    $content_msg = mysql_real_escape_string($_POST["msg_answer"]);

    $to = $email_msg;
    $support_link = $pageURL . 'comments.php?ticket_id=';
    $subject = 'New answer from our support team';
    $message = 'Hello,<br><br>A new answer has been added to your message. Please follow this link to read it and to answer : <br><br><a href="' . $support_link . $msg_ticket_id . '">' . $support_link . $msg_ticket_id . '</a><br /><br />Thank you.';
    $headers = 'From: ' . $config->responderMail . "\r\n" .
        'Reply-To: ' . $config->responderMail . "\r\n";
    $headers .= 'Content-Type: text/html; charset="iso-8859-1"' . "\n";
    $headers .= 'Content-Transfer-Encoding: 8bit';

    mail($to, $subject, $message, $headers);

    $accountIdReq = mysql_query(
        "SELECT id FROM accounts
                                                                 WHERE username = '" . $_SESSION['username'] . "'"
    ) or die(mysql_error());

    $accountId = mysql_fetch_object($accountIdReq);

    // We insert the answer in the database
    mysql_query(
        "INSERT INTO answers
                                                 SET
                                                 a_msg_id = " . $id_msg . ",
                                         a_date = NOW(),
                                         a_email = '" . $_SESSION["email"] . "',
                                         a_content = '" . $content_msg . "',
                                         a_account = '" . $accountId->id . "', 
										 ans_ticket_id = '" . $ans_ticket_id . "'"
    );

    mysql_query("UPDATE messages SET msg_status = 'pending' WHERE msg_id = " . $id_msg);

    $msg_sent = 1;
    $msg_content = "Your answer has been well sent.";

}

?>


<h3>Administration</h3>
<a class="btn" style="margin-bottom: 15px; float:right;" href="./logout.php">Log out</a>
<ul class="nav nav-tabs">
    <?php
    $_SESSION['admin'] = false;
    $nPriority = '0';
    $admin = mysql_query("SELECT username FROM accounts limit 1");
    $admin_id = mysql_fetch_object($admin);
    if ($_SESSION['username'] == $admin_id->username) {
        $_SESSION['admin'] = true;
    }


    $_POST['csrf_token'] = $_SESSION['csrf_token_all'];
    if (isset($_SESSION['username'], $_SESSION['csrf_csrf_token'])) {

        try {
            // Run CSRF check, on POST data, in exception mode, for 60 minutes, in multiple mode.
            NoCSRF::check('csrf_token', $_POST, true, 60 * 60, true);

        } catch (Exception $e) {
            // CSRF attack detected
            $error = 2;
            $CSRF_error = $e->getMessage();
        }

    }


    createMenu(1, $_SESSION['admin']);
    ?>
</ul>

<div class="tab-content">

<div class="row">
    <div class="span12">
        <div class="pull-left">
            <form class="form-inline" method="post" action="admin.php?order=<?php echo $order ?>">

                <select name="state" class="state span2">
                    <option value="all" <?php if ($state == "all") {
                        echo 'selected="selected"';
                    } ?>>All states
                    </option>
                    <option value="open" <?php if ($state == "open") {
                        echo 'selected="selected"';
                    } ?>>Open
                    </option>
                    <option value="pending" <?php if ($state == "pending") {
                        echo 'selected="selected"';
                    } ?>>Pending
                    </option>
                    <option value="close" <?php if ($state == "close") {
                        echo 'selected="selected"';
                    } ?>>Close
                    </option>
                </select>

                <select name="priority" class="state span2">
                    <option value="all" <?php if ($priority == "all") {
                        echo 'selected="selected"';
                    } ?>>All priorities
                    </option>
                    <option value="urgent" <?php if ($priority == "urgent") {
                        echo 'selected="selected"';
                        $nPriority = 1;
                    } ?>>Critical
                    </option>
                    <option value="high" <?php if ($priority == "high") {
                        echo 'selected="selected"';
                        $nPriority = 2;
                    } ?>>High
                    </option>
                    <option value="medium" <?php if ($priority == "medium") {
                        echo 'selected="selected"';
                        $nPriority = 3;
                    } ?>>Normal
                    </option>
                    <option value="low" <?php if ($priority == "low") {
                        echo 'selected="selected"';
                        $nPriority = 4;
                    } ?>>Low
                    </option>
                </select>

                <select name="division" class="state span2">
                    <option value="all" <?php if ($division == "all") {
                        echo 'selected="selected"';
                    } ?>>All divisions
                    </option>
                    <?php
                    foreach ($config->divisions->division as $divisionC) {
                        echo "<option value='" . $divisionC . "'";
                        if ($divisionC == $division) {
                            echo " selected ";
                        }
                        echo ">" . $divisionC . "</option>";
                    }
                    ?>
                </select>

                <?php if ($config->productOption == "yes") { ?>
                    <select name="product" class="state span2">
                        <option value="all" <?php if ($product == "all") {
                            echo 'selected="selected"';
                        } ?>>All products
                        </option>
                        <?php
                        foreach ($config->products->product as $productC) {
                            echo "<option value= '" . $productC . "'";
                            if ($productC == $product) {
                                echo " selected ";
                            }
                            echo ">" . $productC . "</option>";
                        }
                        ?>
                    </select>
                <?php } ?>

                <select name="page" class="state span2">
                    <?php
                    for ($i = 1; $i <= $nbPages; $i++) {
                        echo "<option value=" . $i;
                        if ($i == $actualPage) {
                            echo " selected ";
                        }
                        echo ">Page " . $i . "</option>";
                    }
                    ?>
                </select>

            </form>
        </div>

        <div class="pull-right">
            <form action="admin.php?order=<?php echo $order ?>" id="search" class="form-inline" method="post">
                <div class="input-append">
                    <input name="searchEmail" placeholder="Email address" class="span2 searchEmail"
                           id="appendedInputButtons" type="text">
                    <button class="btn submit" type="button" name="emailSearch" style="width:90px;">By email</button>
                </div>
                <div class="input-append">
                    <input name="searchId" placeholder="Ticket number" class="span2 searchId" id="appendedInputButtons"
                           type="text">
                    <button class="btn submit" type="button" name="idSearch" style="width:90px;">By ID</button>
                </div>
                <input type="hidden" name="searchType" id="searchType" value=""/>

            </form>

        </div>
    </div>
</div>

<div style="display:block;">

    <?php
    $req = "SELECT msg_id, msg_ticket_id, msg_date, msg,update_date, msg_subject, msg_name, msg_email, msg_priority, msg_division, msg_content, msg_phone, msg_product, msg_status, msg_spam ";
    $req .= "FROM messages WHERE 1=1 ";
    if ($state != "all") {
        $req .= "AND msg_status = '" . $state . "' ";
    }
    if ($priority != "all") {
        $req .= "AND msg_priority = " . $nPriority . " ";
    }
    if ($division != "all") {
        $req .= "AND msg_division = '" . $division . "' ";
    }
    if ($product != "all") {
        $req .= "AND msg_product = '" . ucfirst($product) . "' ";
    }
    if ($config->spamshow == "no") {
        $req .= "AND msg_spam = 0 ";
    }
    if ($searchEmail != '') {
        $req .= "AND msg_email = '" . ucfirst($searchEmail) . "' ";
    }
    if ($searchId != '') {
        $req .= "AND msg_id = '" . ucfirst($searchId) . "' ";
    }

    if ($order == "asc") {
        $req .= "ORDER BY msg_update_date ";
    } else {
        $req .= "ORDER BY msg_update_date DESC ";
    }


    $first = ($actualPage - 1) * $nbParPage;
    $req .= "LIMIT " . $first . ", " . $nbParPage;

    $messages = mysql_query($req);

    ?>

    <?php
    if ($msg_sent) {
        echo "<div class='alert alert-success'>" . ($msg_content) . "</div>";
    }

    if (isset($righterror)) {
        echo "<div class='alert alert-error'>You're not allowed to do that</div>";
    }
    if (isset($error) && $error = 2) {
        echo "<div class='alert alert-error'>" . $CSRF_error . "</div>";
        exit;
    }
    ?>
</div>

<div class="row"
     style="text-align: center; font-weight:bold; height: 34px; line-height: 34px; border-top: 1px solid <?php echo $config->inputtextcolor ?>; border-bottom: 1px solid <?php echo $config->inputtextcolor ?>; margin-top: 40px;">
    <div class="span1">
        <?php
        if ($order == "desc") {
            echo '<a href="admin.php?order=asc' . '&amp;state=' . $state . '">Oldest</a>';
        } else {
            echo '<a href="admin.php?order=desc' . '&amp;state=' . $state . '">Newest</a>';
        }
        ?></div>

    <div class="span3">Subject</div>
    <div class="span2">Date</div>
    <?php if ($config->productOption == "yes") { ?>
        <div class="span2">Product</div><?php } ?>
    <div class="span1">Division</div>
    <div class="span1">Status</div>
    <div class="span1">Priority</div>
    <div class="span1">Action</div>
</div>

<?php while ($message = mysql_fetch_object($messages)) { ?>
    <div class="row description">

        <div class="span1 openable" style="text-align:center;">
            <a href="#"
               style="text-decoration:none;color:<?php echo $config->inputtextcolor ?>;font-weight: bold; font-size: 20px; width: 10px; margin: 0 auto; display:block;transition: all 500ms ease-in-out 0s;">+</a>
        </div>

        <div class="span3"><?php echo substr(htmlspecialchars($message->msg_subject), 0, 35) ?></div>
        <div class="span2"><?php echo htmlspecialchars(getDataDiff($message->msg_date))
            ?></div>
        <?php if ($config->productOption == "yes") { ?>
            <div class="span2"><?php echo htmlspecialchars($message->msg_product) ?></div><?php } ?>
        <div class="span1"><?php echo htmlspecialchars($message->msg_division) ?></div>
        <div class="span1">

            <?php
            $msg_status = ucfirst(htmlspecialchars($message->msg_status));
            $msg_spam = $message->msg_spam;
            if ($msg_spam == 1) {
                $msg_status = 'Spam';
            }

            switch ($msg_status) {
                case "Open" :
                    echo '<span class="label label-important">';
                    break;
                case "Pending" :
                    echo '<span class="label label-success">';
                    break;
                case "Close" :
                    echo '<span class="label label-info">';
                    break;
                case "Spam" :
                    echo '<span class="label label-default">';
                    break;
            }


            echo htmlspecialchars($msg_status) . "</span>";
            ?>


        </div>
        <div class="span1">
            <?php
            switch ($message->msg_priority) {
                case 4 :
                    echo '<span class="label label-info">';
                    break;
                case 3 :
                    echo '<span class="label label-success">';
                    break;
                case 2 :
                    echo '<span class="label label-warning">';
                    break;
                case 1 :
                    echo '<span class="label label-important">';
                    break;
            }

            echo htmlspecialchars(setPriority($message->msg_priority)) . "</span>";
            ?>
        </div>
        <div class="span1">
            <a href="admin.php?action=delete&msg_id=<?php echo intval($message->msg_id) ?>">
                <img src="images/cross.png"/>
            </a>
        </div>
    </div>
    <div class="row details"
         style="padding-top: 10px; display:none; background-color: <?php echo $config->inputcolor ?>;">


        <div class="span10 offset1">
            <div class="alert alert-info" style="margin-left:-4px;">
                <?php
                $division = ($message->msg_division == "" ? "-" : $message->msg_division);
                $product = ($message->msg_product == "" ? "-" : $message->msg_product);
                $phone = ($message->msg_phone == "" ? "-" : $message->msg_phone);

                echo "<table width=100%><tr><td width='50%'><ul>
                                    
									<li><strong>Subject</strong> : " . htmlspecialchars($message->msg_subject) . "</li>
									<li><strong>Name</strong> : " . htmlspecialchars($message->msg_name) . "</li>
									<li><strong>Email</strong> : " . htmlspecialchars($message->msg_email) . "</li>
                                    <li><strong>Phone</strong> : " . htmlspecialchars($phone) . "</li>
                                  </ul></td>
								  
								  <td width='50%'>
								  <ul>
                                    <li><strong>Msg Id</strong> : " . htmlspecialchars($message->msg_id) . "</li>
									<li><strong>Date</strong> : " . htmlspecialchars($message->msg_date) . "</li>
                                    <li><strong>Division</strong> : " . htmlspecialchars($division) . "</li>
                                    <li><strong>Product</strong> : " . htmlspecialchars($product) . "</li>
									</ul></tr></td>
								  
								  </table>";

                ?>
            </div>
        </div>


        <div class="span5 offset1">

            <?php

            echo "	<div class='row'>
                                <div class='conversRight' style='padding-top:10px;margin-left:-4px;'>
                                    <div class='span1' style=''>" . get_gravatar($message->msg_email) . "</div>
                                    <div class='span'> 
									<div style='font-weight: bold; border-bottom: 1px dashed silver; margin-bottom: 5px; line-height: 25px;'>By: " . $message->msg_email . "</div><blockquote style='min-height:80px;'>" . quoted_printable_decode(
                    $message->msg_content
                ) . "</blockquote>    </div>  </div>  </div>";

            // show attachments
            $upload_dir = wp_upload_dir(); 
   	        $savefilepath =  $upload_dir['basedir']  . '/b1st/' . $message->msg_ticket_id;
	
            if (is_dir($savefilepath)) {
                echo '<span class="label label-info">Attachments</span> <div class="alert alert-default" style="margin-left:-4px;">';

                $attach_files = scandir($savefilepath . "/");
                
				$upload_dir = wp_upload_dir(); 
   	            $savefilepath2 =  $upload_dir['baseurl']  . '/b1st/' . $message->msg_ticket_id . "/";
				

                for ($i = 2; $i < count($attach_files); $i++) {
                    echo "<a href ='$savefilepath2$attach_files[$i]' target='_blank'>$attach_files[$i]</a> ";
                }

                if (count(
                        $attach_files
                    ) == 2
                ) {
                    echo "This email has an attachment which was removed due to being suspicious to be infected";
                }

                echo "</div>";

            }


            $req = "SELECT a_content , a_email, a_date, ans_ticket_id FROM answers WHERE a_msg_id ='" . $message->msg_id . "' order by a_date DESC"; // AND a_account = accounts.id ";
            //echo $req;
            $answers = mysql_query($req) or die(mysql_error());

            while ($answer = mysql_fetch_object($answers)) {

                echo "	<div class='row'>
                                    <div class='conversRight' style='padding-top:10px;margin-left:-4px;'>
                                    <div class='span1' style=''>" . get_gravatar($answer->a_email) . "</div>
                                    <div class='span'> 
									<div style='font-weight: bold; border-bottom: 1px dashed silver; margin-bottom: 5px; line-height: 25px;'>By: " . $answer->a_email . "</div><blockquote style='min-height:80px;'>" . quoted_printable_decode(
                        $answer->a_content
                    ) . "</blockquote>  </div></div></div>   ";

                // show attachments
                $upload_dir = wp_upload_dir(); 
   	            $savefilepath =  $upload_dir['basedir']  . '/b1st/' . $answer->ans_ticket_id;
			
                if (is_dir($savefilepath)) {
                    echo '<span class="label label-info">Attachments</span> <div class="alert alert-default" style="margin-left:-4px;">';

                    $attach_files = scandir($savefilepath . "/");
                    
					$upload_dir = wp_upload_dir(); 
   	                $savefilepath2 =  $upload_dir['baseurl']  . '/b1st/' . $answer->ans_ticket_id . "/";

                    for ($i = 2; $i < count($attach_files); $i++) {
                        echo "<a href ='$savefilepath2$attach_files[$i]' target='_blank'>$attach_files[$i]</a> ";
                    }

                    if (count(
                            $attach_files
                        ) == 2
                    ) {
                        echo "This email has an attachment which was removed due to being suspicious to be infected";
                    }
                    echo "</div>  ";
                }


            }
            ?>


            <form method="post" action="<?php echo 'admin.php?order=' . $order . '&amp;state=' . $state; ?>">
                <input type="hidden" value="<?php echo htmlspecialchars($message->msg_id) ?>" name="msg_id">
                <input type="hidden" value="<?php echo htmlspecialchars($message->msg_ticket_id) ?>" name="ticket_id">
                <input type="hidden" name="status_edit" value="close"/>

                <div class="form-inline" style="margin-bottom: 10px; text-align: center;">
                    <?php if ($message->msg_status != 'close') { ?>
                        <button type="submit" class="btn btn-danger">Close this message</button>
                    <?php } ?>
                </div>
            </form>
        </div>

        <div class="span5">
            <?php if ($message->msg_status != 'close') {
                $ans_ticket_id = md5(rand() . $message->msg_email);
                ?>

                <form id="uploadForm" enctype="multipart/form-data" action="upload.php" target="uploadFrame"
                      method="post">
                    <div class="">

                        <input type="hidden" id="ticketid" name="ticketid" value="<?php echo $ans_ticket_id; ?>"/>
                        <input id="uploadFile" name="uploadFile" type="file"/>
                        <iframe type="hidden" id="uploadFrame" name="uploadFrame" src="#"></iframe>


                        <input type="submit" id="upload-attachment" value="upload" class="btn btn-success"
                               style="margin-top: 12px;"/>
                    </div>

                </form>


                <form method="post" action="admin.php" style="text-align:center;">
                    <textarea cols="6" style="height:120px;" name="msg_answer" placeholder="Your answer"></textarea>
                    <br/>
                    <input type="hidden" value="<?php echo $message->msg_email ?>" name="msg_email"/>
                    <input type="hidden" value="<?php echo intval($message->msg_id) ?>" name="msg_id">
                    <input type="hidden" value="<?php echo htmlspecialchars($ans_ticket_id) ?>"
                           name="ans_ticket_id">
                    <input type="hidden" value="<?php echo htmlspecialchars($message->msg_ticket_id) ?>"
                           name="msg_ticket_id">
                    <input type="submit" value="Answer" class="btn btn-success" style="margin-top: 12px;"/>


                </form>


            <?php } else { ?>
                <div class="alert" style="margin-top: 20px;">This message is closed.</div>
            <?php } ?>
        </div>

    </div>
<?php } ?>

<?php
} else {
    header('Location: login.php');
} ?>
</div>

<br>
</div>
</div>

<?php wp_enqueue_script('jquery'); ?>
<script type="text/javascript">
    (function ($) {
        $('.description .openable').live("click", function (event) {
            event.preventDefault();
            $(this).parent().next().slideDown();
            //$(this).parent().find('td:first a').html("-");
            $(this).parent().find('a:first').addClass("opened");
            $(this).addClass('closable').removeClass('openable');
        });

        $('.description .closable').live("click", function (event) {
            event.preventDefault();
            $(this).parent().next().slideUp();
            //$(this).parent().find('td:first a').html("+");
            $(this).parent().find('a:first').removeClass("opened");
            $(this).addClass('openable').removeClass('closable');
        });

        $('.state').live("change", function (event) {
            event.preventDefault();
            $(this).parent().submit();
        });

        $('form#search input.searchEmail').keyup(function (e) {
            if (e.keyCode == 13) {
                $("form#search input#searchType").val("emailSearch");
                $("form#search").submit();
            }
        });

        $('form#search input.searchId').keyup(function (e) {
            if (e.keyCode == 13) {
                $("form#search input#searchType").val("idSearch");
                $("form#search").submit();
            }
        });

        $("button.submit").click(function () {
            $("form#search input#searchType").val(this.name);
            $("form#search").submit();

        });

    })(jQuery);
</script>

</body>
</html>
