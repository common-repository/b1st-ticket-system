<?php
$this->require_once_includes('nocsrf');
$this->include_includes("functions");
$msg_sent = "";
$msg_content = "";
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

 <?php  $this->SetMessage(6) ; ?>


<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">

            <?php

            $current_theme = Array(
                'inputtextcolor' => $config->inputtextcolor,
                'inputcolor' => $config->inputcolor,
                'titlecolor' => $config->titlecolor,
                'loadercolor' => $config->loadercolor,
                'backgroundcolor' => $config->backgroundcolor,
                'edit_mode' => false,
                'theme_name' => 'default'
                );
            if (isset($_POST['ticketsys_lang_admin'])) {
                update_option('ticketsys_lang_admin', $_POST['ticketsys_lang_admin']);
            }

            if (isset($_POST['ticketsys_lang_user'])) {
                update_option('ticketsys_lang_user', $_POST['ticketsys_lang_user']);
            }

            if (isset($_POST['delete_theme'])) {


                $xml = new SimpleXMLElement($themes->asXML());

                $cpt = 0;
                foreach ($themes->theme as $theme) {
                    if ($theme->name == $_POST['delete_theme']) { // if found, we delete it
                        unset($xml->theme[$cpt]);
                    }
                    $cpt++;
                }

                $xml->saveXML(self::$PATH_THEMES);
                header("Location: " . $this->renderAction('deleted=1&tab=styles', null, false));
                exit;
            }
             if (isset($_POST['edit_theme_display'])) {

                $xml = new SimpleXMLElement($themes->asXML());
                $settingsPage='styles';
                foreach ($themes->theme as $theme) {
                    if ($theme->name == $_POST['edit_theme_display']) { // if found, we delete it
                        $current_theme = Array(
                            'inputtextcolor' => $theme->inputtextcolor,
                            'inputcolor' => $theme->inputcolor,
                            'titlecolor' => $theme->titlecolor,
                            'loadercolor' => $theme->loadercolor,
                            'backgroundcolor' => $theme->backgroundcolor,
                            'edit_mode' => true,
                            'theme_name' =>$theme->name
                        );
                    };
                }

            }
            if (isset($_POST['edit_theme'])) {
                $xml = new SimpleXMLElement($themes->asXML());

                $cpt = 0;
                foreach ($themes->theme as $theme) {
                    if ($theme->name == $_POST['edit_theme']) { // if found, we delete it
                        unset($xml->theme[$cpt]);
                    }
                    $cpt++;
                }

                $xml->saveXML(self::$PATH_THEMES);
            }
           

            if (isset($_POST['backgroundcolor']) && !empty($_POST['backgroundcolor']) &&
                    isset($_POST['loadercolor']) && !empty($_POST['loadercolor']) &&
                    isset($_POST['inputcolor']) && !empty($_POST['inputcolor']) &&
                    isset($_POST['inputtextcolor']) && !empty($_POST['inputtextcolor']) &&
                    isset($_POST['titlecolor']) && !empty($_POST['titlecolor']) &&
                    isset($_POST['nameTheme']) && !empty($_POST['nameTheme'])
            ) {
                $themesTmp = simplexml_load_file(self::$PATH_THEMES);

                $xml = new SimpleXMLElement($themesTmp->asXML());

                $themeAdded = $xml->addChild("theme");

                $themeAdded->addChild('name', $_POST['nameTheme']);
                $themeAdded->addChild('backgroundcolor', $_POST['backgroundcolor']);
                $themeAdded->addChild('loadercolor', $_POST['loadercolor']);
                $themeAdded->addChild('inputcolor', $_POST['inputcolor']);
                $themeAdded->addChild('inputtextcolor', $_POST['inputtextcolor']);
                $themeAdded->addChild('titlecolor', $_POST['titlecolor']);

                $xml->saveXML(self::$PATH_THEMES);
                if(isset($_POST['edit_theme']))
                    header("Location: " . $this->renderAction('edited=1&tab=styles', null, false));
                else
                    header("Location: " . $this->renderAction('created=1&tab=styles', null, false));
                exit;
            }

             if (isset($_POST['mailAuto']) && !empty($_POST['mailAuto']) &&
                    isset($_POST['subjectAuto']) && !empty($_POST['subjectAuto']) &&
                    isset($_POST['contentAuto']) && !empty($_POST['contentAuto'])
            ) {


                $xml = new SimpleXMLElement($config->asXML());

                unset($xml->responderMail);
                unset($xml->responderSubject);
                unset($xml->responderContent);


                $xml->addChild('responderMail', $_POST['mailAuto']);
                $xml->addChild('responderSubject', $_POST['subjectAuto']);
                $xml->addChild('responderContent', $_POST['contentAuto']);
                $xml->saveXML(self::$PATH_CONFIG);
                header("Location: " . $this->renderAction('configured=1&tab=responder', null, false));
                exit;
            }
            if (isset($_POST["email_username"]) ||
                    isset($_POST["email_password"]) ||
                    isset($_POST['email_client']) || isset($_POST['email_importaction']) || isset($_POST['email_deleteaction'])
            ) {


                $xml = new SimpleXMLElement($config->asXML());

                unset($xml->emailUsername);
                unset($xml->emailPassword);
                unset($xml->emailClient);
                unset($xml->emailimportaction);
                unset($xml->emaildeleteaction);

                //$converter = new Encryption;
                //$emailpassword_encoded = $converter->encode($_POST['gmail_password']);
                $xml->addChild('emailUsername', $_POST['email_username']);
                $xml->addChild('emailPassword', $_POST['email_password']);
                $xml->addChild('emailClient', $_POST['email_client']);
                $xml->addChild('emailimportaction', $_POST['email_importaction']);
                $xml->addChild('emaildeleteaction', $_POST['email_deleteaction']);

                $xml->saveXML(self::$PATH_CONFIG);
                header("Location: " . $this->renderAction('email=1&tab=email', null, false));
                exit;
            }


            if (isset($_POST["key_consumer"]) || isset($_POST["key_secret_consumer"]) || isset($_POST["key_token"]) || isset($_POST["key_secret_token"]) || isset($_POST["twitter_username"])
            ) {

                $xml = new SimpleXMLElement($config->asXML());

                unset($xml->keyconsumer);
                unset($xml->keysecretconsumer);
                unset($xml->keytoken);
                unset($xml->keysecrettoken);

                $xml->addChild('keyconsumer', $_POST['key_consumer']);
                $xml->addChild('keysecretconsumer', $_POST['key_secret_consumer']);
                $xml->addChild('keytoken', $_POST['key_token']);
                $xml->addChild('keysecrettoken', $_POST['key_secret_token']);

                $xml->saveXML(self::$PATH_CONFIG);
                header("Location: " . $this->renderAction('twitter=1&tab=twitter', null, false));

                exit;
            }


            if (isset($_POST['new_theme'])) { // left form validation : theme switching
                $xml = new SimpleXMLElement($config->asXML());

                unset($xml->productOption);
                unset($xml->recaptchaOption);
                unset($xml->mailNotification);
                unset($xml->allowUpload);
                unset($xml->allowRating);
                unset($xml->deleteconfirm);
                unset($xml->forceregister);

                if (isset($_POST["ticketsperpage"])) {
                    unset($xml->nbPerPage);
                    $xml->addChild("nbPerPage", $_POST["ticketsperpage"]);
                }

                if (isset($_POST["inputproduct"])) {
                    $xml->addChild("productOption", "yes");
                } else {
                    $xml->addChild("productOption", "no");
                }

                if (isset($_POST["mailnotif"])) {
                    $xml->addChild("mailNotification", "yes");
                } else {
                    $xml->addChild("mailNotification", "no");
                }

                if (isset($_POST["inputrecaptcha"])) {
                    $xml->addChild("recaptchaOption", "yes");
                } else {
                    $xml->addChild("recaptchaOption", "no");
                }


                if (isset($_POST["allowUpload"])) {
                    $xml->addChild("allowUpload", "yes");
                } else {
                    $xml->addChild("allowUpload", "no");
                }  
                if (isset($_POST["forceregister"])) {
                    $xml->addChild("forceregister", "yes");
                } else {
                    $xml->addChild("forceregister", "no");
                }
                  if (isset($_POST["deleteconfirm"])) {
                    $xml->addChild("deleteconfirm", "yes");
                } else {
                    $xml->addChild("deleteconfirm", "no");
                }

                if (isset($_POST["allowRating"])) {
                    $xml->addChild("allowRating", "yes");
                } else {
                    $xml->addChild("allowRating", "no");
                }

                if (isset($_POST["uploadPath"])) {
                    unset($xml->uploadPath);
                    $xml->addChild("uploadPath", $_POST["uploadPath"]);
                }

                if (isset($_POST["layout"])) {
                    unset($xml->layout);
                    $xml->addChild("layout", $_POST["layout"]);
                }

                if (isset($_POST['new_theme_admin'])) {

                    foreach ($themes->theme as $theme) {
                        if ($theme->name == $_POST['new_theme_admin']) { // if found, we apply it
                            // We remove
                            unset($xml->themename);
                            unset($xml->backgroundcolor);
                            unset($xml->loadercolor);
                            unset($xml->inputcolor);
                            unset($xml->inputtextcolor);
                            unset($xml->titlecolor);

                            // We add the new one
                            $xml->addChild('themename', $theme->name);
                            $xml->addChild('backgroundcolor', $theme->backgroundcolor);
                            $xml->addChild('loadercolor', $theme->loadercolor);
                            $xml->addChild('inputcolor', $theme->inputcolor);
                            $xml->addChild('inputtextcolor', $theme->inputtextcolor);
                            $xml->addChild('titlecolor', $theme->titlecolor);
                        }
                    }
                }

                if (isset($_POST['new_theme'])) {
                    foreach ($themes->theme as $theme) {
                        if ($theme->name == $_POST['new_theme']) { // if found, we apply it
                            // We remove
                            unset($xml->themenamecustomer);
                            unset($xml->backgroundcolorcustomer);
                            unset($xml->loadercolorcustomer);
                            unset($xml->inputcolorcustomer);
                            unset($xml->inputtextcolorcustomer);
                            unset($xml->titlecolorcustomer);

                            // We add the new one
                            $xml->addChild('themenamecustomer', $theme->name);
                            $xml->addChild('backgroundcolorcustomer', $theme->backgroundcolor);
                            $xml->addChild('loadercolorcustomer', $theme->loadercolor);
                            $xml->addChild('inputcolorcustomer', $theme->inputcolor);
                            $xml->addChild('inputtextcolorcustomer', $theme->inputtextcolor);
                            $xml->addChild('titlecolorcustomer', $theme->titlecolor);
                        }
                    }
                }

                $xml->saveXML(self::$PATH_CONFIG);

                header("Location: " . $this->renderAction('saved=1&tab=styles', null, false));

                exit;
            }

            if (isset($_POST["recaptchapublickey"]) ||
                    isset($_POST["recaptchaprivatekey"]) ||
                    isset($_POST["recaptchatheme"])
            ) {
                $xml = new SimpleXMLElement($config->asXML());
                unset($xml->recaptchaprivatekey);
                unset($xml->recaptchapublickey);
                unset($xml->recaptchatheme);
                $xml->addChild("recaptchaprivatekey", $_POST["recaptchaprivatekey"]);
                $xml->addChild("recaptchapublickey", $_POST["recaptchapublickey"]);
                $xml->addChild("recaptchatheme", $_POST["recaptchatheme"]);

                $xml->saveXML(self::$PATH_CONFIG);
                header("Location: " . $this->renderAction('recaptcha=1&tab=recaptcha', null, false));
                exit;
            }

            if (isset($_POST["akismit"])) {
                $xml = new SimpleXMLElement($config->asXML());
                unset($xml->akismit);
                $xml->addChild("akismit", $_POST["akismit"]);


                unset($xml->spamdelete);
                if (isset($_POST["spamdelete"])) {
                    $xml->addChild("spamdelete", "yes");
                } else {
                    $xml->addChild("spamdelete", "no");
                }

                unset($xml->spamshow);
                if (isset($_POST["spamshow"])) {
                    $xml->addChild("spamshow", "yes");
                } else {
                    $xml->addChild("spamshow", "no");
                }

                $xml->saveXML(self::$PATH_CONFIG);
                header("Location: " . $this->renderAction('akismit=1&tab=akismit', null, false));

                exit;
            }

            if (isset($_POST["metascan"])) {
                $xml = new SimpleXMLElement($config->asXML());
                unset($xml->metascan);
                $xml->addChild("metascan", $_POST["metascan"]);

                $xml->saveXML(self::$PATH_CONFIG);
                header("Location: " . $this->renderAction('deleted=1&tab=metascan', null, false));

                exit;
            }


            ?>


            <h3><?php _e('Administration', 'ticketsys'); ?></h3>

            <ul class="nav nav-tabs" id="mainMenu">
                <?php createMenu(6, $_SESSION['admin']); ?>
            </ul>

            <?php
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
            ?>



            <div class="tab-content">
                <div class="row-fluid">
                    <div class="offset">
                        <ul class="nav nav-tabs span14" id="menuSettings">
                            <li class="active"><a href="#global"><?php _e('Global settings', 'ticketsys'); ?></a></li>
                            <li><a href="#styles"><?php _e('Customization & styles', 'ticketsys'); ?></a></li>
                            <li><a href="#responder"><?php _e('Auto responder', 'ticketsys'); ?></a></li>
                            <li><a href="#email"><?php _e('Email integration', 'ticketsys'); ?></a></li>
                            <li><a href="#twitter"><?php _e('Twitter integration', 'ticketsys'); ?></a></li>
                            <li><a href="#recaptcha"><?php _e('reCAPTCHA', 'ticketsys'); ?></a></li>
                            <li><a href="#akismit"><?php _e('A.kis.met', 'ticketsys'); ?></a></li>
                            <li><a href="#metascan"><?php _e('OPSWAT metascan online', 'ticketsys'); ?></a></li>
                        </ul>
                    </div>
                </div>


                <?php
                if (isset($_GET["edited"])) {
                    echo '<div style="" class="row-fluid"><div class="span10 offset1"><div class="alert alert-success">' . __('The theme has been well edited.', 'ticketsys') . '</div></div></div>';
                }

                if (isset($_GET["created"])) {
                    echo '<div style="" class="row-fluid"><div class="span10 offset1"><div class="alert alert-success">' . __('The theme has been well created.', 'ticketsys') . '</div></div></div>';
                }

                if (isset($_GET["deleted"])) {
                    echo '<div style="" class="row-fluid"><div class="span10 offset1"><div class="alert alert-success">' . __('The theme has been well deleted.', 'ticketsys') . '</div></div></div>';
                }

                if (isset($_GET["saved"])) {
                    echo '<div style="" class="row-fluid"><div class="span10 offset1"><div class="alert alert-success">' . __('Changes saved.', 'ticketsys') . '</div></div></div>';
                }

                if (isset($_GET["configured"])) {
                    echo '<div style="" class="row-fluid"><div class="span10 offset1"><div class="alert alert-success">' . __('Auto-responder configured.', 'ticketsys') . '</div></div></div>';
                }

                if (isset($_GET["email"])) {
                    echo '<div style="" class="row-fluid"><div class="span10 offset1"><div class="alert alert-success">' . __('Your email account has been well configured.', 'ticketsys') . '</div></div></div>';
                }

                if (isset($_GET["twitter"])) {
                    echo '<div style="" class="row-fluid"><div class="span10 offset1"><div class="alert alert-success">' . __('Your twitter account has been well configured.', 'ticketsys') . '</div></div></div>';
                }

                if (isset($_GET["recaptcha"])) {
                    echo '<div style="" class="row-fluid"><div class="span10 offset1"><div class="alert alert-success">' . __('Your reCAPTCHA account has been well configured.', 'ticketsys') . '</div></div></div>';
                }

                if (isset($_GET["akismit"])) {
                    echo '<div style="" class="row-fluid"><div class="span10 offset1"><div class="alert alert-success">' . __('Your A.kis.mit account has been well configured.', 'ticketsys') . '</div></div></div>';
                }

                if (isset($_GET["metascan"])) {
                    echo '<div style="" class="row-fluid"><div class="span10 offset1"><div class="alert alert-success">' . __('Your OPSWAT metascan online account has been well configured.', 'ticketsys') . '</div></div></div>';
                }

                if (isset($_GET["server"])) {
                    echo '<div style="" class="row-fluid"><div class="span10 offset1"><div class="alert alert-success">' . __('Your MySql connection has been well configured.', 'ticketsys') . '</div></div></div>';
                }
                if (isset($error) && $error == 2) {
                    echo '<div class="alert alert-error">' . $CSRF_error . '</div>';
                    exit;
                }
                ?>
                <div class="tab-pane fade active in" id="global" style="margin-left:30px;">
                    <div class="row-fluid">
                        <div class="span10 offset1">
                            <form method="post" action="<?php $this->renderAction(); ?>">
                                <fieldset>
                                    <legend><?php _e('Global settings', 'ticketsys'); ?></legend>
                                    <div class="row-fluid" style="margin-top:7px;">
                                        <div class="span10">
                                            <div class="input-prepend">
                                                <button class="btn submit" type="button" style="width: 200px;"><?php _e('Choose a layout', 'ticketsys'); ?> :
                                                </button>
                                                <select name="layout">
                                                    <?php
                                                    $i = 1;
                                                    while ($i < 3) {
                                                        echo '<option value="' . $i . '" ';

                                                        if ($config->layout == $i) {
                                                            echo ' selected="selected" ';
                                                        }

                                                        echo ' >' . __('Layout', 'ticketsys') . $i . '</option>';

                                                        $i++;
                                                    }
                                                    ?>

                                                </select>
                                            </div>

                                            <div class="input-prepend">
                                                <button class="btn submit" type="button" style="width: 200px;"><?php _e('Choose a customer theme', 'ticketsys'); ?>:</button>
                                                <select name="new_theme">
                                                    <?php
                                                    foreach ($themes->theme as $theme) {
                                                        echo '<option value="' . $theme->name . '" ';

                                                        if ($theme->name == $config->themenamecustomer . '') {
                                                            echo ' selected="selected" ';
                                                        }

                                                        echo ' >' . $theme->name . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="input-prepend">
                                                <button class="btn submit" type="button" style="width: 200px;"><?php _e('Choose an admin theme', 'ticketsys'); ?>
                                                    :
                                                </button>
                                                <select name="new_theme_admin">
                                                    <?php
                                                    foreach ($themes->theme as $theme) {
                                                        echo '<option value="' . $theme->name . '" ';

                                                        if ($theme->name == $config->themename . '') {
                                                            echo ' selected="selected" ';
                                                        }

                                                        echo ' >' . $theme->name . '</option>';
                                                    }
                                                    ?>

                                                </select>
                                            </div>



                                            <?php
                                            if ($config->productOption == "yes") {
                                                $checkedProduct = "checked";
                                            } else {
                                                $checkedProduct = "";
                                            }

                                            if ($config->recaptchaOption == "yes") {
                                                $checkedCaptcha = "checked";
                                            } else {
                                                $checkedCaptcha = "";
                                            }

                                            if ($config->mailNotification == "yes") {
                                                $mailNotif = "checked";
                                            } else {
                                                $mailNotif = "";
                                            }

                                            if ($config->allowUpload == "yes") {
                                                $allowUpload = "checked";
                                            } else {
                                                $allowUpload = "";
                                            }

                                            if ($config->allowRating == "yes") {
                                                $allowRating = "checked";
                                            } else {
                                                $allowRating = "";
                                            }

                                            if ($config->spamdelete == "yes") {
                                                $checkedspamdelete = "checked";
                                            } else {
                                                $checkedspamdelete = "";
                                            }

                                            if ($config->spamshow == "yes") {
                                                $checkedspamshow = "checked";
                                            } else {
                                                $checkedspamshow = "";
                                            }
                                             if ($config->deleteconfirm == "yes") {
                                                $deleteconfirm = "checked";
                                            } else {
                                                $deleteconfirm = "";
                                            } 
                                             if ($config->forceregister == "yes") {
                                                $forceregister = "checked";
                                            } else {
                                                $forceregister = "";
                                            }
                                            ?>
                                            <br/>
                                            <fieldset>
                                                <legend><?php _e('Fields to show / hide', 'ticketsys'); ?></legend>
                                            </fieldset>
                                            <table>
                                                <tr>
                                                    <td><?php _e('Product input', 'ticketsys'); ?> :</td>
                                                    <td><input style="vertical-align:1px;" <?php echo $checkedProduct ?>
                                                               name="inputproduct" type="checkbox"></td>
                                                </tr>
                                                <tr>
                                                    <td><?php _e('reCAPTCHA input', 'ticketsys'); ?> :</td>
                                                    <td><input style="vertical-align:1px;" <?php echo $checkedCaptcha ?>
                                                               name="inputrecaptcha" type="checkbox"></td>
                                                </tr>
                                                <tr>
                                                    <td><?php _e('Allow uploads', 'ticketsys'); ?> :</td>
                                                    <td><input style="vertical-align:1px;" <?php echo $allowUpload ?>
                                                               name="allowUpload" type="checkbox"></td>
                                                </tr>
                                                  <tr>
                                                    <td><?php _e('Confirm delete', 'ticketsys'); ?> :</td>
                                                    <td><input style="vertical-align:1px;" <?php echo $deleteconfirm ?>
                                                               name="deleteconfirm" type="checkbox"></td>
                                                </tr>
                                                   <tr>
                                                    <td><?php _e('Force registeration', 'ticketsys'); ?> :</td>
                                                    <td><input style="vertical-align:1px;" <?php echo $forceregister ?>
                                                               name="forceregister" type="checkbox"></td>
                                                </tr>

                                                <tr>
                                                    <td><?php _e('Allow rating', 'ticketsys'); ?> :</td>
                                                    <td><input style="vertical-align:1px;" <?php echo $allowRating ?>
                                                               name="allowRating" type="checkbox"></td>
                                                </tr>

                                                <tr>
                                                    <td><?php _e('Maximum uploads', 'ticketsys'); ?>: </td>
                                                    <td><input class="input-mini" value="<?php echo $config->maxuploads ?>" style="vertical-align:1px;" name="maxuploads"
                                                               type="text"> </input></td>
                                                </tr>

                                            </table>

                                            <hr/>

                                            <table>
                                                <tr>
                                                    <td><?php _e('Send mail notification', 'ticketsys'); ?> :</td>
                                                    <td><input style="vertical-align:1px;" <?php echo $mailNotif ?> name="mailnotif"
                                                               type="checkbox"></td>
                                                </tr>
                                            </table>



                                            <div class="input-prepend">
                                                <button class="btn submit" type="button" style="width: 200px;"><?php _e('Tickets per page', 'ticketsys'); ?> :
                                                </button>

                                                <select style="vertical-align:1px;" name="ticketsperpage">
                                                    <?php
                                                    $i = 0;
                                                    $i_val = array(5, 10, 20, 50, 100);
                                                    while ($i < 5) {
                                                        echo '<option value="' . $i_val[$i] . '" ';

                                                        if ($config->nbPerPage == $i_val[$i]) {
                                                            echo ' selected="selected" ';
                                                        }
                                                        echo '">' . $i_val[$i] . '</option>';
                                                        $i++;
                                                    }
                                                    ?>

                                                </select>


                                            </div>

                                        </div>

                                    </div>
                                </fieldset>

                                <hr/>

                                <?php
                                $ticketsys_lang_admin = get_option('ticketsys_lang_admin');
                                $ticketsys_lang_user = get_option('ticketsys_lang_user');

                                $languages = array();
                                $path = dirname(dirname(dirname(__FILE__))) . '/languages';
                                $files = scandir($path);

                                foreach ($files as $file) {
                                    $ext = pathinfo($file, PATHINFO_EXTENSION);
                                    if ($ext == 'mo') {
                                        $file = basename($file, '.mo');
                                        $file = str_replace('ticketsys-', '', $file);
                                        $languages[] = $file;
                                    }
                                }
                                ?>

                                <table>
                                    <tr>
                                        <td><?php _e('Admin Language', 'ticketsys'); ?> :</td>
                                        <td>
                                            <select name="ticketsys_lang_admin">
                                                <?php foreach ($languages as $language) { ?>
                                                    <option value="<?php echo $language; ?>" <?php if ($ticketsys_lang_admin == $language) echo 'selected'; ?>><?php echo $language; ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><?php _e('User Language', 'ticketsys'); ?> :</td>
                                        <td>
                                            <select name="ticketsys_lang_user">
                                                <?php foreach ($languages as $language) { ?>
                                                    <option value="<?php echo $language; ?>" <?php if ($ticketsys_lang_user == $language) echo 'selected'; ?>><?php echo $language; ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                    </tr>
                                </table>

                                <div style="text-align:center;margin-top: 20px;">
                                    <button type="<?php $this->SetType(); ?>" <?php $this->SetCability(); ?> class="btn btn-success"><?php _e('Save changes', 'ticketsys'); ?></button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

                <div id="server" class="tab-pane fade" style="margin-left:30px;">
                    <div class="row-fluid">
                        <div class="span10 offset1">

                            <form method="post" action="" style="margin:0;">
                                <fieldset>
                                    <legend><?php _e('MySql connection configuration', 'ticketsys'); ?></legend>

                                    <div class="alert alert-danger"><?php _e('Be Careful. In case you make any incorrect modifications and lose control over your admin panel download config.xml file and make the correct corresponding changes then upload it again.', 'ticketsys'); ?></div>

                                    <div class="row-fluid">
                                        <div class="span10">
                                            <div class="input-prepend" class="span10">
                                                <button class="btn submit" type="button" name="inputdbServer" style="width:150px;">
                                                    <?php _e('Database server', 'ticketsys'); ?>
                                                </button>
                                                <input name="dbServer" type="text" value="<?php echo $config->dbServer ?>">
                                            </div>
                                            <div class="input-prepend" class="span10">
                                                <button class="btn submit" type="button" name="inputdbUsername"
                                                        style="width:150px;"><?php _e('Database username', 'ticketsys'); ?>
                                                </button>
                                                <input name="dbUsername" type="text" value="<?php echo $config->dbUsername ?>">
                                            </div>
                                            <div class="input-prepend" class="span10">
                                                <button class="btn submit" type="button" name="inputdbPassword"
                                                        style="width:150px;"><?php _e('Database password', 'ticketsys'); ?>
                                                </button>
                                                <input name="dbPassword" type="password" value="<?php echo $config->dbPassword ?>">
                                            </div>
                                            <div class="input-prepend" class="span10">
                                                <button class="btn submit" type="button" name="inputdbName" style="width:150px;">
                                                    Database name
                                                </button>
                                                <input name="dbName" type="text" value="<?php echo $config->dbName ?>">
                                            </div>
                                        </div>
                                    </div>

                                </fieldset>

                                <div style="text-align:center;margin-top: 20px;">
                                    <button type="submit" class="btn btn-success"><?php _e('Configure', 'ticketsys'); ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div id="styles" class="tab-pane fade" style="margin-left:30px;">
                    <div class="row-fluid">
                        <div class="span6 offset1">
                            <fieldset>
                                <legend><?php _e('Create a theme', 'ticketsys'); ?></legend>
                                <form method="post" action="" style="margin:0;">
                                <?php if($current_theme['edit_mode']){ ?>
                                        <input type="hidden" name="edit_theme" value="<?php echo $current_theme['theme_name']?>"/>
                                    <?php }else{ ?>
                                        <input type="hidden" name="addthememode" value="<?php echo $current_theme['theme_name']?>"/>
                                    <?php }?>
                                    <div class="row-fluid" style="margin-top:7px;">
                                        <div class="span3 offset2">
                                            <div id="main" style="text-align:center;">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="row-fluid" style="text-align:center; margin-top:7px;">
                                        <div class="input-prepend">
                                            <button class="btn submit" type="button" style="width:150px;"><?php _e('Theme Name', 'ticketsys'); ?></button>
                                            <input name="nameTheme" type="text" style="background-color:white;" value="<?php if($current_theme['edit_mode'])echo $current_theme['theme_name']?>">
                                        </div>
                                    </div>
                                    <div class="row-fluid" style="text-align:center; margin-top:7px;">
                                        <div class="input-prepend" class="span3">
                                            <button class="btn submit" type="button" name="inputTextColor" style="width:150px;">
                                                <?php _e('Input Text Color', 'ticketsys'); ?>
                                            </button>
                                            <input class="color" id="color1" name="inputtextcolor" type="text"
                                                   style="background-color:<?php echo $current_theme['inputtextcolor'] ?>;"
                                                   value="<?php echo $current_theme['inputtextcolor'] ?>">
                                        </div>
                                        <div class="input-prepend" class="span3">
                                            <button class="btn submit" type="button" name="inputColor" style="width:150px;">
                                                <?php _e('Input Color', 'ticketsys'); ?>
                                            </button>
                                            <input class="color" id="color2" name="inputcolor" placeholder="Search"
                                                   id="appendedInputButtons"
                                                   style="background-color:<?php echo $current_theme['inputcolor'] ?>;" type="text"
                                                   value="<?php echo $current_theme['inputcolor'] ?>">
                                        </div>
                                    </div>
                                    <div class="row-fluid" style="text-align:center; margin-top:7px;">
                                        <div class="input-prepend" class="span3">
                                            <button class="btn submit" type="button" name="titleColor" style="width:150px;">
                                                <?php _e('Title Color', 'ticketsys'); ?>
                                            </button>
                                            <input name="titlecolor" class="color" id="color3" placeholder="Search"
                                                   id="appendedInputButtons"
                                                   style="background-color:<?php echo $current_theme['titlecolor'] ?>;" type="text"
                                                   value="<?php echo $current_theme['titlecolor'] ?>">
                                        </div>
                                        <div class="input-prepend" class="span3">
                                            <button class="btn submit" type="button" name="loaderColor" style="width:150px;">
                                                <?php _e('Loader Color', 'ticketsys'); ?>
                                            </button>
                                            <input name="loadercolor" class="color" id="color4" placeholder="Search"
                                                   id="appendedInputButtons"
                                                   style="background-color:<?php echo $current_theme['loadercolor'] ?>;" type="text"
                                                   value="<?php echo $current_theme['loadercolor'] ?>">
                                        </div>
                                    </div>
                                    <div class="row-fluid" style="text-align:center; margin-top:7px;">
                                        <div class="input-prepend" class="span3">
                                            <button class="btn submit" type="button" name="bgColor" style="width:150px;">
                                                <?php _e('Background Color', 'ticketsys'); ?>
                                            </button>
                                            <input name="backgroundcolor" class="color" id="color5" placeholder="Search"
                                                   id="appendedInputButtons"
                                                   style="background-color:<?php echo $current_theme['backgroundcolor'] ?>;" type="text"
                                                   value="<?php echo $current_theme['backgroundcolor'] ?>">
                                        </div>
                                    </div>
                                    <div style="text-align:center;margin-top: 20px;">
                                    <?php if(!$current_theme['edit_mode']){ ?>
                                        <button type="submit" class="btn btn-success"><?php _e('Create', 'ticketsys'); ?></button>
                                    <?php }else{?>
                                        <button type="submit" class="btn btn-success"><?php _e('Edit', 'ticketsys'); ?></button>
                                        <?php }?>
                                    </div>
                                </form>
                            </fieldset>
                        </div>
                        <div class="span5">
                            <form method="post" action="" style="margin:0;">
                                <fieldset>
                                    <legend><?php _e('Edit a theme', 'ticketsys'); ?></legend>


                                </fieldset>

                                <div style="text-align:center;margin-top: 20px;">
                                    <select name="edit_theme_display">
                                        <?php
                                        //$themes = simplexml_load_file("themes.xml");
                                        foreach ($themes->theme as $theme) {
                                            echo "<option value=\"" . $theme->name . "\">" . $theme->name . "</option>";
                                        }
                                        ?>

                                    </select>
                                    <br/>
                                    <button type="submit" class="btn btn-success" style="text-align:center;margin-top: 20px;"><?php _e('Edit', 'ticketsys'); ?>
                                    </button>
                                </div>
                            </form>
                            <form method="post" action="" style="margin:0;">
                                <fieldset>
                                    <legend><?php _e('Delete a theme', 'ticketsys'); ?></legend>


                                </fieldset>

                                <div style="text-align:center;margin-top: 20px;">
                                    <select name="delete_theme">
                                        <?php
                                        //$themes = simplexml_load_file("themes.xml");
                                        foreach ($themes->theme as $theme) {
                                            echo "<option value=\"" . $theme->name . "\">" . $theme->name . "</option>";
                                        }
                                        ?>

                                    </select>
                                    <br/>
                                    <button type="submit" class="btn btn-success" style="text-align:center;margin-top: 20px;">
                                        <?php _e('Delete', 'ticketsys'); ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="responder" style="margin-left:30px;">
                    <div class="row-fluid">
                        <div class="span10 offset1">
                            <form method="post" action="" style="margin:0;">
                                <fieldset>
                                    <legend><?php _e('Auto-responder configuration', 'ticketsys'); ?> </legend>
                                    <input value="<?php echo $config->responderMail ?>" name="mailAuto" type="text">
                                    <input value="<?php echo $config->responderSubject; ?>" name="subjectAuto" type="text">
                                    <textarea name="contentAuto"><?php echo $config->responderContent; ?></textarea>
                                </fieldset>

                                <div style="text-align:center;margin-top: 20px;">
                                    <button type="submit" class="btn btn-success"><?php _e('Configure', 'ticketsys'); ?> </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div id="email" class="tab-pane fade" style="margin-left:30px;">
                    <div class="row-fluid">
                        <div class="span10 offset1">

                            <form method="post" action="" style="margin:0;">
                                <fieldset>
                                    <legend><?php _e('Email configuration', 'ticketsys'); ?></legend>

                                    <select style="vertical-align:1px;" name="email_client" id='email_client'
                                            onchange="showEmailInfo()">
                                                <?php
                                                $i = 0;
                                                $i_val = array('Gmail', 'Yahoo', 'Horde');
                                                while ($i < 3) {
                                                    echo '<option value="' . $i_val[$i] . '" ';

                                                    if ($config->emailClient == $i_val[$i]) {
                                                        echo ' selected="selected" ';
                                                    }
                                                    echo '">' . $i_val[$i] . '</option>';
                                                    $i++;
                                                }
                                                ?>

                                    </select>
                                    <script type="text/javascript">
                                        function showEmailInfo() {
                                            var e = document.getElementById("email_client");
                                            if (e.value == 'Gmail')
                                                $msg = "<?php printf(__("You have to create an email <a href='%s' target='_blank'>here</a>. Paste your account credential below.", 'ticketsys'), 'https://gmail.com'); ?>";
                                            else if (e.value == 'Yahoo')
                                                $msg = "<?php printf(__("You have to create an email <a href='%s' target='_blank'>here</a>. Paste your account credential below.", 'ticketsys'), 'https://login.yahoo.com/'); ?>";
                                            else if (e.value == 'Horde')
                                                $msg = "<?php _e('You have to create an email at your Plesk/cPanel control panel.', 'ticketsys'); ?>";

                                            document.getElementById("email_message").innerHTML = $msg;
                                        }

                                    </script>

                                    <div class='alert alert-info' id="email_message"><?php printf(__("You have to create an email <a href='%s' target='_blank'>here</a>. Paste your account credential below.", 'ticketsys'), 'https://gmail.com'); ?></div>


                                    <div class="row-fluid">
                                        <div class="span10">
                                            <div class="input-prepend" class="span10">
                                                <button class="btn submit" type="button" name="inputemailusername"
                                                        style="width:150px;"><?php _e('Email', 'ticketsys'); ?>
                                                </button>
                                                <input name="email_username" type="text"
                                                       value="<?php echo $config->emailUsername ?>">
                                            </div>
                                            <div class="input-prepend" class="span10">
                                                <button class="btn submit" type="button" name="inputemailpassword"
                                                        style="width:150px;"><?php _e('Password', 'ticketsys'); ?>
                                                </button>
                                                <input name="email_password" type="password"
                                                       value="<?php echo $config->emailPassword ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <fieldset>
                                        <legend><?php _e('Importing actions', 'ticketsys'); ?></legend>
                                    </fieldset>
                                    <div class="row-fluid">
                                        <div class="span10">
                                            <div class="input-prepend" class="span10">
                                                <button class="btn submit" type="button"
                                                        style="width:150px;"><?php _e('Imported mail action', 'ticketsys'); ?>
                                                </button>
                                                <select style="vertical-align:1px;" name="email_importaction">
                                                    <option <?php echo ($config->emailimportaction == 'read') ? 'selected=selected' : '' ?>value="read"><?php _e('Mark as read', 'ticketsys'); ?></option>
                                                    <option <?php echo ($config->emailimportaction == 'delete') ? 'selected=selected' : '' ?> value="delete"><?php _e('Delete from server', 'ticketsys'); ?></option>
                                                </select>

                                            </div>
                                            <div class="input-prepend" class="span10">
                                                <button class="btn submit" type="button"
                                                        style="width:150px;"><?php _e('Deleted mail action', 'ticketsys'); ?>
                                                </button>
                                                <select style="vertical-align:1px;" name="email_deleteaction">
                                                    <option <?php echo ($config->emaildeleteaction == 'read') ? 'selected=selected' : '' ?>value="read"><?php _e('Mark as read', 'ticketsys'); ?></option>
                                                    <option <?php echo ($config->emaildeleteaction == 'delete') ? 'selected=selected' : '' ?> value="delete"><?php _e('Delete from server', 'ticketsys'); ?></option>
                                                </select>

                                            </div>

                                        </div>
                                    </div>

                                </fieldset>

                                <div style="text-align:center;margin-top: 20px;">
                                    <button type="submit" class="btn btn-success"><?php _e('Configure', 'ticketsys'); ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div id="twitter" class="tab-pane fade" style="margin-left:30px;">
                    <div class="row-fluid">
                        <div class="span10 offset1">
                            <form method="post" action="" style="margin:0;">
                                <fieldset>
                                    <legend><?php _e('Twitter configuration', 'ticketsys'); ?></legend>

                                    <div class="alert alert-info">
                                        <?php printf(__('You have to create an app <a href="%s"
                                           target="_blank">here</a>. Paste your keys below and please be sure that your tokens have the "Read, write, and direct messages" access level.', 'ticketsys'), 'https://dev.twitter.com/apps/new'); ?>
                                    </div>
                                    <div class="row-fluid">
                                        <div class="span10">
                                            <div class="input-prepend" class="span10">
                                                <button class="btn submit" type="button" name="inputConsumerKey"
                                                        style="width:150px;"><?php _e('Consumer key', 'ticketsys'); ?>
                                                </button>
                                                <input name="key_consumer" type="text" value="<?php echo $config->keyconsumer ?>">
                                            </div>
                                            <div class="input-prepend" class="span10">
                                                <button class="btn submit" type="button" name="inputConsumerSecret"
                                                        style="width:150px;"><?php _e('Consumer secret', 'ticketsys'); ?>
                                                </button>
                                                <input name="key_secret_consumer" type="text"
                                                       value="<?php echo $config->keysecretconsumer ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row-fluid">
                                        <div class="span10">
                                            <div class="input-prepend" class="span10">
                                                <button class="btn submit" type="button" name="inputAccessToken"
                                                        style="width:150px;"><?php _e('Access token', 'ticketsys'); ?>
                                                </button>
                                                <input name="key_token" type="text" value="<?php echo $config->keytoken ?>">
                                            </div>
                                            <div class="input-prepend" class="span6">
                                                <button class="btn submit" type="button" name="inputAccessSecret"
                                                        style="width:150px;"><?php _e('Token secret', 'ticketsys'); ?>
                                                </button>
                                                <input name="key_secret_token" type="text"
                                                       value="<?php echo $config->keysecrettoken ?>">
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <div style="text-align:center;margin-top: 20px;">
                                    <button type="submit" class="btn btn-success"><?php _e('Configure', 'ticketsys'); ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="recaptcha" style="margin-left:30px;">
                    <div class="row-fluid">
                        <div class="span10 offset1">
                            <form method="post" action="" style="margin:0;">
                                <fieldset>
                                    <legend><?php _e('reCAPTCHA configuration', 'ticketsys'); ?></legend>
                                    <div class="alert alert-info">
                                        <?php
                                        printf(__('You have to get a key <a href="%s" target="_blank">here</a>. Paste your key below', 'ticketsys'), 'ticketsys');
                                        ?>
                                    </div>
                                    <div class = "row-fluid">
                                        <div class = "span10">

                                            <div class = "input-prepend" class = "span10">
                                                <button class = "btn submit" type = "button" name = "recaptcha_publickey"
                                                        style = "width:150px;"><?php _e('Public key', 'ticketsys');
                                        ?>
                                                </button>
                                                <input name="recaptchapublickey" type="text"
                                                       value="<?php echo $config->recaptchapublickey ?>">
                                            </div>

                                            <div class="input-prepend" class="span10">
                                                <button class="btn submit" type="button" name="recaptcha_privatekey"
                                                        style="width:150px;"><?php _e('Private key', 'ticketsys'); ?>
                                                </button>
                                                <input name="recaptchaprivatekey" type="text"
                                                       value="<?php echo $config->recaptchaprivatekey ?>">
                                            </div>


                                            <div class="input-prepend">
                                                <button class="btn submit" type="button" style="width: 150px;"><?php _e('reCAPTCHA theme', 'ticketsys'); ?>:
                                                </button>

                                                <select style="vertical-align:1px;" name="recaptchatheme">
                                                    <?php
                                                    $i = 0;
                                                    $i_val = array('white', 'red', 'blackglass', 'clean');
                                                    while ($i < 4) {
                                                        echo '<option value="' . $i_val[$i] . '" ';

                                                        if ($config->recaptchatheme == $i_val[$i]) {
                                                            echo ' selected="selected" ';
                                                        }
                                                        echo '">' . $i_val[$i] . '</option>';
                                                        $i++;
                                                    }
                                                    ?>

                                                </select>


                                            </div>


                                        </div>
                                    </div>
                                </fieldset>

                                <div style="text-align:center;margin-top: 20px;">
                                    <button type="submit" class="btn btn-success"><?php _e('Configure', 'ticketsys'); ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="akismit" style="margin-left:30px;">
                    <div class="row-fluid">
                        <div class="span10 offset1">
                            <form method="post" action="" style="margin:0;">
                                <fieldset>
                                    <legend><?php _e('A.kis.mit configuration', 'ticketsys'); ?></legend>
                                    <div class="alert alert-info">
                                        <?php
                                        printf(__('You have to get a key <a href="%s" target="_blank">here</a>. Paste your key below.', 'ticketsys'), 'https://akismet.com/account/login/');
                                        ?>
                                    </div>
                                    <div class="row-fluid">
                                        <div class="span10">
                                            <div class="input-prepend" class="span10">
                                                <button class="btn submit" type="button" name="akismit_Key" style="width:150px;">
                                                    <?php _e('A.kis.mit Key', 'ticketsys'); ?>
                                                </button>
                                                <input name="akismit" type="text" value="<?php echo $config->akismit ?>">
                                            </div>

                                        </div>
                                    </div>

                                    <table>
                                        <tr>
                                            <td><?php _e('Delete spam automatically', 'ticketsys'); ?> :</td>
                                            <td><input style="vertical-align:1px;" <?php echo $checkedspamdelete ?>name="spamdelete"
                                                       type="checkbox"></td>
                                        </tr>

                                        <tr>
                                            <td><?php _e('Show spam messages', 'ticketsys'); ?> :</td>
                                            <td><input style="vertical-align:1px;" <?php echo $checkedspamshow ?> name="spamshow"
                                                       type="checkbox"></td>
                                        </tr>
                                    </table>

                                </fieldset>

                                <div style="text-align:center;margin-top: 20px;">
                                    <button type="submit" class="btn btn-success"><?php _e('Configure', 'ticketsys'); ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


                <div class="tab-pane fade" id="metascan" style="margin-left:30px;">
                    <div class="row-fluid">
                        <div class="span10 offset1">
                            <form method="post" action="" style="margin:0;">
                                <fieldset>
                                    <legend><?php _e('OPSWAT metascan configuration', 'ticketsys'); ?></legend>
                                    <div class="alert alert-info">
                                        <?php
                                        printf(__('You have to get a key <a href="%s" target="_blank">here</a>. Paste your key below.', 'ticketsys'), 'https://portal.opswat.com/');
                                        ?>
                                    </div>
                                    <div class="row-fluid">
                                        <div class="span10">
                                            <div class="input-prepend" class="span10">
                                                <button class="btn submit" type="button" name="metascan_Key" style="width:150px;">
                                                    <?php _e('Metascan Key', 'ticketsys'); ?>
                                                </button>
                                                <input name="metascan" type="text" value="<?php echo $config->metascan ?>">
                                            </div>

                                        </div>
                                    </div>


                                </fieldset>

                                <div style="text-align:center;margin-top: 20px;">
                                    <button type="submit" class="btn btn-success"><?php _e('Configure', 'ticketsys'); ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    (function($) {

        function invertColor(hexTripletColor) {
            var color = hexTripletColor;
            color = color.substring(1); // remove #
            color = parseInt(color, 16); // convert to integer
            color = 0xFFFFFF ^ color; // invert three bytes
            color = color.toString(16); // convert to hex
            color = ("000000" + color).slice(-6); // pad with leading zeros
            color = "#" + color; // prepend #
            return color;
        }

        $(".color").focus(function() {

            $('#colorpicker').remove();
            var id = $(this).attr("id");
            //console.log(id);
            $("#main").hide().html('<div id="colorpicker" class="colorpicker" style="display:block;background:none;position:static;"></div>').show();
            var cw = Raphael.colorwheel($(".colorpicker")[0], 150).color($(this).val() ? $(this).val() : "#F00");
            cw.onchange(function(color) {
                $('#' + id).val(color.hex);
                $('#' + id).css({
                    "color": invertColor(color.hex),
                    "background-color": color.hex
                });
                ;
                //hex_color.replace(/^#/,'')
            });

            // $('#colorpicker').farbtastic("#" + id);


        }).focusout(function() {


        });

        $("button.submit").click(function() {
            $("form").submit();

        });

        $('#menuSettings a[href="#global"]').tab('show');

        $('#menuSettings a').click(function(e) {
            e.preventDefault();
            $(this).tab('show');
        })

        <?php if(isset($settingsPage))
            echo "jQuery('a[href=#".$settingsPage."]').trigger('click');";
        ?>
    })(jQuery);
</script>

</body>
</html>
