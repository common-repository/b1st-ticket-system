<?php

// DEFAULT : -1      LACKING INFOS : 0      BAD MAIL : 1     SUCCESS : 2      CAPTCHA ERROR : 3
// Done : Verify if ReCaptcha is enabled in the XML file
require_once(plugin_dir_path(__FILE__) . '../includes/recaptchalib.php');
require_once(plugin_dir_path(__FILE__) . '../includes/akismet.class.php');
require_once plugin_dir_path(__FILE__) . '../includes/akismet.php';

$aCallBack[0] = -1;

if (empty($_POST['name']) || empty($_POST['subject']) || empty($_POST['email']) || empty($_POST['content']) || $_POST['division'] == "Division" || $_POST['priority'] == "Priority" || $_POST['product'] == "Product"
) {
    $aCallBack[0] = 0;
} else {
    // ReCaptcha
    if ($config->recaptchaOption == "yes") {
        $privatekey = $config->recaptchaprivatekey;

        $resp = recaptcha_check_answer(
                $privatekey, $_SERVER["REMOTE_ADDR"], $_POST["challengeField"], $_POST["responseField"]
        );
    }

    if (preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $_POST['email'])) {
        if ($config->recaptchaOption == "no" || ($resp->is_valid || ($_POST["challengeField"] == "none" && $_POST["responseField"] == "none"))) {

            switch ($_POST['priority']) {
                case "Low" :
                    $iPriority = 1;
                    break;
                case "Medium" :
                    $iPriority = 2;
                    break;
                case "High" :
                    $iPriority = 3;
                    break;
                case "Urgent" :
                    $iPriority = 4;
                    break;
            }


            // Twitter
            require(plugin_dir_path(__FILE__) . '../includes/twitteroauth/twitteroauth.php');

            define('CONSUMER_KEY', $config->keyconsumer);
            define('CONSUMER_SECRET', $config->keysecretconsumer);
            define('KEY_TOKEN', $config->keytoken);
            define('KEY_SECRET', $config->keysecrettoken);

            //$connection = new TwitterOAuth($consumer_key, $consumer_secret, $oauth_token,  $oauth_token_secret);
            $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, KEY_TOKEN, KEY_SECRET);

            /* Get logged in user to help with tests. */
            if (null != CONSUMER_KEY) {
                $user = $connection->get('account/verify_credentials');

                /* direct_messages/new */
                $parameters = array('user_id' => $user->id, 'text' => $_POST['content']);
                $method = 'direct_messages/new';
                $dm = $connection->post($method, $parameters);
            }
            // check for spam if allowed
            $message_spam = 0;
            $spam_flag = checkAkismit($_POST['name'], $_POST['email'], $_POST['content']);
            if ($spam_flag == 4) { // spam
                $message_spam = 1;
            }

            if ($config->spamdelete = 'no' || ($config->spamdelete = 'yes' && $message_spam == 0)) {

                $ticket_id = $_POST['ticketid'];

                $now = date('U');
                $sql = "INSERT INTO {$dbprefix}messages (msg_ticket_id, msg_date,  msg_update_date, msg_subject, msg_name, msg_email, msg_priority, msg_division, msg_content, msg_phone, msg_product, msg_status, msg_spam)
                            VALUES('" . $ticket_id . "', '$now', '$now', '" . $_POST['subject'] . "' , '" . $_POST['name'] . "' ,
                                    '" . $_POST['email'] . "' , '" . $iPriority . "' ,
                                    '" . $_POST['division'] . "' , '" . $_POST['content'] . "',
                                    '" . $_POST['phone'] . "' , '" . $_POST['product'] . "' ,
                                     'open', " . $message_spam . ")";

                $wpdb->query($sql);
                $email_msg = $_POST['email'];

                $content_msg = sprintf(__('Hi %s', 'ticketsys'), $_POST['name']) . ',<br/>' . $config->responderContent . '<br/>';
                $content_msg2 = sprintf(__('Thank you for using %s Contact support.', 'ticketsys'), get_site_url()) . ' <br/>';
                $content_msg3 = sprintf(__('This message was sent to %s at your request.', 'ticketsys'), $_POST['email']) . " <br/>-----<br>" . __("This message has been sent automatically : please don't answer.", 'ticketsys') . "<br/><br/>";
                $content_msg4 = __('Thanks', 'ticketsys') . ', <br/>' . get_bloginfo('name') . '<br/>' . get_site_url() . '<br/>';

                $subject_msg = get_bloginfo('name') . ' - ' . $config->responderSubject;

                $headers = 'From: ' . $config->responderMail . '' . "\r\n" .
                        'Reply-To: ' . $config->responderMail . '' . "\r\n";
                $headers .= 'Content-Type: text/html; charset="iso-8859-1"' . "\n";
                $headers .= 'Content-Transfer-Encoding: 8bit';
                mail(
                        $email_msg, $subject_msg, $content_msg . $content_msg2 . $content_msg3 . $content_msg4, $headers
                );


                if ($config->mailNotification == "yes") {

                    $sql = "SELECT * FROM $wpdb->users";
                    foreach ($wpdb->get_results($sql) as $col) {
                        $user = new WP_User($col->ID);
                        if ($user->has_cap('ticketsys-manage')) {
                            $email_msg = $user->user_email;
                            $content_msg5 = sprintf(__('Hi %s', 'ticketsys'), $user->user_login) . ', <br/>' . __('A new ticket has been posted.', 'ticketsys') . '<br/>';
                            $subject_msg = get_bloginfo('name') . ' - ' . __('New ticket', 'ticketsys');
                            mail(
                                    $email_msg, $subject_msg, $content_msg5 . sprintf(__('This message was sent to %s at your request.', 'ticketsys'), $user->user_email) . " <br/>-----<br>" . __("This message has been sent automatically : please don't answer.", 'ticketsys') . " <br/><br/>" . $content_msg4, $headers
                            );
                        }
                    }
                }
            }
            $aCallBack[0] = 2;
        } else {
            $aCallBack[0] = 3;
        }
    } else {
        //var_dump($_POST['email']);
        $aCallBack[0] = 1;
    }
}

echo json_encode($aCallBack);
