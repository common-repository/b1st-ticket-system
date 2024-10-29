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

<?php  $this->SetMessage(4) ;
  
$this->include_includes("functions");
$this->include_includes("scan");
$this->include_includes("utilities");

$hostname = '' ; //'{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX' or die(__('Cannot connect:', 'ticketsys') . ' ' . print_r(imap_errors(), true)); //gmail
$username = htmlspecialchars($config->emailUsername);
$password = $config->emailPassword;
$status = -1;
$count = -1;
$error = '';
$emailclient = $config->emailClient;
$folderInbox = "INBOX";
$msgType = "UNSEEN";

function save_attachment($content, $filename, $localfilepath, $thumbfilepath)
{
    if (imap_base64($content) != false) {
        $wp_filesystem->put_contents($localfilepath.$filename,imap_base64($content),0755);
    }
}
switch ($emailclient) {
    case "Gmail" :
        $folderSpam = "[Gmail]/Spam";
        $hostname = '{imap.gmail.com:993/imap/ssl}' . $folderInbox;
        $hostspam = '{imap.gmail.com:993/imap/ssl}' . $folderSpam;
        break;

    case "Yahoo" :
        $folderSpam = "Bulk Mail";
        $hostname = '{imap.mail.yahoo.com:993/imap/ssl}' . $folderInbox;
        $hostspam = '{imap.mail.yahoo.com:993/imap/ssl}' . $folderSpam;

        break;

    case "Horde" :
        $server = $_SERVER['HTTP_HOST'];
        $hostname = '{' . $server . ':143/notls}' . $folderInbox;
        $hostspam = '{' . $server . ':143/notls}' . $folderSpam;
        break;

}

$inbox = imap_open(
        $hostname, $username, $password
);
$spam = imap_open(
        $hostspam, $username, $password
);

if (!$inbox) {
       
        $error = "Cannot connect to your email: " ;
        $error .= imap_last_error();
		$error .= "You can click <a href='" .  site_url() . "/wp-admin/admin.php?page=ticketsys-settings-settings&tab=email'>here</a> to change your email under email  integration settings-tab." ;
   
}

if (isset($_POST['submit-mail']) && $_POST['submit-mail'] == "true") {
    $markread = "";
    $delete = "";
    $importMarkRead = $config->emailimportaction != "delete";
    $deleteMarkRead = $config->emaildeleteaction != "delete";
    foreach ($_POST['emailaction'] as $ticket => $value) {
        if ($value == '1') {
            $status = 1;
            $from = $_POST['from'][$ticket];
            $division = $_POST['division'][$ticket];
            $subject = $_POST['subject'][$ticket];
            $product = $_POST['product'][$ticket];
            $message = $_POST['message'][$ticket];
            $number = $_POST['number'][$ticket];
            $priority = intval($_POST['priority'][$ticket]);
            $now = date('U');
            $sql = "INSERT INTO {$dbprefix}messages (msg_ticket_id, msg_date, msg_subject, msg_email, msg_content,msg_division,msg_product,msg_priority,msg_status)
            VALUES('" . $ticket . "' , " . 'NOW()' . " , '" . $subject . "' , '" . $from . "' , '" . $message . "' , '" . $division . "' , '" . $product . "' , '" . $priority . "', 'open' )";
            $wpdb->query($sql);
            if ($importMarkRead)
                $markread.=$number . ",";
            else
                $delete.=$number . ",";
        }
        elseif ($value == '2') {
            $status = 1;
            $number = $_POST['number'][$ticket];
            if ($deleteMarkRead)
                $markread.=$number . ",";
            else
                $delete.=$number . ",";
        }
    }
    if ($status == -1)
        $status = 0;
    if ($markread != "")
        imap_setflag_full($inbox, rtrim($markread, ","), "\\Seen");
    if ($delete != "")
        imap_delete($inbox, rtrim($delete));
}
?>
<div class="container-fluid">
    <h3><?php _e('Administration', 'ticketsys'); ?></h3>
    <ul class="nav nav-tabs">
        <?php createMenu(5, $_SESSION['admin']); ?>
    </ul> 
    
		
	<div id="email-alert" class="alert alert-success hide">    </div>
    <?php
    if ($status == 1) {
        echo '<div style="" class="row-fluid"><div class="span10 offset1"><div class="alert alert-success">' . __('Messages has been updated!', 'ticketsys') . '</div></div></div>';
        exit;
    } else if ($status == 0) {
        echo '<div style="" class="row-fluid"><div class="span10 offset1"><div class="alert alert-error">' . __('Cannot import messages!', 'ticketsys') . '</div></div></div>';
        exit;
    }
    
    $emails = imap_search($inbox, 'UNSEEN');
    rsort($emails);
    foreach ($emails as $key => $value) {
        $value = Array(
            'type' => $inbox,
            'value' =>$value
            );
        $emails[$key]=$value;
    }
    if(true || $config->spamdelete == 'no'){
       $emailsSpam = imap_search($spam, 'UNSEEN');
       rsort($emailsSpam);
       foreach ($emailsSpam as $key => $value) {
         $value = Array(
            'type' => $spam,
            'value' =>$value
            );
         array_push($emails,$value);
       }
    }
    if ($emails) {
        $count = 0;
        ?>
        
        <div class="row-fluid"
             style="text-align:center;font-weight:bold; height: 34px; line-height: 34px; border-top: 1px solid <?php echo $config->inputtextcolor ?>; border-bottom: 1px solid <?php echo $config->inputtextcolor ?>; margin-top: 40px;">
            <div class="span1"></div>
            <div class="span3"><?php _e('From', 'ticketsys'); ?></div>
            <div class="span3"><?php _e('Subject', 'ticketsys'); ?></div>
            <div class="span1b"><?php _e('Division', 'ticketsys'); ?></div>
            <?php if ($config->productOption == "yes") { ?>
                <div class="span1b"><?php _e('Product', 'ticketsys'); ?></div>
            <?php } ?>
            <div class="span1a"><?php _e('Priority', 'ticketsys'); ?></div>
            <div class="span1b"><?php _e('Action', 'ticketsys'); ?></div>
        </div>
        <form action="" method="POST">
            <input type="hidden" name="submit-mail" value="true"/>
            <?php
            foreach ($emails as $email) {

                $count++;
                $overview = imap_fetch_overview($email['type'], $email['value'], 0);
                $message = imap_fetchbody($email['type'], $email['value'], 1, FT_PEEK);
                $pre = 'Received on ' . $overview[0]->date . ' via ' . $config->emailClient . '\n';
                $message = $pre . str_replace("'", "\'", $message);
                $now = date('U');
                $ticket_id = md5($now . $overview[0]->message_id);
                $upload_dir = wp_upload_dir(); 
                $savefilepath =  self::$PATH_ATTACHEMENTS. $ticket_id. '/';
                $date = new DateTime();
                $date->setTimestamp(intval($overview[0]->udate));
                $header = explode("\n", imap_fetchheader($email['type'], $email['value']));
                $priority = false;
                $head = array();
                $priority = $priority || $overview[0]->flagged;
                if (is_array($header) && count($header)) {

                    foreach ($header as $line) {
                        if (preg_match("/^X-/i", $line)) {
                            preg_match("/^([^:]*): (.*)/i", $line, $arg);
                            $head[$arg[1]] = $arg[2];
                        }
                    }
                }
                if (isset($head["X-Priority"])) {
                    $priority = $priority || intval($head["X-Priority"]);
                }
                    
                $structure = imap_fetchstructure($email['type'], $email['value']);
                $parts=Array();
                if(isset($structure->parts))
                    $parts = $structure->parts;
                $attachements= Array();
                foreach ($parts as $part) {
                    if ($part->parameters[0]->attribute == "NAME") {
                        $savefilename = $part->parameters[0]->value; // date("m-d-Y") . mt_rand(rand(), 6)
                        if(!file_exists($savefilepath)){
                            mkdir($savefilepath);
                            chmod($savefilepath,0755);
                    }
                        save_attachment(
                            imap_fetchbody($email['type'], $email['value'], 2,FT_PEEK),
                            $savefilename,
                            $savefilepath ,
                            null
                        );
                        #imap_fetchbody($email['type'], $email['value'], 2); //This marks message as read
                        #$scan_res = scanFile($config->metascan, $savefilepath . $savefilename);
                        if (false && $scan_res != 0 && $scan_res != 4 && $scan_res != 7) {
                            unlink($savefilepath . "/" . $savefilename);
                        }
                        else{
                            $attachements[$savefilename]=$ticket_id.'/'.$savefilename;
                        }
                        // 0  clean, 4 cleaned, 7 white-list


                    }
                }
                // imap_setflag_full($email['type'], rtrim($markread, ","), "\\Seen");

                ?>
                <div class="row-fluid description2">
                    <div class="span1 openable" style="text-align:center;">
                        <a href="javascript:void(0)" style="text-decoration:none;color:<?php echo $config->inputtextcolor ?>;font-weight: bold; font-size: 20px; width: 10px; margin: 0 auto; display:block;transition: all 500ms ease-in-out 0s;">+</a>
                    </div>
                    <div class="span3"><?php echo substr(htmlspecialchars($overview[0]->from), 0, 40) ?></div>
                    <div class="span3"><?php echo substr(htmlspecialchars($overview[0]->subject), 0, 40) ?></div>
                    <div class="span1b">
                        <select  name='division[<?php echo $ticket_id ?>]' class="state span11 division-select">
                            <?php
                            foreach ($config->divisions->division as $divisionC) {
                                echo "<option value='" . $divisionC . "'";
                                echo ">" . $divisionC . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="span1b">

                        <?php if ($config->productOption == "yes") { ?>
                            <select  name='product[<?php echo $ticket_id ?>]' class="state span11 product-select">
                                <?php
                                foreach ($config->products->product as $productC) {
                                    echo "<option value= '" . $productC . "'";
                                    echo ">" . $productC . "</option>";
                                }
                                ?>
                            </select>
                        <?php } ?>
                    </div>
                    <div class="span1a">  
                        <select  name='priority[<?php echo $ticket_id ?>]' class="state span11 priority-select">

                            <option value="4"><?php _e('Low', 'ticketsys'); ?></option>
                            <option value="3"><?php _e('Normal', 'ticketsys'); ?></option>
                            <option <?php echo ($priority) ? 'selected=selected' : ''; ?>value="2"><?php _e('High', 'ticketsys'); ?> </option>
                            <option value="1"><?php _e('Critical', 'ticketsys'); ?></option>
                        </select>
                    </div>
                    <div class="span1b">  
                        <select name='emailaction[<?php echo $ticket_id ?>]' class="state span11 action-select">
                            <option value="0"><?php _e('Nothing', 'ticketsys'); ?></option>
                            <option value="1"><?php _e('Import', 'ticketsys'); ?></option>  
                            <option value="2"><?php _e('Delete', 'ticketsys'); ?></option>                                   
                        </select>
                    </div>
                    <input type="hidden"name='number[<?php echo $ticket_id ?>]' value="<?php echo $email_number ?>"/>
                    <input type="hidden" name='from[<?php echo $ticket_id ?>]' value="<?php echo $overview[0]->from ?>"/>
                    <input type="hidden" name='subject[<?php echo $ticket_id ?>]' value="<?php echo $overview[0]->subject ?>"/>
                    <input type="hidden" name='message[<?php echo $ticket_id ?>]' value="<?php echo htmlspecialchars($message) ?>"/>
                </div>
                <div class="row-fluid details"
                     style="padding-top: 10px; display:none; background-color: <?php echo $config->inputcolor; ?>;">


                    <div class="span10 offset1">
                        <div class="alert alert-info" style="margin-left:-4px;">
                            <?php
                              $attachements_html="";
                              if (count($attachements) > 0) {
                                    $attachements_html.=" <li><strong>" .__('Attachments','ticketsys')."</strong> : ";
                                    $attachements_html.= '<span class="label label-info">' . __('Attachments', 'ticketsys') . '</span> <div class="alert alert-default" style="margin-left:-4px;">';
                                    
                                    foreach ($attachements as $file_name => $file_path) {
                                        $abs_url = get_site_url().'/wp_content/uploads/b1st/'.$file_path;
                                        $attachements_html.= "<a href ='$abs_url' target='_blank'>$file_name</a> ";
                                    }
                                    $attachements_html.= "</div>";
                                    $attachements_html.="</li>";
                                }
                            
                            echo ("<table width=100%><tr><td width='50%'><ul>
                                    
                                    <li><strong>" . __('Subject', 'ticketsys') . "</strong> : " . htmlspecialchars($overview[0]->subject) . "</li>
                                    <li><strong>" . __('From', 'ticketsys') . "</strong> : " . htmlspecialchars($overview[0]->from) . "</li>
                                    <li><strong>" . __('Date', 'ticketsys') . "</strong> : " . htmlspecialchars($date->format("D, d M y H:i:s O")) . "</li>
                                    <li><strong>" . __('Body', 'ticketsys') . "</strong> : " . htmlspecialchars($message) . "</li>
                                    ".$attachements_html.
                                    "</ul></td>
                                    
                                  </table>");
                            ?>

                        </div>
                    </div>

                </div>
                <?php
                $sql = "INSERT INTO messages (msg_ticket_id, msg_date, msg_subject, msg_email, msg_content, msg_status)
                VALUES('" . $ticket_id . "' , '" . $now . "' , '" . $overview[0]->subject . "' , '" . $overview[0]->from . "' , '" . $message . "', 'open' )";
            }
            ?> 
            <div style="text-align:center;margin-top: 20px;">
                <button type="submit" class="btn btn-success"><?php _e('Save changes', 'ticketsys'); ?></button>
            </div> 
        </form>
        
        <?php
    }
    imap_close($inbox);
	 ?>
	<script type="text/javascript">
            //var $= jQuery.noConflict();
			 jQuery(document).ready(function($) {
                count =<?php echo $count ?>;
				
                if (count == 0) {
                    content = "<?php _e('No new messages in your inbox', 'ticketsys'); ?>";
                }
                else if (count == 1) {
                    content = "<?php _e('There is only 1 message received', 'ticketsys'); ?>";
                }
                else if  (count > 1) {
                    content = "<?php _e('There are', 'ticketsys'); ?> " + count + " <?php _e('messages received', 'ticketsys'); ?>";
                }
				else if  (count < 0) {
				     content = "<?php _e($error, 'ticketsys'); ?> ";
				}
                $('#email-alert').html(content);
                $('#email-alert').removeClass('hide');
				
            });

        </script>
		
  
</div>

<script type="text/javascript">
    var test;
    var test2;
    (function($) {
        $('.description2 .openable').live("click", function(event) {
            event.preventDefault();
            $(this).parent().next().slideDown();
            //$(this).parent().find('td:first a').html("-");
            $(this).parent().find('a:first').addClass("opened");
            $(this).addClass('closable').removeClass('openable');
        });

        $('.description2 .closable').live("click", function(event) {
            event.preventDefault();
            $(this).parent().next().slideUp();
            //$(this).parent().find('td:first a').html("+");
            $(this).parent().find('a:first').removeClass("opened");
            $(this).addClass('openable').removeClass('closable');
        });

        $('.division-select').live("change", function(event) {
            changeAction(event.target);

        });

        $('.product-select').live("change", function(event) {
            changeAction(event.target);

        });

        $('.priority-select').live("change", function(event) {
            changeAction(event.target);

        });

        function changeAction(event) {
            target = $('.description2').has(event).find('.action-select');
            target.val(1);

        }





    })(jQuery);
</script>
