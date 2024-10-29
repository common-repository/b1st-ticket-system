<?php
$this->require_once_includes('nocsrf');
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

 <?php  $this->SetMessage(5) ; ?> 
 
 <div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">

            <h3><?php _e('Administration', 'ticketsys'); ?></h3>

            <ul class="nav nav-tabs">
                <?php createMenu(9, $_SESSION['admin']); ?>
            </ul>
            <div class="row-fluid">
                <div class="span10">
                    <?php
                    if (!isset($_GET['action']) || (isset($_GET['action']) && $_GET['action'] == 'delete')) {
                        if (isset($_GET['action']) && $_GET['action'] == 'delete') {
                            $wpdb->delete("{$dbprefix}faqs", array("id" => $_GET['fid']));
                        }
                        ?>
                        <a href="?page=ticketsys-faq-settings&action=add" class="btn btn-success" style="margin-bottom: 5px;"><?php _e('Add FAQ', 'ticketsys'); ?></a>
                        <table class="table table-stripped">
                            <tr>
                                <th style="text-align:center"><?php _e('ID', 'ticketsys'); ?></th>
                                <th style="text-align:center"><?php _e('Faq Message', 'ticketsys'); ?></th>
                                <th style="text-align:center"><?php _e('Product', 'ticketsys'); ?></th>
                                <th style="text-align:center"><?php _e('Date Time', 'ticketsys'); ?></th>
                                <th style="text-align:center"><?php _e('Action', 'ticketsys'); ?></th>
                            </tr>
                            <tr>
                                <?php
                                global $wpdb;
                                foreach ($wpdb->get_results("SELECT * FROM {$dbprefix}faqs ORDER BY product") as $faq) {
                                    ?>
                                <tr>
                                    <td style="text-align:center"><a href="?page=ticketsys-faq-settings&action=detail&fid=<?php echo $faq->id; ?>"><?php echo $faq->id; ?></a></td>
                                    <td style="text-align:center"><a href="?page=ticketsys-faq-settings&action=detail&fid=<?php echo $faq->id; ?>"><?php echo $faq->message; ?></a></td>
                                    <td style="text-align:center"><?php echo $faq->product; ?></td>
                                    <td style="text-align:center"><?php echo $faq->date_time; ?></td>
                                    <td style="text-align:center"><a href="?page=ticketsys-faq-settings&action=detail&fid=<?php echo $faq->id; ?>" class="btn btn-info"><?php _e('Edit', 'ticketsys'); ?></a> <a <?php if($config->deleteconfirm=='yes'){?>onclick="return confirm('<?php echo __('Are you sure you want to delete this item?','ticketsys')?> ')" <?php }?> href="?page=ticketsys-faq-settings&action=delete&fid=<?php echo $faq->id; ?>" class="btn btn-danger"><?php _e('Delete', 'ticketsys'); ?></a></td>
                                </tr>
                            <?php }
                            ?>
                            </tr>
                        </table>
                        <?php
                    } else {
                        if (isset($_POST['replied']) && isset($_POST['reply'])) {

                            $wpdb->update("{$dbprefix}faqs", array("message" => $_POST['message'], "reply" => $_POST['reply'], "rating" => 0, "product" => $_POST['product']), array("id" => $_POST['id']));
                            echo '<div id="message" class="alert alert-success">' . __('Updated', 'ticketsys') . '</div>';
                        } else if (isset($_POST['add']) && isset($_POST['reply']) && isset($_POST['message'])) {
                            $wpdb->insert("{$dbprefix}faqs", array("message" => $_POST['message'], "reply" => $_POST['reply'], "rating" => 0, "product" => $_POST['product']));
                            header("Location: ?page=ticketsys-faq-settings");
                            echo '<div id="message" class="alert alert-success">' . __('Published', 'ticketsys') . '</div>';
                        }
                        if (isset($_GET['fid'])) {
                            $faq = $wpdb->get_row("SELECT * FROM {$dbprefix}faqs WHERE id = {$_GET['fid']}");
                            $faqid = $faq->id;
                            $faqmsg = $faq->message;
                            $faqprod = $faq->product;
                            $repmsg = $faq->reply;
                        } else
                            $faq = $faqid = $repmsg = $faqmsg = $faqprod = "";
                        ?>

                        <h4><?php _e('ID', 'ticketsys'); ?>: <?php echo $faqid; ?></h4>
                        <form method="POST">
                            <h4><?php _e('Message', 'ticketsys'); ?>:</h4>
                            <textarea name="message" required><?php echo $faqmsg; ?></textarea>
                            <h4><?php _e('Reply', 'ticketsys'); ?>:</h4>
                            <textarea name="reply" required><?php echo $repmsg; ?></textarea>
                            <h4><?php _e('Product', 'ticketsys'); ?>:</h4>
                            <select name="product">
                                <?php foreach ($config->products->product as $product) : ?>
                                    <option value="<?php echo $product; ?>"<?php echo ($product == $faqprod) ? " selected" : ""; ?>><?php echo $product; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <br class="clear" /><br/>
                            <?php if ($faqid): ?>
                                <input type="hidden" value="<?php echo $faqid; ?>" name="id" />
                                <input type="<?php $this->SetType(); ?>" <?php $this->SetCability(); ?> class="btn btn-success span2" name="replied" value="<?php _e('Save changes', 'ticketsys'); ?>" />
                            <?php else: ?>
                                <input type="<?php $this->SetType(); ?>" <?php $this->SetCability(); ?> class="btn btn-success span2"  name="add" value="<?php _e('Add', 'ticketsys'); ?>" />
                            <?php endif; ?>
                        </form>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>