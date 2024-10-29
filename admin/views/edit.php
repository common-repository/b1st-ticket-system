<!DOCTYPE html>
<html>
    <head>
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <title>Administration</title>
        <link href='http://fonts.googleapis.com/css?family=Monda' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" type="text/css" href="./styles/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="./styles/bootstrap-responsive.css">
        <link rel="stylesheet" media="screen" type="text/css" href="./styles/farbtastic.css"/>

    </head>
    <body>
        <?php
// Database
        $this->include_includes("config.php");
        ?>

        <div class="container">
            <div class="row">
                <div class="span12">

        <?php
        $msg_sent = "";
        $msg_content = "";


        if (isset($_SESSION['username'])) {

            if (isset($_POST['edit_backgroundcolor']) && !empty($_POST['edit_backgroundcolor']) &&
                    isset($_POST['edit_loadercolor']) && !empty($_POST['edit_loadercolor']) &&
                    isset($_POST['edit_inputcolor']) && !empty($_POST['edit_inputcolor']) &&
                    isset($_POST['edit_inputtextcolor']) && !empty($_POST['edit_inputtextcolor']) &&
                    isset($_POST['edit_titlecolor']) && !empty($_POST['edit_titlecolor']) &&
                    isset($_POST['edit_theme']) && !empty($_POST['edit_theme'])
            ) {

                //$themes = simplexml_load_file("themes.xml"); // We search the theme config
                $xml = new SimpleXMLElement($themes->asXML());

                $cpt = 0;
                foreach ($themes->theme as $theme) {
                    if ($theme->name == $_POST['edit_theme']) { // if found, we delete it
                        unset($xml->theme[$cpt]);
                    }
                    $cpt++;
                }

                $xml->saveXML('themes.xml');


                $themesTmp = simplexml_load_file("themes.xml");

                $xml = new SimpleXMLElement($themesTmp->asXML());

                $themeAdded = $xml->addChild("theme");

                $themeAdded->addChild('name', $_POST['edit_theme']);
                $themeAdded->addChild('backgroundcolor', $_POST['edit_backgroundcolor']);
                $themeAdded->addChild('loadercolor', $_POST['edit_loadercolor']);
                $themeAdded->addChild('inputcolor', $_POST['edit_inputcolor']);
                $themeAdded->addChild('inputtextcolor', $_POST['edit_inputtextcolor']);
                $themeAdded->addChild('titlecolor', $_POST['edit_titlecolor']);

                $xml->saveXML('themes.xml');

                header("Location: settings.php?edited=1");
                exit;
            }
            ?>


                        <h3>Administration</h3>

                        <ul class="nav nav-tabs" id="mainMenu">
                            <li><a href="admin.php">Messages</a></li>
                            <li><a href="users.php">Admins</a></li>
            <?php if ($config->productOption == "yes") { ?>
                                <li><a href="products.php">Products</a></li><?php } ?>
                            <li><a href="divisions.php">Divisions</a></li>
                            <li class="active"><a href="settings.php">Settings</a></li>
                        </ul>

                        <div class="tab-content">
                            <div style="margin-left:30px;">
                                <div class="row">
                                    <div class="span10 offset1">
                                        <fieldset>
                                            <legend>Edit a theme</legend>
                                            <form method="post" action="edit.php" style="margin:0;">
                                                <div class="row" style="margin-top:7px;">
                                                    <div class="span3 offset4">
                                                        <div id="main" style="text-align:center;">

                                                        </div>
                                                    </div>
                                                </div>

            <?php
            //$themes = simplexml_load_file("themes.xml"); // We search the theme config
            $xml = new SimpleXMLElement($themes->asXML());

            foreach ($themes->theme as $theme) {

                if ($theme->name == $_POST['edit_theme']) { // if found, we delete it
                    $value = $theme;
                }
            }

            print_r($value);
            ?>

                                                <div class="row" style="text-align:center; margin-top:7px;">
                                                    <div class="input-prepend" class="span3">
                                                        <button class="btn submit" type="button" style="width:150px;">Theme
                                                            Name
                                                        </button>
                                                        <input name="edit_theme_show" type="text" disabled="disabled"
                                                               style="background-color:white;"
                                                               value="<?php echo $_POST['edit_theme'] ?>">
                                                        <input name="edit_theme" type="hidden" style="background-color:white;"
                                                               value="<?php echo $_POST['edit_theme'] ?>">
                                                    </div>
                                                </div>
                                                <div class="row" style="text-align:center; margin-top:7px;">
                                                    <div class="input-prepend" class="span3">
                                                        <button class="btn submit" type="button" name="inputTextColor"
                                                                style="width:150px;">Input Text Color
                                                        </button>
                                                        <input class="color" id="color1" name="edit_inputtextcolor" type="text"
                                                               style="background-color:<?php echo $value->inputtextcolor ?>;"
                                                               value="<?php echo $value->inputtextcolor ?>">
                                                    </div>
                                                    <div class="input-prepend" class="span3">
                                                        <button class="btn submit" type="button" name="inputColor"
                                                                style="width:150px;">Input Color
                                                        </button>
                                                        <input class="color" id="color2" name="edit_inputcolor"
                                                               placeholder="Search" id="appendedInputButtons"
                                                               style="background-color:<?php echo $value->inputcolor ?>;"
                                                               type="text" value="<?php echo $value->inputcolor ?>">
                                                    </div>
                                                </div>
                                                <div class="row" style="text-align:center; margin-top:7px;">
                                                    <div class="input-prepend" class="span3">
                                                        <button class="btn submit" type="button" name="titleColor"
                                                                style="width:150px;">Title Color
                                                        </button>
                                                        <input name="edit_titlecolor" class="color" id="color3"
                                                               placeholder="Search" id="appendedInputButtons"
                                                               style="background-color:<?php echo $value->titlecolor ?>;"
                                                               type="text" value="<?php echo $value->titlecolor ?>">
                                                    </div>
                                                    <div class="input-prepend" class="span3">
                                                        <button class="btn submit" type="button" name="loaderColor"
                                                                style="width:150px;">Loader Color
                                                        </button>
                                                        <input name="edit_loadercolor" class="color" id="color4"
                                                               placeholder="Search" id="appendedInputButtons"
                                                               style="background-color:<?php echo $value->loadercolor ?>;"
                                                               type="text" value="<?php echo $value->loadercolor ?>">
                                                    </div>
                                                </div>
                                                <div class="row" style="text-align:center; margin-top:7px;">
                                                    <div class="input-prepend" class="span3">
                                                        <button class="btn submit" type="button" name="bgColor"
                                                                style="width:150px;">Background Color
                                                        </button>
                                                        <input name="edit_backgroundcolor" class="color" id="color5"
                                                               placeholder="Search" id="appendedInputButtons"
                                                               style="background-color:<?php echo $value->backgroundcolor ?>;"
                                                               type="text" value="<?php echo $value->backgroundcolor ?>">
                                                    </div>
                                                </div>
                                                <div style="text-align:center;margin-top: 20px;">
                                                    <button type="submit" class="btn btn-success">Edit</button>
                                                </div>
                                            </form>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                        </div>

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
            (function($) {

                $(".color").focus(function() {

                    //$('#colorpicker').remove();

                    var id = $(this).attr("id");

                    $("#main").slideUp().html("<div id='colorpicker'></div>").slideDown("slow");

                    $('#colorpicker').farbtastic("#" + id);


                }).focusout(function() {


                });

                $("button.submit").click(function() {
                    $("form").submit();

                });


            })(jQuery);
        </script>

    </body>
</html>
