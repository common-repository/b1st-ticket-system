<?php
$this->require_once_includes('nocsrf');
$this->require_once_includes('scan');
$this->include_includes('functions');
?>

<script>
    jQuery(function() {
        jQuery(".collapse").click(function(e) {
            e.preventDefault();
            jQuery(this).parent().parent().next().fadeToggle('slow');
        });
    });
</script>
<style>
    .collapse-div {transition: 1s display;}
    .collapse-div:before {display: none !important;}
    .innertable-rating, .innertable-rating tr, .innertable-rating td {border: none !important;}
</style>

 <?php  $this->SetMessage(1) ; ?> 
 
 <style>
    .rating {
        unicode-bidi: bidi-override;
        direction: rtl;
        position: absolute;
        right: 0px;
        bottom: 0px;
        margin: 0;
    }
    .rating a {
        text-decoration: none !important;
    }
    .rating > a {
        display: inline-block;
        position: relative;
        width: 1.1em;
        color: #FFF !important;
    }
    .rating > a.selected:before,
    .rating > a.selected ~ a:before {
        content: "\2605";
        position: absolute;
        color: rgb(255, 133, 0) !important;
    }
    .rating a.selected {
        color: rgb(255, 133, 0) !important;
    }
</style>
<?php
global $wpdb;
$faqs = '<div class="bootstrap"><h4>' . __('FAQs', 'ticketsys') . '</h4><form><ul style="margin-left: 0;">';
$faq_product = "";
if (isset($_GET['faq_product']) && isset($_GET['ajaxy']))
    $faq_product = "WHERE product = '" . $_GET['faq_product'] . "'";
$results = $wpdb->get_results("SELECT * FROM {$dbprefix}faqs " . $faq_product);
if (!$results)
    $results = $wpdb->get_results("SELECT * FROM {$dbprefix}faqs ORDER BY product");

$products = array();
if (self::$INIT_TIME < 0 ) {
foreach ($results as $faq) {
    $product = "";
    if (isset($faq->product)) {
        if (!in_array($faq->product, $products)) {
            $product = $faq->product;
            array_push($products, $faq->product);
        }
    }
    if ($product != "")
        $faqs .= "<h4>" . ucfirst($product) . "</h4>";
    $faqs .= '<li><input type="radio" value="' . $faq->reply . '" name="faqid"  id="faq-' . $faq->id . '" /><label style="display: inline-block;margin: 4px 5px 0;" class="faqq" for="faq-' . $faq->id . '">' . $faq->message . '</label><span class="faqa" style="display: block;">' . $faq->reply . '</span></li>';
}
}
$faqs .= '</ul><a href="javascript:void()" id="faq-done" class="avgrund-close btn-success btn">' . __('Done', 'ticketsys') . '</a></form></div>';
echo '<div id="faq_popupi" style="display: none">' . $faqs . '</div>';
?>

<link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__); ?>../../public/assets/css/avgrund.css">
<script src="<?php echo plugin_dir_url(__FILE__); ?>../../public/assets/js/jquery.avgrund.js"></script>

<script>
    jQuery(function($) {
        var ticket, product;
        $('.show').avgrund({
            height: 350,
            showClose: false,
            showCloseText: 'Close',
            template: '<div id="faq_popup" style="position: relative;"></div>',
            holderClass: false,
            overlayClass: '',
            onLoad: function(e) {
                setTimeout(function() {
                    $("#faq_popup").empty();
                    $("#faq_popup").append('<img src="<?php echo self::$PATH_IMAGES; ?>ajax-loader.gif" style="position: absolute;left: 0;right: 0;bottom: 0;top: 0;margin: 0 auto;" />');
                    $url = '<?php site_url(); ?>/wp-admin/admin.php?page=ticketsys-messages-settings&ajaxy=true&faq_product=' + product;
                    var j = $.ajax({
                        url: $url,
                        datatype: "HTML",
                        error: function(error) {
                            //alert("Error: "+error);
                            return;
                        },
                        success: function(e) {
                            $("#faq_popup").html($(e).find("#faq_popupi").html());
                        }
                    });
                }, 100);
            },
            onUnload: function(e) {
                $v = $("input[name=faqid]:checked").val();
                ticket.val($v);
            },
            enableStackAnimation: false
        });

        $(".show").click(function(e) {
            e.preventDefault();
            ticket = $(this).parent().children("textarea[name=msg_answer]");
            product = $(this).attr('data-product');
        });

    });
</script>

<?php

function get_gravatar($email, $s = 80, $d = 'mm', $r = 'g', $img = true, $atts = array()) {
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


<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">

            <?php
            $msg_sent = "";
            $msg_content = "";
            $pageURL = dirname("http://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]) . "/";


            if (isset($_GET["action"]) && isset($_GET["msg_id"])) {
                if ($_GET["action"] == "delete") {
                    if ($_SESSION['delete_right'] == 1) {
                        $msg_id = intval($_GET["msg_id"]);

                        // Verify if the message exists
                        $messageById = $wpdb->query("SELECT Count(msg_id) FROM {$dbprefix}messages WHERE msg_id = " . $msg_id);

                        // Ok
                        if ($wpdb->num_rows > 0) {
                            $wpdb->query("DELETE FROM {$dbprefix}messages WHERE msg_id = " . $msg_id);
                            $wpdb->query("DELETE FROM {$dbprefix}answers WHERE a_msg_id = " . $msg_id);
                            $msg_sent = 1;
                            $msg_content = __('Your message has been well deleted', 'ticketsys');
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
            $nbParPage = intval($nbParPage . '');


            $totalreq = "SELECT COUNT(*) AS total FROM {$dbprefix}messages";
            $total = $wpdb->get_col($totalreq);

            $nbPages = ceil($total[0] / $nbParPage);

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
                    $wpdb->query(
                            "UPDATE {$dbprefix}messages
                                                         SET   msg_status = 'close'
                                                         WHERE msg_id     = '" . $_POST['msg_id'] . "'"
                    );

                    $msg_sent = 1;
                    $msg_content = __('The message has been closed.', 'ticketsys');
                } else {
                    $righterror = 1;
                }
            }

// Answer system
            if ($_POST && isset($_POST["msg_answer"])) {
                $ticket_id = htmlspecialchars($_POST["ticket_id"]);
                $id_msg = intval($_POST["msg_id"]);
                $email_msg = addslashes($_POST["msg_email"]);
                $content_msg = addslashes($_POST["msg_answer"]);

                $to = $email_msg;
                $secret = "MaA@^&13aLEx99";
                $token = sha1($ticket_id . $secret);
                $support_link = get_site_url() . '/?ticketsys_id=' . $ticket_id . "&token=" . $token;
                $subject = __('New answer from our support team', 'ticketsys');
                $message = sprintf(__('Hello,<br><br>A new answer has been added to your message. Please follow this link to read it and to answer : <br><br><a href="%1$s">%2$s</a><br /><br />Thank you.', 'ticketsys'), $support_link, $support_link);
                $headers = 'From: ' . $config->accEmail . "\r\n" .
                        'Reply-To: ' . $config->accEmail . "\r\n";
                $headers .= 'Content-Type: text/html; charset="iso-8859-1"' . "\n";
                $headers .= 'Content-Transfer-Encoding: 8bit';
               trigger_error($support_link, E_USER_NOTICE);
                $send = mail($to, $subject, $message, $headers);
                // echo $support_link;
                // a.a_email!='$email_msg' AND
                $sql = "SELECT DISTINCT a.a_date, m.msg_id FROM {$dbprefix}messages m JOIN {$dbprefix}answers a ON m.msg_id=a.a_msg_id  WHERE m.msg_id ='" . $id_msg . "' order by a.a_date";
                foreach ($wpdb->get_results($sql) as $res)
                    $a_msg_date = $res->a_date;


                // We insert the answer in the database
                $now = date('U');

                $a_response = $now - $a_msg_date;

                $wpdb->query(
                        "INSERT INTO {$dbprefix}answers
                                                 SET
                                                 a_msg_id = " . $id_msg . ",
                                         a_date = '$now',
										 a_msg_date = '$a_msg_date',
										 a_response = '$a_response',
                                         a_email = '" . $_SESSION["email"] . "',
                                         a_content = '" . $content_msg . "',
                                         ans_ticket_id = '" . $_POST['answerId'] . "',
                                         a_account = '" . $_SESSION["id"] . "'"
                );

                $now = date('U');
                $wpdb->query("UPDATE {$dbprefix}messages SET msg_status = 'pending', msg_update_date = '$now' WHERE msg_id = " . $id_msg);

                $msg_sent = 1;
                $msg_content = __('Your answer has been well sent.', 'ticketsys');
                if (isset($_FILES)) {

                    $filename = null;
                    $status = 0;
                    if (isset($_FILES['uploadFile']) && !empty($_FILES['uploadFile']['name'][0])) {
                        $files = $_FILES['uploadFile'];

                        $savefilepath = self::$PATH_ATTACHEMENTS . $ticket_id;

                        if (isset($_POST['answerId'])) {
                            if (!file_exists($savefilepath)) {
                                mkdir($savefilepath);
                                chmod($savefilepath, 777);
                            }
                            $answerid = $_POST['answerId'];
                            $savefilepath .= '/' . $answerid;
                        }

                        mkdir($savefilepath);
                        chmod($savefilepath, 777);
                        $tempArr = array();
                        $count = 0;
                        $max = count($files['name']);
                        $limit = $config->maxuploads;
                        while ($count < $max && $count < $limit) {
                            $temp['name'] = $files['name'][$count];
                            $temp['type'] = $files['type'][$count];
                            $temp['tmp_name'] = $files['tmp_name'][$count];
                            $temp['error'] = $files['error'][$count];
                            $temp['size'] = $files['size'][$count];
                            array_push($tempArr, $temp);
                            $count++;
                        }
                        foreach ($tempArr as $key => $file) {
                            $filename = $file['name'];
                            $targetpath = $savefilepath . '/' . $file['name'];
                            if (@move_uploaded_file($file['tmp_name'], $targetpath)) {
                                $status = 2;

                                $scan_res = scanFile($config->metascan, $targetpath);
                                if ($scan_res != 0 && $scan_res != 4 && $scan_res != 7) {
                                    unlink($targetpath);
                                }
                            } else {
                                $status = 3;
                                break;
                            }
                        }
                    }
                }
            }
            ?>


            <h3><?php _e('Administration', 'ticketsys'); ?></h3>
            <ul class="nav nav-tabs">
                <?php
                echo wp_enqueue_script('jquery');
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

            <div class="">
			      <div class="row-fluid">
                    <div class="span12">
                        <div>
                            <form class="form-inline" method="post" action="<?php $this->renderAction("order=" . $order); ?>">

                                <select name="state" class="state span2">
                                    <option value="all" <?php
                                    if ($state == "all") {
                                        echo 'selected="selected"';
                                    }
                                    ?>><?php _e('All states', 'ticketsys'); ?>
                                    </option>
                                    <option value="open" <?php
                                    if ($state == "open") {
                                        echo 'selected="selected"';
                                    }
                                    ?>><?php _e('Open', 'ticketsys'); ?>
                                    </option>
                                    <option value="pending" <?php
                                    if ($state == "pending") {
                                        echo 'selected="selected"';
                                    }
                                    ?>><?php _e('Pending', 'ticketsys'); ?>
                                    </option>
                                    <option value="close" <?php
                                    if ($state == "close") {
                                        echo 'selected="selected"';
                                    }
                                    ?>><?php _e('Close', 'ticketsys'); ?>
                                    </option>
                                </select>

                                <select name="priority" class="state span2">
                                    <option value="all" <?php
                                    if ($priority == "all") {
                                        echo 'selected="selected"';
                                    }
                                    ?>><?php _e('All priorities', 'ticketsys'); ?>
                                    </option>
                                    <option value="urgent" <?php
                                    if ($priority == "urgent") {
                                        echo 'selected="selected"';
                                        $nPriority = 1;
                                    }
                                    ?>><?php _e('Critical', 'ticketsys'); ?>
                                    </option>
                                    <option value="high" <?php
                                    if ($priority == "high") {
                                        echo 'selected="selected"';
                                        $nPriority = 2;
                                    }
                                    ?>><?php _e('High', 'ticketsys'); ?>
                                    </option>
                                    <option value="medium" <?php
                                    if ($priority == "medium") {
                                        echo 'selected="selected"';
                                        $nPriority = 3;
                                    }
                                    ?>><?php _e('Normal', 'ticketsys'); ?>
                                    </option>
                                    <option value="low" <?php
                                    if ($priority == "low") {
                                        echo 'selected="selected"';
                                        $nPriority = 4;
                                    }
                                    ?>><?php _e('Low', 'ticketsys'); ?>
                                    </option>
                                </select>

                                <select name="division" class="state span2">
                                    <option value="all" <?php
                                    if ($division == "all") {
                                        echo 'selected="selected"';
                                    }
                                    ?>><?php _e('All divisions', 'ticketsys'); ?>
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
                                        <option value="all" <?php
                                        if ($product == "all") {
                                            echo 'selected="selected"';
                                        }
                                        ?>><?php _e('All products', 'ticketsys'); ?>
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
                                        echo ">" . sprintf(__('Page %d', 'ticketsys'), $i) . "</option>";
                                    }
                                    ?>
                                </select>

                            </form>
                        </div>

                        <div>
                            <form action="<?php $this->renderAction('order=' . $order) ?>" id="search" class="form-inline" method="post">
                                <div class="span5 input-append">
                                    <input name="searchEmail" placeholder="<?php _e('Email address', 'ticketsys'); ?>" class=" searchEmail"
                                           id="appendedInputButtons" type="text">
                                    <button class="btn submit" type="button" name="emailSearch" style="width:90px;"><?php _e('By email', 'ticketsys'); ?></button>
                                </div>
                                <div class="span5 input-append">
                                    <input name="searchId" placeholder="<?php _e('Ticket number', 'ticketsys'); ?>" class=" searchId" id="appendedInputButtons"
                                           type="text">
                                    <button class="btn submit" type="button" name="idSearch" style="width:90px;"><?php _e('By ID', 'ticketsys'); ?></button>
                                </div>
                                <input type="hidden" name="searchType" id="searchType" value=""/>

                            </form>

                        </div>
                    </div>
                </div>

                <div style="display:block;">

                    <?php
                    $req = "SELECT msg_id, msg_ticket_id, msg_date, msg_update_date, msg_subject, msg_name, msg_email, msg_priority, msg_division, msg_content, msg_phone, msg_product, msg_status, msg_spam, msg_rating ";
                    $req .= "FROM {$dbprefix}messages WHERE 1=1 ";
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

                    $messages = $req;
                    ?>

                    <?php
                    if ($msg_sent) {
                        echo "<div class='alert alert-success'>" . ($msg_content) . "</div>";
                    }

                    if (isset($righterror)) {
                        echo "<div class='alert alert-error'>" . __("You're not allowed to do that", 'ticketsys') . "</div>";
                    }
                    if (isset($error) && $error = 2) {
                        echo "<div class='alert alert-error'>" . $CSRF_error . "</div>";
                        exit;
                    }
                    ?>

                </div>

                <div class="row-fluid"
                     style="text-align: center; font-weight:bold; height: 34px; line-height: 34px; border-top: 1px solid <?php echo $config->inputtextcolor ?>; border-bottom: 1px solid <?php echo $config->inputtextcolor ?>; margin-top: 40px;">
                    <div class="span1">
                        <?php
                        if ($order == "desc") {
                            echo "<a href=" . $this->renderAction('order=asc&amp;state=' . $state, null, false) . '">' . __('Oldest', 'ticketsys') . '</a>';
                        } else {
                            echo "<a href=" . $this->renderAction('order=desc&amp;state=' . $state, null, false) . '">' . __('Latest', 'ticketsys') . '</a>';
                        }
                        ?>
                    </div>

                    <div class="span3"><?php _e('Subject', 'ticketsys'); ?></div>
                    <div class="span1"><?php _e('Date', 'ticketsys'); ?></div>
                    <?php if ($config->productOption == "yes") { ?>
                        <div class="span2"><?php _e('Product', 'ticketsys'); ?></div><?php } ?>
                    <div class="span1"><?php _e('Division', 'ticketsys'); ?></div>
                    <div class="span1"><?php _e('Status', 'ticketsys'); ?></div>
                    <div class="span1"><?php _e('Priority', 'ticketsys'); ?></div>
                    <div class="span1"><?php _e('Action', 'ticketsys'); ?></div>
                </div>

                <?php
                $results = $wpdb->get_results($messages);
                foreach ($results as $message) {
                    ?>
                    <div class="row-fluid description">
                        <div class="span1 openable" style="text-align:center;">
                            <a href="javascript:void(0)" style="text-decoration:none;color:<?php echo $config->inputtextcolor ?>;font-weight: bold; font-size: 20px; width: 10px; margin: 0 auto; display:block;transition: all 500ms ease-in-out 0s;">+</a>
                        </div>

                        <div class="span3"><?php echo substr(htmlspecialchars($message->msg_subject), 0, 35) ?></div>
                        <div class="span1"><?php echo time_elapsed($message->msg_update_date);
                    ?></div>
                        <?php if ($config->productOption == "yes") { ?>
                            <div class="span2"><?php echo htmlspecialchars($message->msg_product); ?></div>
                        <?php } ?>

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

                            echo __(htmlspecialchars($msg_status), 'ticketsys') . "</span>";
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

                            echo __(htmlspecialchars(setPriority($message->msg_priority)), 'ticketsys') . "</span>";
                            ?>
                        </div>
                        <div class="span1">
                            <a <?php if($config->deleteconfirm=='yes'){?>onclick="return confirm('<?php echo __('Are you sure you want to delete this item?','ticketsys')?> ')" <?php }?>href="<?php $this->renderAction('delete&action=delete&msg_id=' . intval($message->msg_id)); ?>">
                                <img src="<?php echo self::$PATH_IMAGES; ?>cross.png"/>
                            </a>
                        </div>
                    </div>
                    <div class="row-fluid details"
                         style="padding-top: 10px; display:none; background-color: <?php echo $config->inputcolor; ?>;">


                        <div class="span10 offset1">
                            <div class="alert alert-info" style="margin-left:-4px;">
                                <?php
                                $division = ($message->msg_division == "" ? "-" : $message->msg_division);
                                $product = ($message->msg_product == "" ? "-" : $message->msg_product);
                                $phone = ($message->msg_phone == "" ? "-" : $message->msg_phone);
                                echo ("<table width=100%><tr><td width='50%'><ul>
                                    
									<li><strong>" . __('Subject', 'ticketsys') . "</strong> : " . htmlspecialchars($message->msg_subject) . "</li>
									<li><strong>" . __('Name', 'ticketsys') . "</strong> : " . htmlspecialchars($message->msg_name) . "</li>
									<li><strong>" . __('Email', 'ticketsys') . "</strong> : " . htmlspecialchars($message->msg_email) . "</li>
                                    <li><strong>" . __('Phone', 'ticketsys') . "</strong> : " . htmlspecialchars($phone) . "</li>
                                  </ul></td>
								  
								  <td width='50%'>
								  <ul>
                                    <li><strong>" . __('Msg Id', 'ticketsys') . "</strong> : " . htmlspecialchars($message->msg_id) . "</li>
									<li><strong>" . __('Date', 'ticketsys') . "</strong> : " . htmlspecialchars(date('Y-m-d H:i:s', $message->msg_date)) . "</li>
                                    
                                    <li><strong>" . __('Product', 'ticketsys') . "</strong> : " . htmlspecialchars($product) . "</li>
                                    <li><strong>" . __('Division', 'ticketsys') . "</strong> : " . htmlspecialchars($division) . "</li>
									</ul></tr></td>
								  
								  </table>");
                                ?>
                            </div>
                        </div>


                        <div class="span5 offset1">

                            <?php
                            echo "	<div class='row'>
                                <div class='conversRight' style='padding-top:10px;margin-left:-4px;'>
                                    <div class='span1' style=''>" . get_gravatar($message->msg_email) . "</div>
                                    <div class='span' syyle='max-width:450px;'> 
									<div style='font-weight: bold; border-bottom: 1px dashed silver; margin-bottom: 5px; line-height: 25px;'>" . __('By:', 'ticketsys') . " " . $message->msg_email . "</div><blockquote style='min-height:80px;'>" . quoted_printable_decode(
                                    $message->msg_content
                            ) . "</blockquote>    </div>  </div>  </div>";

                            $savefilepath = self::$PATH_ATTACHEMENTS . $message->msg_ticket_id;
                            if (is_dir($savefilepath)) {


                                $savefilepath2 = sprintf('%s?action=ticketsys-download&ticket=%s&fileName=', get_site_url(), $message->msg_ticket_id);
                                $temp = scandir($savefilepath . "/");

                                $attach_files = array();

                                for ($i = 2; $i < count($temp); $i++) {
                                    if (!is_dir($savefilepath . '/' . $temp[$i])) {
                                        array_push($attach_files, $temp[$i]);
                                    }
                                }

                                if (count($attach_files) > 0) {
                                    echo '<span class="label label-info">' . __('Attachments', 'ticketsys') . '</span> <div class="alert alert-default" style="margin-left:-4px;">';
                                    for ($i = 0; $i < count($attach_files); $i++) {
                                        echo "<a href ='$savefilepath2$attach_files[$i]' target='_blank'>$attach_files[$i]</a> ";
                                    }

                                    echo "</div>";
                                }
                            }


                            $req = "SELECT * FROM {$dbprefix}answers WHERE a_msg_id ='" . $message->msg_id . "' order by a_date ASC"; // AND a_account = accounts.id ";
                            //echo $req;
                            $answers = $req;

                            foreach ($wpdb->get_results($answers) as $answer) {
                                $savechildfilepath = $savefilepath . '/' . $answer->ans_ticket_id;
                                if ($answer->a_account != 0) :
                                    $total_rating = $wpdb->get_col("SELECT stars FROM {$dbprefix}msg_rating WHERE mid = $message->msg_id AND aid = $answer->id")[0];
                                    $rated = 5;
                                    $rating = '<div class="rating">';
                                    for ($i = 0; $i < 5; $i++) {
                                        $select = "";
                                        $link = "#";
                                        $rated--;
                                        if ($total_rating > $rated && $total_rating > 0) {
                                            $select = ' class = "selected"';
                                        }
                                        $rating .= '<a href="' . $link . '"' . $select . '>&#9734;</a>';
                                    }
                                    $rating .= '</div>';
                                else :
                                    $rating = "";
                                endif;

                                echo "	<div class='row'>
                                    <div class='conversRight' style='padding-top:10px;margin-left:-4px;'>
                                    <div class='span1' style=''>" . get_gravatar($answer->a_email) . "</div>
                                    <div class='span'> 
									<div style='position: relative; font-weight: bold; border-bottom: 1px dashed silver; margin-bottom: 5px; line-height: 25px;'><span>" . __('By:', 'ticketsys') . " " . $answer->a_email . "</span>" . $rating . "</div><blockquote style='min-height:80px;'>" . htmlspecialchars(
                                        $answer->a_content
                                ) . "</blockquote>    </div>  </div>  </div>";

                                if (is_dir($savechildfilepath) && !empty($answer->ans_ticket_id)) {

                                    echo '<span class="label label-info">' . __('Attachments', 'ticketsys') . '</span> <div class="alert alert-default" style="margin-left:-4px;">';
                                    $savechildfilepath2 = sprintf('%s?action=ticketsys-download&ticket=%s&answer=%s&fileName=', get_site_url(), $message->msg_ticket_id, $answer->ans_ticket_id);
                                    $attach_files = scandir($savechildfilepath . "/");


                                    for ($i = 2; $i < count($attach_files); $i++) {
                                        echo "<a href ='$savechildfilepath2$attach_files[$i]' target='_blank'>$attach_files[$i]</a> ";
                                    }

                                    if (count($attach_files) == 2) {
                                        //_e('This email has an attachment which was removed due to being suspicious to be infected', 'ticketsys');
                                    }
                                    echo "</div>";
                                }
                            }
                            ?>


                            <form method="post" action="<?php $this->renderAction() ?>">
                                <input type="hidden" value="<?php echo htmlspecialchars($message->msg_id) ?>" name="msg_id">
                                <input type="hidden" value="<?php echo htmlspecialchars($message->msg_ticket_id) ?>" name="ticket_id">
                                <input type="hidden" name="status_edit" value="close"/>

                                <div class="form-inline" style="margin-bottom: 10px; text-align: center;">
                                    <?php if ($message->msg_status != 'close') { ?>
                                        <button type="submit" class="btn btn-danger"><?php _e('Close this message', 'ticketsys'); ?></button>
                                    <?php } ?>
                                </div>
                            </form>
                        </div>

                        <div class="span5" style="position: relative;">
                            <?php
                            if ($message->msg_status != 'close') {
                                $ans_ticket_id = md5(rand() . $message->msg_email);
                                ?>


                                <form method="post" action="<?php $this->renderAction() ?>" enctype="multipart/form-data" style="text-align:center;">
                                    <script>
                                        var $ = jQuery.noConflict();
                                        var count = 1;
                                        var test;
                                        function addFile(event) {
                                            if (count + 1 ><?php echo $config->maxuploads ?>)
                                                return;
                                            test = event;
                                            $(event.parentElement).append('<input <?php $this->SetCability(); ?> class="uploadFile" name="uploadFile[]" type="file"/><a href="javascript:void(0);" onclick="removeFile(this)"><?php _e('Remove', 'ticketsys'); ?></a>');
                                            temp = event.parentElement;
                                            $(event).remove();
                                            $(temp).append(event);

                                            count++;
                                        }
                                        function removeFile(event) {
                                            $(event).prev().remove();
                                            $(event).remove();
                                            count--;

                                        }
                                    </script>  
                                    
									
									<div id="uploads">

                                        <input type="hidden" id="ticketid" name="answerId" value="<?php echo $ans_ticket_id; ?>"/>

                                        <input <?php $this->SetCability(); ?> id="uploadFile" class="uploadFile" name="uploadFile[]" type="file"/>
                                        <a href="javascript:void(0);" onclick="removeFile(this)"><?php _e('Remove', 'ticketsys'); ?></a>


                                        <a style="display:block" href="javascript:void(0);" onclick="addFile(this)"><?php _e('Add File', 'ticketsys'); ?></a>


                                    </div> 
									

                                    <textarea cols="6" style="height:120px;" name="msg_answer" placeholder="<?php _e('Your answer File', 'ticketsys'); ?>"></textarea>
                                    <br/>
                                    <input type="hidden" value="<?php echo $message->msg_email ?>" name="msg_email"/>
                                    <input type="hidden" value="<?php echo intval($message->msg_id) ?>" name="msg_id">
                                    <input type="hidden" value="<?php echo htmlspecialchars($message->msg_ticket_id) ?>"
                                           name="ticket_id">
                                    <input type="submit" value="<?php _e('Answer', 'ticketsys'); ?>" class="btn btn-success" style="margin-top: 12px;"/>
                                    <a href="" data-product="<?php echo htmlspecialchars($product); ?>" id="show" class="show btn btn-success" style="margin-top: 12px; display: inline-block;"><?php _e('FAQs', 'ticketsys'); ?></a>
                                </form>
                            <?php } else { ?>
                                <div class="alert" style="margin-top: 20px;"><?php _e('This message is closed.', 'ticketsys'); ?></div>
                            <?php } ?>
                        </div>

                    </div>
                <?php }
                ?>

            </div>

            <br>
        </div>
    </div>

    <script type="text/javascript">
        (function($) {
            $('.description .openable').live("click", function(event) {
                event.preventDefault();
                $(this).parent().next().slideDown();
                //$(this).parent().find('td:first a').html("-");
                $(this).parent().find('a:first').addClass("opened");
                $(this).addClass('closable').removeClass('openable');
            });

            $('.description .closable').live("click", function(event) {
                event.preventDefault();
                $(this).parent().next().slideUp();
                //$(this).parent().find('td:first a').html("+");
                $(this).parent().find('a:first').removeClass("opened");
                $(this).addClass('openable').removeClass('closable');
            });

            $('.state').live("change", function(event) {
                event.preventDefault();
                $(this).parent().submit();
            });

            $('form#search input.searchEmail').keyup(function(e) {
                if (e.keyCode == 13) {
                    $("form#search input#searchType").val("emailSearch");
                    $("form#search").submit();
                }
            });

            $('form#search input.searchId').keyup(function(e) {
                if (e.keyCode == 13) {
                    $("form#search input#searchType").val("idSearch");
                    $("form#search").submit();
                }
            });

            $("button.submit").click(function() {
                $("form#search input#searchType").val(this.name);
                $("form#search").submit();

            });

        })(jQuery);
    </script>