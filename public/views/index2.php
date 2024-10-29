<style>
    #uploadFrame {
        display: none;
    }

    .contact {
        background-color: <?php echo $config->backgroundcolorcustomer ?>;
    }

    .contact input, .contact textarea, .contact select, .contact button {
        background-color: <?php echo $config->inputcolorcustomer ?>;
        color: <?php echo $config->inputtextcolorcustomer ?>;

    }

    .contact h3, .contact contact-result {
        color: <?php echo $config->titlecolorcustomer ?>;
    }

    .contact .message-content {
        <?php if ($config->allowUpload == "yes") { ?> height: 240px;
        <?php } else { ?> height: 200px;
        <?php } ?>
    }
</style>

<div class="container">
    <div class="contact">
        <div class="row">
            <div class="span12">
                <h3><?php _e('Contact us', 'ticketsys'); ?></h3>

                <div class="row" style="margin-top:20px;">

                    <div class="contact-init">
                        <form id="uploadForm" enctype="multipart/form-data" action="upload.php" target="uploadFrame"
                              method="post">

                            <div class="row">
                                <div class="span5 offset1">

                                    <select class="message-division" class="">
                                        <option selected><?php _e('Division', 'ticketsys'); ?></option>
                                        <?php
                                        foreach ($config->divisions->division as $division) {
                                            echo "<option>" . $division . "</option>";
                                        }
                                        ?>
                                    </select>
                                    <select class="message-priority ">
                                        <option><?php _e('Priority', 'ticketsys'); ?></option>
                                        <option><?php _e('Urgent', 'ticketsys'); ?></option>
                                        <option><?php _e('High', 'ticketsys'); ?></option>
                                        <option><?php _e('Medium', 'ticketsys'); ?></option>
                                        <option><?php _e('Low', 'ticketsys'); ?></option>
                                    </select>
                                    <?php if ($config->productOption == "yes") { ?>
                                        <select class="message-product">
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

                                    <input class="span5 message-subject" type="text" placeholder="<?php _e('Subject', 'ticketsys'); ?>">
                                    <input class="span5 message-name" type="text" placeholder="<?php _e('Name', 'ticketsys'); ?>">
                                    <input class="span5 message-email" type="text" placeholder="<?php _e('Mail address', 'ticketsys'); ?>">
                                    <input class="span5 message-phone" type="text"
                                           placeholder="<?php _e('Phone number (Optional)', 'ticketsys'); ?>">
                                           <?php if ($config->allowUpload == "yes") { ?>
                                        <div style="text-align: left">
                                            <input id="uploadFile" name="uploadFile" type="file"/>
                                            <iframe id="uploadFrame" name="uploadFrame" src="#"></iframe>

                                        </div>
                                    <?php } ?>

                                </div>

                                <div class="span5 ">

                                    <textarea class="message-content" placeholder="<?php _e('Your message', 'ticketsys'); ?>"></textarea>

                                </div>

                                <div class="span10 offset1">
                                    <?php if ($config->recaptchaOption == "yes") { ?>
                                        <div class="span2">
                                            <div id="recaptcha" class="span1"></div>
                                        </div>
                                    <?php } else { ?>
                                        <input id="recaptcha_challenge_field" type="hidden" value="none"/>
                                        <input id="recaptcha_response_field" type="hidden" value="none"/>
                                    <?php } ?>
                                </div>

                                <div class="span10 offset1" style="margin-top: 15px;">
                                    <div style="width:100%; text-align: left;">
                                        <button class="btn" id="send-contact-message"><?php _e('Send', 'ticketsys'); ?></button>
                                    </div>
                                </div>

                        </form>
                    </div>
                </div>

            </div>

            <div class="contact-loader" style="display:none; width:40%; margin: 0 auto;">
                <div style="margin-bottom: 9px;" class="progress progress-info progress-striped active">
                    <div class="bar"
                         style="background-color:<?php echo $config->loadercolorcustomer ?>; width: 100%;"></div>
                </div>
            </div>

            <div class="contact-result" style="display:none; text-align: center;"></div>
        </div>
    </div>
</div>
</div>

<?php wp_enqueue_script('jquery'); ?>
<script type="text/javascript" src="http://www.google.com/recaptcha/api/js/recaptcha_ajax.js"></script>
<?php if ($config->recaptchaOption == "yes") { ?>
    <script type="text/javascript">
        function showRecaptcha() {
            Recaptcha.create(<?php echo "'" . $config->recaptchapublickey . "'"; ?>, document.getElementById("recaptcha"), {
                theme: "<?php echo $config->recaptchatheme; ?>",
                callback: Recaptcha.focus_response_field});
        }

        jQuery(document).ready(function() {
            showRecaptcha();
        });

    </script>
<?php } ?>