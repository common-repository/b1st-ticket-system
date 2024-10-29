<?php
$instance = md5(rand() . 'ticket');
?>
<style>
    .uploadFrame {
        display: none !important;
    }

    .contact input, .contact textarea, .contact select, .contact button {
        background-color: <?php echo $config->inputcolorcustomer ?> !important;
        color: <?php echo $config->inputtextcolorcustomer ?> !important;
        border: none;
    }

    .contact h3, .contact contact-result {
        color: <?php echo $config->titlecolorcustomer ?> !important;
    }

    .contact .message-content {
        <?php if ($config->productOption == "yes") { ?> height: 90px !important;
        <?php } else { ?> height: 90px !important;
        <?php } ?>
    }
</style>
<div class="bootstrap">
    <div class="container-fluid">

        <div class="row">


            <div class="span5 contact" style="background-color: <?php echo $config->backgroundcolorcustomer ?> !important; ">
                <h3><?php _e('Contact us', 'ticketsys'); ?></h3>

                <div class="row" style="margin-top:20px;">

                    <div id="<?php echo $instance ?>contact-init">
                        <form id="<?php echo $instance ?>" enctype="multipart/form-data" action="" target="uploadFrame"
                              method="post">

                            <input type="hidden" name="action" value="ticketsys-upload"/>
                            <input type="hidden" name="instance" value="<?php echo $instance ?>"/>
                            <div class="row">

                                <div class="span4 offset1">
                                    <input class="span4 message-subject" type="text" placeholder="<?php _e('Subject', 'ticketsys'); ?>">

                                    <input class="span4 message-email" name="email_id" id="email_id" type="text"
                                           placeholder="<?php _e('Mail address', 'ticketsys'); ?>">

                                    <input class="message-ticketid" type="hidden" name="ticketid" id="ticketid"
                                           value="<?php echo md5(rand() . time()); ?>">


                                </div>

                                <div class="span4 offset1">

                                    <input class="span4 message-name" type="text" placeholder="<?php _e('Name', 'ticketsys'); ?>">
                                    <input class="span4 message-phone" type="text" placeholder="<?php _e('Phone number (Optional)', 'ticketsys'); ?>">

                                </div>


                                <div class="span4 offset1">
                                    <div style="width:100%; text-align: left;">
                                        <select class="message-division span2">
                                            <option selected><?php _e('Division', 'ticketsys'); ?></option>
                                            <?php
                                            foreach ($config->divisions->division as $division) {
                                                echo "<option>" . $division . "</option>";
                                            }
                                            ?>
                                        </select>
                                        <select class="message-priority span2">
                                            <option><?php _e('Priority', 'ticketsys'); ?></option>
                                            <option><?php _e('Urgent', 'ticketsys'); ?></option>
                                            <option><?php _e('High', 'ticketsys'); ?></option>
                                            <option><?php _e('Medium', 'ticketsys'); ?></option>
                                            <option><?php _e('Low', 'ticketsys'); ?></option>
                                        </select>
                                        <?php if ($config->productOption == "yes") { ?>
                                            <select class="message-product" class="span2">
                                                <option selected><?php _e('Product', 'ticketsys'); ?></option>
                                                <?php
                                                foreach ($config->products->product as $product) {
                                                    echo "<option>" . $product . "</option>";
                                                }
                                                ?>
                                            </select>
                                        <?php } else { ?>
                                            <input class="message-product" type="hidden" value="none">
                                        <?php } ?>
                                    </div>
                                </div>


                                <div class="span4 offset1">
                                    <textarea class="message-content" placeholder="<?php _e('Your message', 'ticketsys'); ?>"></textarea>
                                </div>

                                <?php if ($config->allowUpload == "yes") { ?>
                                    <div class="span4 offset1">
                                        <div id="uploads" style="text-align: left; margin-bottom: 10px;">
                                            <?php for ($i = 0; $i < 10; $i++) { ?>
                                                <div id="file-<?php echo $i; ?>" style="display: none;"><div class="sep"></div><input class="uploadFile" name="uploadFile[]" type="file"/><a href="javascript:void(0);" onclick="removeFile(this)"><img src="<?php echo plugins_url('../assets/cross.png', __FILE__); ?>" /></a></div>
                                            <?php } ?>
                                            <iframe class="uploadFrame" name="uploadFrame" src="#"></iframe>
                                            <a style="display:block" href="javascript:void(0);" onclick="addFile2(this)"><?php _e('Add File', 'ticketsys'); ?></a>

                                            <script>
                                                var count = 0;
                                                var test;
                                                function addFile(event) {
                                                    if (count + 1 ><?php echo $config->maxuploads ?>) {
                                                        return;
                                                    }
                                                    jQuery(event.parentElement).append('<div class="sep"></div><input class="uploadFile" name="uploadFile[]" type="file"/><a href="javascript:void(0);" onclick="removeFile(this)"><img src="<?php echo plugins_url('../assets/cross.png', __FILE__); ?>" /></a>');
                                                    temp = event.parentElement;
                                                    jQuery(event).remove();
                                                    jQuery(temp).append(event);

                                                    count++;
                                                }
                                                function addFile2(event) {
                                                    if (count + 1 ><?php echo $config->maxuploads ?>) {
                                                        return;
                                                    }
                                                    //jQuery(event.parentElement).append('<div class="sep"></div><input class="uploadFile" name="uploadFile[]" type="file"/><a href="javascript:void(0);" onclick="removeFile(this)"><img src="<?php echo plugins_url('../assets/cross.png', __FILE__); ?>" /></a>');
                                                    jQuery("#file-" + count).show();
                                                    temp = event.parentElement;
                                                    jQuery(event).remove();
                                                    jQuery(temp).append(event);

                                                    count++;
                                                }
                                                function removeFile(event) {
                                                    jQuery(event).prev().remove();
                                                    jQuery(event).remove();
                                                    count--;

                                                }
                                            </script>  
                                        </div>
                                    </div>
                                <?php } ?>

                                <div class="row-fluid">
                                    <?php if ($config->recaptchaOption == "yes") { ?>
                                        <div class="span3"></div>
                                        <div class="span5">
                                            <div id="<?php echo $instance . 'recaptcha' ?>" class="span2 recaptcha"></div>
                                        </div>
                                    <?php } else { ?>
                                        <input id="recaptcha_challenge_field" type="hidden" value="none"/>
                                        <input id="recaptcha_response_field" type="hidden" value="none"/>
                                    <?php } ?>
                                </div>

                                <div class="span4 offset1" style="margin-top: 15px;">
                                    <div style="width:100%; text-align: left;">
                                        <button  class="btn ticket-submit" id="send-contact-message2"><?php _e('Send', 'ticketsys'); ?></button>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>

                </div>

                <div id="<?php echo $instance ?>contact-loader" style="display:none; width:40%; margin: 0 auto;">
                    <div style="margin-bottom: 9px;" class="progress progress-info progress-striped active">
                        <div class="bar"
                             style="background-color:<?php echo $config->loadercolorcustomer ?> !important; width: 100%;"></div>
                    </div>
                </div>

                <div id="<?php echo $instance ?>contact-result" style="display:none; text-align: center;"></div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="http://www.google.com/recaptcha/api/js/recaptcha_ajax.js"></script>
<?php if ($config->recaptchaOption == "yes") { ?>
    <script type="text/javascript">
                                                if (typeof _parent == "undefined")
                                                    var _parent = null;
                                                if (typeof _children == "undefined")
                                                    var _children = new Array();

                                                function recaptchCallback() {
                                                    Recaptcha.focus_response_field();
                                                    setTimeout(function() {
                                                        jQuery('.recaptcha').html(jQuery('#' + _parent + 'recaptcha').clone(true, true).html());
                                                    }, 600);
                                                }

                                                function showRecaptcha() {
                                                    if (_parent != null) {

                                                        jQuery('.recaptcha').html(jQuery('#'+_parent+'recaptcha').clone(true,true).html());
                                                        return;
                                                    }
                                                    _parent = "<?php echo $instance ?>";
                                                    console.log(document.getElementById(_parent + "recaptcha"));
                                                    Recaptcha.create(<?php echo "'" . $config->recaptchapublickey . "'"; ?>, document.getElementById(_parent + "recaptcha"), {
                                                        theme: "<?php echo $config->recaptchatheme; ?>",
                                                        callback: recaptchCallback});
                                                }

                                                jQuery(document).ready(function() {
                                                    showRecaptcha();
                                                });

    </script>
<?php } ?>