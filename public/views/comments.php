<?php
$instance = md5(rand() . 'ticket');

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

$ticket = $_GET['ticketsys_id'];
$token = $_GET['token'];
$secret = "MaA@^&13aLEx99";

if (sha1($ticket . $secret) != $token) {
    header('Location: ' . get_site_url());
    exit;
}

$conversation = "SELECT * FROM {$dbprefix}messages WHERE msg_ticket_id = '" . $ticket . "'";

$msg = $wpdb->get_results($conversation);
if ($wpdb->num_rows == 0) {
    header('Location: ' . get_site_url());
    exit;
}

$msg = $msg[0];
if ($msg->msg_status == 'closed') {
    header('Location: ' . get_site_url());
    exit;
}

if ($_POST) {

    $ans_ticket_id = $_POST["ticketid"];

    if (empty($_POST["answ_content"])) {
        $msg_error = __('You must enter a content for the answer.', 'ticketsys');
    } else {

        $wpdb->query(
                "INSERT INTO {$dbprefix}answers SET a_msg_id = " . $msg->msg_id . ",
                                 a_date = NOW(),
                                 a_content = '" . addslashes($_POST["answ_content"]) . "',
                                 a_email = '" . addslashes($_POST["answ_email"]) . "',
                                 ans_ticket_id = '" . addslashes($ans_ticket_id) . "' ,  
                                 a_account = 0"
        );

        $now = date('U');
        $wpdb->query(
                "UPDATE {$dbprefix}messages SET msg_update_date = '$now' WHERE msg_id = '" . $msg->msg_id . "'"
        );

        $msg_content = __('Your message has been well sent!', 'ticketsys');

        // Mail for the admin

        $ans_email = $msg->msg_email;
        $sql = "SELECT DISTINCT a.a_email, m.msg_id FROM {$dbprefix}messages m JOIN {$dbprefix}answers a ON m.msg_id=a.a_msg_id WHERE a.a_email!='$ans_email' AND m.msg_id ='" . $msg->msg_id . "'";

        if ($config->mailNotification == "yes") {
            $mailing_list = array();
            $query = $wpdb->get_results($sql);
            foreach ($query as $list) {
                $mail_list = strtolower($list->a_email);
                if (!in_array($mail_list, $mailing_list)) {
                    array_push($mailing_list, $mail_list);
                }
            }



            $start = 1;
            $mail_to = '';
            foreach ($mailing_list as $mail) {
                if ($start) {
                    $mail_to .= $mail;
                    $start = 0;
                } else {
                    $mail_to .= ',' . $mail;
                }
            }
            //$mail_to .= '\'';

            $secret = "@^&tick99";
            $token = sha1($ticket . $secret);
            $support_link = get_site_url() . '/?ticketsys_id=' . $ticket . "&token=" . $token;

            $headers = 'From: ' . $config->responderMail . '' . "\r\n" .
                    'Reply-To: ' . $config->responderMail . '' . "\r\n";
            $headers .= 'Content-Type: text/html; charset="iso-8859-1"' . "\n";
            $headers .= 'Content-Transfer-Encoding: 8bit';
            if (!empty($mail_to))
                mail(
                        $mail_to, get_bloginfo('name') . ' - ' . __('New ticket answer', 'ticketsys'), __('Hello', 'ticketsys') . ',<br><br>' . __('There is a new message here', 'ticketsys') . ' : <br> <a href="' . $support_link . '">' . $support_link . '</a>  ------  ' . __('This message has been sent automatically : please don\'t answer.', 'ticketsys'), $headers
                );
        }
        $out = array();
        header('Content-Type: application/json');
        $out['answerid'] = $ans_ticket_id;
        echo json_encode($out);
        exit;
    }
}
?>

<style>

    .bootstrap #uploadFrame {
        display: none;
    }

    .bootstrap .conversLeft {
        border-left: 4px solid silver;
        min-height: 100px;
        background-color: rgba(212, 212, 212, 0.2);
        padding: 0;
    }

    .bootstrap .conversRight {
        border-right: 4px solid silver;
        border-top: 1px solid silver;
        min-height: 100px;
        background-color: rgba(240, 240, 240, 0.1);
        padding: 0;
    }

    .bootstrap .container-fluid {
        margin-top: 30px;
        background-color: <?php echo $config->backgroundcolor ?>;
        color: <?php echo $config->inputtextcolor ?>;
        padding: 10px;
    }

    .bootstrap .test {
        background-color: <?php echo $config->loadercolor ?>;
    }

    .bootstrap input, .bootstrap textarea, .bootstrap select,.bootstrap  button {
        background-color: <?php echo $config->inputcolor ?>;
        color: <?php echo $config->inputtextcolor ?>;
    }

    .bootstrap h3, .bootstrap .contact-result,.bootstrap th {
        color: <?php echo $config->titlecolor ?>;
    }

    .bootstrap .breadcrumb {
        background-color: <?php echo $config->inputcolor ?>;
        color: <?php echo $config->inputtextcolor ?>;
    }

    .bootstrap .breadcrumb a {
        color: <?php echo $config->inputtextcolor ?>;
        text-shadow: none;
    }

    .bootstrap textarea {
        box-sizing: border-box; /* For IE and modern versions of Chrome */
        -moz-box-sizing: border-box; /* For Firefox                          */
        -webkit-box-sizing: border-box;
        resize: none;
        width: 100%;
    }
    .bootstrap .hide{
        display: none !important;
    }
    .bootstrap .sep{
        margin-bottom:3px;
    }
    .bootstrap input[type="file"] {
        line-height: auto;
        height:auto;
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


<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">



            <h3><?php _e('Your conversation', 'ticketsys'); ?></h3>

            <div class="row-fluid">
                <div class="span10 offset1">
                    <div class="row-fluid">
                        <div class="alert alert-info" style="margin-left:-4px;">
                            <?php
                            $division = ($msg->msg_division == "" ? "-" : $msg->msg_division);
                            $product = ($msg->msg_product == "" ? "-" : $msg->msg_product);
                            $phone = ($msg->msg_phone == "" ? "-" : $msg->msg_phone);

                            echo "<table width=100%><tr><td width='50%'><ul>
                                    
									<li><strong>" . __('Subject', 'ticketsys') . "</strong> : " . htmlspecialchars($msg->msg_subject) . "</li>
									<li><strong>" . __('Name', 'ticketsys') . "</strong> : " . htmlspecialchars($msg->msg_name) . "</li>
									<li><strong>" . __('Email', 'ticketsys') . "</strong> : " . htmlspecialchars($msg->msg_email) . "</li>
                                    <li><strong>" . __('Phone', 'ticketsys') . "</strong> : " . htmlspecialchars($phone) . "</li>
                                  </ul></td>
								  
								  <td width='50%'>
								  <ul>
                                    <li><strong>" . __('Msg Id', 'ticketsys') . "</strong> : " . htmlspecialchars($msg->msg_id) . "</li>
									<li><strong>" . __('Date', 'ticketsys') . "</strong> : " . htmlspecialchars(date('Y-m-d H:i:s', $msg->msg_date)) . "</li>
                                    <li><strong>" . __('Division', 'ticketsys') . "</strong> : " . htmlspecialchars($division) . "</li>
                                    <li><strong>" . __('Product', 'ticketsys') . "</strong> : " . htmlspecialchars($product) . "</li>
									</ul></tr></td>
								  
								  </table>";


                            $upload_dir = wp_upload_dir();
                            $savefilepath = $upload_dir['basedir'] . '/b1st/' . $ticket;

                            if (is_dir($savefilepath)) {


                                $savefilepath2 = sprintf('%s?action=ticketsys-download&ticket=%s&fileName=', get_site_url(), $ticket);
                                $temp = scandir($savefilepath . "/");

                                $attach_files = array();
                                for ($i = 2; $i < count($temp); $i++) {
                                    if (!is_dir($savefilepath . '/' . $temp[$i]))
                                        array_push($attach_files, $temp[$i]);
                                }

                                if (count($attach_files > 0)) {
                                    echo '<span class="label label-info">' . __('Attachments', 'ticketsys') . '</span> <div class="alert alert-default" style="margin-left:-4px;">';
                                    for ($i = 0; $i < count($attach_files); $i++) {
                                        echo "<a href ='$savefilepath2$attach_files[$i]' target='_blank'>$attach_files[$i]</a> ";
                                    }


                                    echo "</div>";
                                }
                            }
                            ?>
                        </div>
                    </div>

                    <div class="row-fluid">
                        <div class="conversRight" style="padding-top:10px;margin-left:-4px;">
                            <div class="span1" style=""><?php echo get_gravatar($msg->msg_email) ?></div>
                            <div class="span8">
                                <div
                                    style="font-weight: bold; border-bottom: 1px dashed silver; margin-bottom: 5px; line-height: 25px;">
                                        <?php printf(__('By You (%s)', 'ticketsys'), $msg->msg_email); ?>
                                </div>
                                <?php echo htmlspecialchars($msg->msg_content) ?>
                            </div>
                        </div>
                    </div>

                    <?php
                    $answers = $wpdb->get_results(
                            "SELECT id, a_content, a_email, a_date, a_account, ans_ticket_id
                                                                FROM {$dbprefix}answers
                                                         WHERE a_msg_id ='" . $msg->msg_id . "'
                                                    ORDER BY a_date"
                    );


                    if ($wpdb->num_rows == 0) {
                        echo "<div class='alert'>" . __('There is no answer at the moment.', 'ticketsys') . "</div>";
                    } else {
                        global $wp;
                        $current_url = add_query_arg($wp->query_string, '', home_url($wp->request));
                        foreach ($answers as $answ) {
                            if ($answ->a_account != 0) {
                                $email = $answ->a_email;
                                $dbprefix = "b1st_ts_";
                                if(!$can_access){
                                    $tran1 = __("You have to register in order to view the replies.","ticketsys");
                                    $tran2 = __("Click here","ticketsys");
                                    $tran3 = __("To continue.","ticketsys");
                                    $redirect_url  = wp_registration_url();
                                    echo "<p style='font-weight:bold;'> $tran1 <a href='$redirect_url'> $tran2</a> $tran3 </p>";
                                    break;
                                }
                                if ($config->allowRating == "yes") :
                                    $rating = '<style>
.rating {
  unicode-bidi: bidi-override;
  direction: rtl;
position: absolute;
right: 0;
top: 0;
bottom: 0;
}
.rating a {
text-decoration: none !important;
color: #0088cc !important;
}
.rating > a {
  display: inline-block;
  position: relative;
  width: 1.1em;
}
.rating[data-allow="1"] > a:hover:before,
.rating[data-allow="1"] > a:hover ~ a:before, .rating > a.selected:before,
.rating > a.selected ~ a:before {
   content: "\2605";
   position: absolute;
	color: rgb(255, 133, 0) !important;
}
.rating a.selected {
   color: rgb(255, 133, 0) !important;
}
.conversLeft {position: relative;}
			</style>';

                                    if (isset($_GET['aid']) && $_GET['aid'] == $answ->id && isset($_GET['rated']) && $_GET['rated'] <= 5) {
                                        $reply_rated = $wpdb->get_col("SELECT * FROM {$dbprefix}msg_rating WHERE  mid = $msg->msg_id AND aid = $answ->id;");
                                        if (!$reply_rated):
                                            $wpdb->insert("{$dbprefix}msg_rating", array("stars" => $_GET['rated'], "aid" => $answ->id, "mid" => $msg->msg_id));
                                        else :
                                            $wpdb->update("{$dbprefix}msg_rating", array("stars" => $_GET['rated']), array("aid" => $answ->id, "mid" => $msg->msg_id));
                                        endif;

                                        $total = $wpdb->get_row("SELECT SUM(stars) AS 'stars', COUNT(votes) AS 'votes' FROM {$dbprefix}msg_rating WHERE mid = $msg->msg_id;");

                                        $stars = $total->stars;
                                        $votes = $total->votes;
                                        $rating = $stars / $votes;
                                        $rating = sprintf("%.1f", $rating);
                                        $wpdb->update("{$dbprefix}messages", array("msg_rating" => $rating), array("msg_id" => $msg->msg_id));
                                        header("Location: " .  site_url() . '/' . '?ticketsys_id=' . $_GET['ticketsys_id'] . '&token=' . $_GET['token']);
                                    }
                                    $total_rating = $wpdb->get_col("SELECT stars FROM {$dbprefix}msg_rating WHERE mid = $msg->msg_id AND aid = $answ->id")[0];
                                    $rated = 5;
                                    $allow = false;
                                    if ($total_rating == 0)
                                        $allow = true;
                                    $rating .= '<div class="rating" data-allow="' . $allow . '"><h5 style="text-align: left; direction: ltr;margin: 0px 4px;">' . __('Rate this:', 'ticketsys') . '</h5>';
                                    for ($i = 0; $i < 5; $i++) {
                                        $select = "";

                                        $sep = ((strrpos($current_url, '?')) ? "&" : "?");

                                        $link =  site_url() . '?rated=' . ($rated--) . '&aid=' . $answ->id . (isset($_GET['ticketsys_id']) ? '&ticketsys_id=' . $_GET['ticketsys_id'] : "") . (isset($_GET['token']) ? '&token=' . $_GET['token'] : "");
                                        if (!$allow)
                                            $link = "#";

                                        if ($total_rating > $rated && $total_rating > 0) {
                                            $select = ' class = "selected"';
                                        }
                                        $rating .= '<a href="' . $link . '"' . $select . '>&#9734;</a>';
                                    }
                                    $rating .= '</div>';
                                else :
                                    $rating = "";
                                endif;
                                echo '<div class="row-fluid">
                                                <div class="conversLeft" style="padding-top:10px;margin-left: -4px;">
                                                    <div class="span1" style="">' . get_gravatar($answ->a_email) . '</div>
                                                    <div class="span8">
                                                    <div style="font-weight: bold; border-bottom: 1px dashed silver; margin-bottom: 5px; line-height: 25px;">' . sprintf(__('By %s', 'ticketsys'), htmlspecialchars(
                                                get_userdata(1)->user_login
                                )) . '</div>
                                                    ' . htmlspecialchars($answ->a_content) . '
                                                    </div>
						' . $rating . '
                                                <div style="clear:both;"></div></div>
                                              </div>';
                            } else {

                                echo '<div class="row-fluid">
                                                <div class="conversRight" style="padding-top:10px;margin-left: -4px;">
                                                    <div class="span1" style="">' . get_gravatar($answ->a_email) . '</div>
                                                    <div class="span8">
                                                        <div style="font-weight: bold; border-bottom: 1px dashed silver; margin-bottom: 5px; line-height: 25px;">You (' . $answ->a_email . ')</div>
                                                        ' . htmlspecialchars($answ->a_content) . '
                                                    </div>
                                                </div>
                                              </div>';
                            }


                            // show attachments
                            $upload_dir = wp_upload_dir();
                            $savechildfilepath = $upload_dir['basedir'] . '/b1st/' . $ticket . '/' . $answ->ans_ticket_id;


                            if (is_dir($savechildfilepath) && !empty($answ->ans_ticket_id)) {

                                echo '<span class="label label-info">' . __('Attachments', 'ticketsys') . '</span> <div class="alert alert-default" style="margin-left:-4px;">';
                                $savechildfilepath2 = sprintf('%s?action=ticketsys-download&ticket=%s&answer=%s&fileName=', get_site_url(), $ticket, $answ->ans_ticket_id);
                                $attach_files = scandir($savechildfilepath . "/");


                                for ($i = 2; $i < count($attach_files); $i++) {
                                    echo "<a href ='$savechildfilepath2$attach_files[$i]' target='_blank'>$attach_files[$i]</a> ";
                                }

                                if (count($attach_files) == 2) {
                                    _e('This email has an attachment which was removed due to being suspicious to be infected', 'ticketsys');
                                }
                                echo "</div>";
                            }
                        }
                    }
                    ?>
                    <?php
                    if (($msg->msg_status != 'close') && $can_access && !(is_user_logged_in() && current_user_can('ticketsys-manage'))) {
                        ?>
                        <div class="row-fluid" style="text-align:center">
                            <hr>
                            <div id="notification-success" class='alert alert-success hide'><?php _e('Ticket updated. Thanks for your feedback!', 'ticketsys'); ?></div>
                            <div id="notification-fail" class='alert alert-error hide'><?php _e('Error: Contact SysAdmin', 'ticketsys'); ?></div>
                            <div id="notification-info" class='alert alert-info hide'></div>

                            <?php
                            $ans_ticket_id = md5(rand() . $msg->msg_email);
                            ?>

                            <form id="uploadForm" enctype="multipart/form-data" action="" target="uploadFrame"
                                  method="post">
                                <input type="hidden" name="action" value="ticketsys-upload"/>
                                <input type="hidden" name="ticketid" id="ticketid" value="<?php echo $ticket ?>"/>
                                <input type="hidden" name="answerid" id="answerid" value=""/>
                                <input type="hidden" name="instance" value="<?php echo $instance ?>"/>
                                <?php if ($config->allowUpload == "yes") { ?>
                                    <div >
                                        <div id="uploads" style=" margin-bottom: 10px;">
                                            <input class="uploadFile" name="uploadFile[]" type="file"/><a href="javascript:void(0);" onclick="removeFile(this)"><?php _e('Remove', 'ticketsys'); ?></a>
                                            <iframe id="uploadFrame" name="uploadFrame"  src=""></iframe>
                                            <a style="display:block" href="javascript:void(0);" onclick="addFile(this)"><?php _e('Add File', 'ticketsys'); ?></a>
                                            <div class="sep"></div>
                                            <script>
                                                var count = 1;
                                                function addFile(event) {
                                                    if (count + 1 > <?php echo $config->maxuploads ?>)
                                                        return;
                                                    $('#uploads').append('<input class="uploadFile" name="uploadFile[]" type="file"/><a href="javascript:void(0);" onclick="removeFile(this)"><?php _e('Remove', 'ticketsys'); ?></a><div class="sep"></div>');
                                                    $(event).remove();
                                                    $('#uploads').append(event);
                                                    count++;
                                                }
                                                function removeFile(event) {
                                                    $(event).prev().remove();
                                                    $(event).remove();
                                                    count--;

                                                }
                                            </script>  
                                        </div>
                                    </div>
                                <?php } ?>
                            </form>


                            <div class="control-group">
                                <label class="control-label" for="textarea"><?php _e('Your answer', 'ticketsys'); ?></label>

                                <div class="controls">
                                    <input type="hidden" id="ticketid" name="ticketid" value="<?php echo $ans_ticket_id; ?>"/>
                                    <input type="hidden" value="<?php echo $msg->msg_email; ?>" name="answ_email"/>
                                    <textarea class="input-xlarge span7 offset2" id="textarea" name="answ_content"
                                              rows="5"></textarea>
                                </div>
                            </div>
                            <div>
                                <button onclick="submit()" id="ticketsys-comment-but" type="submit" class="btn"><?php _e('Send!', 'ticketsys'); ?></button>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var allow = true;
    function SubmitWithCallback(form, frame, successFunction) {
        var callback = function() {
            if (successFunction)
                successFunction();
            frame.unbind('load', callback);
        };

        frame.bind('load', callback);
        form.submit();
    }
    function notify(type, data) {
        if (type == 1)
            target = 'info';
        else if (type == 2)
            target = 'success';
        else if (type == 3)
            target = 'fail';

        $('#notification-info').addClass('hide');
        $('#notification-success').addClass('hide');
        $('#notification-fail').addClass('hide');


        $('#notification-' + target).html(data);
        $('#notification-' + target).removeClass('hide');
    }

    function submit() {
        if (!allow)
            return;
        allow = false;
        $('#textarea').attr('disabled', 'disabled');
        $('#ticketsys-comment-but').attr('disabled', 'disabled');
        // $('.uploadFile').attr('disabled','disabled');

        $.ajax({
            url: "",
            type: "post",
            data: {'action': 'ticketsys-comment', 'answ_content': $('#textarea').val(), 'ticketid': '<?php echo $ans_ticket_id; ?>', 'answ_email': '<?php echo $msg->msg_email; ?>'},
            success: function(data) {
                $('#answerid').val(data.answerid);


                if ($('[name="uploadFile[]"]').val() == "") {
                    window.setTimeout('location.reload()', 1000);
                    notify(2, '<?php _e('Ticket updated. Thanks for your feedback!', 'ticketsys'); ?>');

                }
                else {
                    notify(1, '<?php _e('Uploading please wait..', 'ticketsys'); ?>');
                    SubmitWithCallback($('#uploadForm'), $('#uploadFrame'),
                            function() {
                                eval($('#uploadFrame').contents().find('script').html());
                                if (status == 2) {
                                    window.setTimeout('location.reload()', 1000);
                                    notify(2, '<?php _e('Ticket updated. Thanks for your feedback!', 'ticketsys'); ?>');
                                }
                                else {
                                    notify(3, error);
                                }
                            });
                }
                allow = true;

            },
            error: function() {
                notify(3, '<?php _e('Error. Contact sysAdmin', 'ticketsys'); ?>');
            }
        });
    }

</script>