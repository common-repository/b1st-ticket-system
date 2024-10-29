<?php
$this->require_once_includes('nocsrf');
$this->include_includes("functions");
$succesReg = 0;
$successDelete = 0;
$error = "";
$recno = 0;
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

 <?php  $this->SetMessage(9) ; ?> 


<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">

            <?php
            if (isset($_POST['product_name'])) {
                // Verify if the product already exists
                foreach ($config->products->product as $product) {
                    if ($product == $_POST['product_name']) {
                        $error = __('The product name already exists', 'ticketsys');
                    }
                }

                if ($error == "") {
                    $xml = new SimpleXMLElement($config->asXML());
                    $xml->products->addChild('product', $_POST['product_name']);
                    $xml->saveXML(self::$PATH_CONFIG);

                    header("Location: " . $this->renderAction('added=1', null, false));
                    exit;
                }
            }

            if (isset($_POST['edit_product'])) {

                $xml = new SimpleXMLElement($config->asXML());

                $id = 0;
                $cpt = 0;
                $exist = 0;

                // Verify if the name already exists
                foreach ($config->products->product as $product) {
                    if ($product == $_POST["edit_product"]) {
                        $exist++;
                    }
                }

                if ($exist > 0) {
                    $error = __('The product name already exists', 'ticketsys');
                } else {

                    // We find the good id
                    foreach ($config->products->product as $product) {
                        if ($product == $_POST["old_product"]) {
                            unset($xml->products->product[$cpt]);
                        }

                        $cpt++;
                    }

                    // We add the new one
                    $xml->products->addChild('product', $_POST['edit_product']);
                    $xml->saveXML(self::$PATH_CONFIG);

                    $sql = "UPDATE messages SET msg_product ='" . $_POST['edit_product'] . "' WHERE msg_product='" . $_POST["old_product"] . "'";
                    $wpdb->query($sql);

                    wp_redirect($this->renderAction('edited=1', null, false));
                    exit;
                }
            }

            if (isset($_GET['delete'])) {

                $rec_no = "SELECT Count(*) as count FROM messages WHERE msg_product = '" . $_GET['delete'] . "'";
                $recno = $wpdb->get_row($rec_no, ARRAY_N);
                $recno = $recno[0];

                if ($recno == 0) {


                    $xml = new SimpleXMLElement($config->asXML());

                    $id = 0;
                    $cpt = 0;

                    // We find the good id
                    foreach ($config->products->product as $product) {
                        if ($product == $_GET['delete']) {
                            unset($xml->products->product[$cpt]);
                        }

                        $cpt++;
                    }

                    $xml->saveXML(self::$PATH_CONFIG);

                    header("Location:" . $this->renderAction('deleted=1', '', false));
                    exit;
                }
            }
            ?>


            <h3><?php _e('Administration', 'ticketsys'); ?></h3>

            <ul class="nav nav-tabs">
                <?php createMenu(3, $_SESSION['admin']); ?>
            </ul>

            <?php
            $_POST['csrf_token'] = $_SESSION['csrf_token_all'];

            if (isset($_SESSION['username'], $_SESSION['csrf_csrf_token'])) {

                try {
                    // Run CSRF check, on POST data, in exception mode, for 60 minutes, in multiple mode.
                    NoCSRF::check('csrf_token', $_POST, true, 60 * 60, true);
                } catch (Exception $e) {
                    // CSRF attack detected
                    $CSRF_error = $e->getMessage();
                    echo "<div class='alert alert-error'>" . $CSRF_error . "</div>";
                    exit;
                }
            }
            ?>


            <div class="row-fluid">
                <div class="span8">

                    <?php
                    if (isset($_GET['edit'])) {
                        ?>
                        <h4 style="margin-top: 0;">Edit <?php echo htmlspecialchars($_GET['edit']) ?>'s title</h4>


                        <form class="form-inline" method="post" action="<?php $this->renderAction(); ?>">
                            <input type="hidden" name="old_product" value="<?php echo htmlspecialchars($_GET['edit']) ?>"/>
                            <input type="text" name="edit_product" value="<?php echo htmlspecialchars($_GET['edit']) ?>"
                                   class="span3"/>
                            <input type="<?php $this->SetType();?>" <?php $this->SetCability(); ?> value="<?php _e('Edit', 'ticketsys'); ?>" class="btn btn-success span1" style="height:32px; width:100px;"/>
                        </form>

                        <?php
                    }

                    if (isset($succesEdit) && $succesEdit == 1) {
                        echo '<div class="alert alert-success">' . __('The account has been updated.', 'ticketsys') . '</div>';
                    }
                    ?>

                    <table class="table table-stripped">
                        <tr>
                            <th style="text-align:center"><?php _e('Number', 'ticketsys'); ?></th>
                            <th style="text-align:center"><?php _e('Product', 'ticketsys'); ?></th>
                            <th style="text-align:center"><?php _e('Action', 'ticketsys'); ?></th>
                        </tr>

                        <?php
                        $cpt = 1;
                        foreach ($config->products->product as $product) {
                            ?>
                            <tr class="description">
                                <td style="text-align:center"><?php echo $cpt ?></td>
                                <td style="text-align:center;"><?php echo htmlspecialchars($product) ?></td>
                                <td style="text-align:center;"><a
                                        href="<?php $this->renderAction('edit=' . $product); ?>"
                                        <button class="btn btn-info"><?php _e('Edit', 'ticketsys'); ?></button>
                                    </a> <a href="<?php $this->renderAction('delete=' . $product); ?>"
                                            <button  <?php if($config->deleteconfirm=='yes'){?>onclick="return confirm('<?php echo __('Are you sure you want to delete this item?','ticketsys')?> ')" <?php }?> class="btn btn-danger"><?php _e('Delete', 'ticketsys'); ?></button>
                                    </a></td>
                            </tr>
                            <?php
                            $cpt++;
                        }
                        ?>

                    </table>
                </div>

                <div class="span3">

                    <?php
                    if (isset($_GET["added"]) && $_GET["added"] == 1) {
                        echo '<div class="alert alert-success">' . __('The product has been well added.', 'ticketsys') . '</div>';
                    } else {
                        if (isset($_GET["edited"]) && $_GET["edited"] == 1) {
                            echo '<div class="alert alert-success">' . __('The product has been well edited.', 'ticketsys') . '</div>';
                        } else {
                            if (isset($_GET["deleted"]) && $_GET["deleted"] == 1 && $recno == 0) {
                                echo '<div class="alert alert-success">' . __('The product has been well deleted.', 'ticketsys') . '</div>';
                            } else {
                                if ($recno > 0) {
                                    echo '<div class="alert alert-danger">' . sprintf(__('There are %d messages associated to this product. It can not be deleted', 'ticketsys'), $recno) . '.</div>';
                                } else {
                                    if ($error != "") {
                                        echo '<div class="alert alert-error">' . $error . '</div>';
                                    }
                                }
                            }
                        }
                    }
                    ?>

                    <h4 style="margin-top: 0;"><?php _e('Add a product', 'ticketsys'); ?></h4>

                    <form class="form-inline" method="post" action="">
                        <input type="text" name="product_name" class="span7" placeholder="<?php _e('Product name', 'ticketsys'); ?>" />
                        <input type="<?php $this->SetType(); ?>" <?php $this->SetCability(); ?> value="<?php _e('Confirm', 'ticketsys'); ?>" class="btn btn-success span5" style="height:32px;"/>
                    </form>

                </div>

            </div>
        </div>
    </div>
</div>
