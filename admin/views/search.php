<?php

session_start();

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
</head>
<body>

<?php
$config = simplexml_load_file("config.xml");
// Database
include("includes/config.php");
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

<div class="pull-right">
    <a class="btn" style="margin-bottom: 15px;" href="./admin.php">Back</a>
</div>

<?php
if (isset($_POST['username']) && isset($_POST['password']) && !empty($_POST['username']) && !empty($_POST['password'])) {
    $loginReq = mysql_query(
        "SELECT COUNT(*) as nb
                                                         FROM accounts
                                                         WHERE username = '" . $_POST['username'] . "'
                                                 AND password = '" . $_POST['password'] . "'"
    );

    $nb = mysql_fetch_object($loginReq);

    if ($nb->nb > 0) {
        $_SESSION['username'] = $_POST['username'];
    } else {
        $error = 1;
    }
}

if (isset($_SESSION['username'])) {

    if ($_POST["searchType"] == 'emailSearch') {
        $messages = mysql_query(
            "SELECT msg_id, msg_date, msg_subject, msg_name, msg_email, msg_priority, msg_status, msg_spam, msg_division, msg_product, msg_content, msg_phone
                                                                     FROM messages
                                                                     WHERE msg_email REGEXP '" . mysql_real_escape_string(
                $_POST['searchEmail']
            ) . "'
                                                         ORDER BY msg_date"
        );
    } else {
        $messages = mysql_query(
            "SELECT msg_id, msg_date, msg_subject, msg_name, msg_email, msg_priority, msg_status, msg_spam, msg_division, msg_product, msg_content, msg_phone
                                                                     FROM messages
                                                                     WHERE msg_id = " . intval($_POST['searchId']) . "
                                                         ORDER BY msg_date"
        );


    }
    ?>


    <h3>Search results</h3>






    <div class="row"
         style="text-align: center; font-weight:bold; height: 34px; line-height: 34px; border-top: 1px solid <?php echo $config->inputtextcolor ?>; border-bottom: 1px solid <?php echo $config->inputtextcolor ?>; margin-top: 40px;">
        <div class="span1">
            <?php
            // if ( $order == "desc" ) { echo '<a href="admin.php?order=asc' . '&amp;state=' . $state . '">Oldest</a>';
            // } else { echo '<a href="admin.php?order=desc' . '&amp;state=' . $state . '">Newest</a>';}
            ?></div>

        <div class="<?php if ($config->productOption == "yes") { ?>span2<?php } else { ?>span4<?php } ?>">Subject</div>
        <div class="span2">Date</div>
        <?php if ($config->productOption == "yes") { ?>
            <div class="span2">Product</div><?php } ?>
        <div class="span2">Division</div>
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

            <div
                class="<?php if ($config->productOption == "yes") { ?>span2<?php } else { ?>span4<?php } ?>"><?php echo htmlspecialchars(
                    $message->msg_subject
                ) ?></div>
            <div class="span2"><?php echo htmlspecialchars($message->msg_date) ?></div>
            <?php if ($config->productOption == "yes") { ?>
                <div class="span2"><?php echo htmlspecialchars($message->msg_product) ?></div><?php } ?>
            <div class="span2"><?php echo htmlspecialchars($message->msg_division) ?></div>
            <div class="span1">

                <?php
                $msg_status = ucfirst(htmlspecialchars($message->msg_status));
                if ($message->msg_spam == 1) {
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
                    case "Low" :
                        echo '<span class="label label-info">';
                        break;
                    case "Medium" :
                        echo '<span class="label label-success">';
                        break;
                    case "High" :
                        echo '<span class="label label-warning">';
                        break;
                    case "Urgent" :
                        echo '<span class="label label-important">';
                        break;
                }

                echo htmlspecialchars($message->msg_priority) . "</span>";
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
									<div style='font-weight: bold; border-bottom: 1px dashed silver; margin-bottom: 5px; line-height: 25px;'>By: " . $message->msg_email . "</div><blockquote style='min-height:80px;'>" . htmlspecialchars(
                        $message->msg_content
                    ) . "</blockquote>    </div>  </div>  </div>";


                $req = "SELECT a_content , a_email, a_date FROM answers WHERE a_msg_id ='" . $message->msg_id . "' order by a_date DESC"; // AND a_account = accounts.id ";
                //echo $req;
                $answers = mysql_query($req) or die(mysql_error());

                while ($answer = mysql_fetch_object($answers)) {

                    echo "	<div class='row'>
                                    <div class='conversRight' style='padding-top:10px;margin-left:-4px;'>
                                    <div class='span1' style=''>" . get_gravatar($answer->a_email) . "</div>
                                    <div class='span'> 
									<div style='font-weight: bold; border-bottom: 1px dashed silver; margin-bottom: 5px; line-height: 25px;'>By: " . $answer->a_email . "</div><blockquote style='min-height:80px;'>" . htmlspecialchars(
                            $answer->a_content
                        ) . "</blockquote>    </div>  </div>  </div>";


                    //echo "<div class='span1' style=''>". get_gravatar($answer->a_email). '</div><b>' . $answer->username . '</b> answered : (' . $answer->a_date . ') <blockquote style="height:80px;">' . $answer->a_content . '</blockquote>';

                }
                ?>


                <form method="post" action="<?php echo 'admin.php?order=' . $order . '&amp;state=' . $state; ?>">
                    <input type="hidden" value="<?php echo htmlspecialchars($message->msg_id) ?>" name="msg_id">
                    <input type="hidden" value="<?php echo htmlspecialchars($message->msg_ticket_id) ?>"
                           name="ticket_id">
                    <input type="hidden" name="status_edit" value="close"/>

                    <div class="form-inline" style="margin-bottom: 10px; text-align: center;">
                        <?php if ($message->msg_status != 'close') { ?>
                            <button type="submit" class="btn btn-danger">Close this message</button>
                        <?php } ?>
                    </div>
                </form>
            </div>

            <div class="span5">
                <?php if ($message->msg_status != 'close') { ?>
                    <form method="post" action="admin.php" style="text-align:center;">
                        <textarea cols="6" style="height:120px;" name="msg_answer" placeholder="Your answer"></textarea>
                        <br/>
                        <input type="hidden" value="<?php echo $message->msg_email ?>" name="msg_email"/>
                        <input type="hidden" value="<?php echo intval($message->msg_id) ?>" name="msg_id">
                        <input type="hidden" value="<?php echo htmlspecialchars($message->msg_ticket_id) ?>"
                               name="ticket_id">
                        <input type="submit" value="Answer" class="btn btn-success" style="margin-top: 12px;"/>
                    </form>
                <?php } else { ?>
                    <div class="alert" style="margin-top: 20px;">This message is closed.</div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>

<?php } else { ?>
    <h3>Please, log in</h3>

    <?php
    if (isset($error) && $error == 1) {
        echo '<div class="alert alert-error">Bad informations : you are not logged in.</div>';
    }
    ?>

    <form class="form-inline" method="post" action="admin.php">
        <input type="text" class="input-large" name="username" placeholder="Username">
        <input type="password" class="input-large" name="password" placeholder="Password">
        <button type="submit" class="btn">Sign in</button>
    </form>

<?php } ?>
</div>

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
