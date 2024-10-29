<?php
$this->require_once_includes('nocsrf');
$this->include_includes("functions");
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

 <?php  $this->SetMessage(8) ; ?> 

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">

            <?php
            if (isset($_SESSION['username'])) {
                ?>


                <h3><?php _e('Administration', 'ticketsys'); ?></h3>

                <ul class="nav nav-tabs">
                    <?php createMenu(8, $_SESSION['admin']); ?>
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
                    <div class="span12">

                    </div>
                </div>

                <div class="row-fluid">
                    <div class="span12">

                        <?php
                        $total_msg = 'SELECT COUNT(*) AS total FROM ' . $dbprefix . 'messages';
                        $data_total = $wpdb->get_results($total_msg, ARRAY_A);
                        $data_total = $data_total[0];
                        $totalMsg = $data_total['total'];

                        // open messages
                        $total_open = 'SELECT COUNT(*) AS total FROM ' . $dbprefix . 'messages WHERE msg_status = "open"';
                        $data_total = $wpdb->get_results($total_open, ARRAY_A);
                        $data_total = $data_total[0];
                        $totalOpen = $data_total['total'];

                        $total_urgent_open = 'SELECT COUNT(*) AS total FROM ' . $dbprefix . 'messages WHERE msg_priority = 4 AND msg_status = "open"'
                        ;
                        $data_total = $wpdb->get_results($total_urgent_open, ARRAY_A);
                        $data_total = $data_total[0];
                        $totalUrgentOpen = $data_total['total'];

                        $total_high_open = 'SELECT COUNT(*) AS total FROM ' . $dbprefix . 'messages WHERE msg_priority = 3 AND msg_status = "open"'
                        ;
                        $data_total = $wpdb->get_results($total_high_open, ARRAY_A);
                        $data_total = $data_total[0];
                        $totalHighOpen = $data_total['total'];

                        $total_medium_open = 'SELECT COUNT(*) AS total FROM ' . $dbprefix . 'messages WHERE msg_priority = 2 AND msg_status = "open"'
                        ;
                        $data_total = $wpdb->get_results($total_medium_open, ARRAY_A);
                        $data_total = $data_total[0];
                        $totalMediumOpen = $data_total['total'];

                        $total_low_open = 'SELECT COUNT(*) AS total FROM ' . $dbprefix . 'messages WHERE msg_priority = 1 AND msg_status = "open"';
                        $data_total = $wpdb->get_results($total_low_open, ARRAY_A);
                        $data_total = $data_total[0];
                        $totalLowOpen = $data_total['total'];


                        // pending
                        $total_pending = 'SELECT COUNT(*) AS total FROM ' . $dbprefix . 'messages WHERE msg_status = "pending"';
                        $data_total = $wpdb->get_results($total_pending, ARRAY_A);
                        $data_total = $data_total[0];
                        $totalPending = $data_total['total'];

                        $total_urgent_pending = 'SELECT COUNT(*) AS total FROM ' . $dbprefix . 'messages WHERE msg_priority = 4 AND msg_status = "pending"'
                        ;
                        $data_total = $wpdb->get_results($total_urgent_pending, ARRAY_A);
                        $data_total = $data_total[0];
                        $totalUrgentPending = $data_total['total'];

                        $total_high_pending = 'SELECT COUNT(*) AS total FROM ' . $dbprefix . 'messages WHERE msg_priority = 3 AND msg_status = "pending"';
                        $data_total = $wpdb->get_results($total_high_pending, ARRAY_A);
                        $data_total = $data_total[0];
                        $totalHighPending = $data_total['total'];

                        $total_medium_pending = 'SELECT COUNT(*) AS total FROM ' . $dbprefix . 'messages WHERE msg_priority = 2 AND msg_status = "pending"'
                        ;
                        $data_total = $wpdb->get_results($total_medium_pending, ARRAY_A);
                        $data_total = $data_total[0];
                        $totalMediumPending = $data_total['total'];

                        $total_low_pending = 'SELECT COUNT(*) AS total FROM ' . $dbprefix . 'messages WHERE msg_priority = 1 AND msg_status = "pending"'
                        ;
                        $data_total = $wpdb->get_results($total_low_pending, ARRAY_A);
                        $data_total = $data_total[0];
                        $totalLowPending = $data_total['total'];

                        // close messages
                        $total_close = 'SELECT COUNT(*) AS total FROM ' . $dbprefix . 'messages WHERE msg_status = "close"';
                        $data_total = $wpdb->get_results($total_close, ARRAY_A);
                        $data_total = $data_total[0];
                        $totalClose = $data_total['total'];

                        $total_urgent_close = 'SELECT COUNT(*) AS total FROM ' . $dbprefix . 'messages WHERE msg_priority = 4 AND msg_status = "close"'
                        ;
                        $data_total = $wpdb->get_results($total_urgent_close, ARRAY_A);
                        $data_total = $data_total[0];
                        $totalUrgentClose = $data_total['total'];

                        $total_high_close = 'SELECT COUNT(*) AS total FROM ' . $dbprefix . 'messages WHERE msg_priority = 3 AND msg_status = "close"'
                        ;
                        $data_total = $wpdb->get_results($total_high_close, ARRAY_A);
                        $data_total = $data_total[0];
                        $totalHighClose = $data_total['total'];

                        $total_medium_close = 'SELECT COUNT(*) AS total FROM ' . $dbprefix . 'messages WHERE msg_priority = 2 AND msg_status = "close"'
                        ;
                        $data_total = $wpdb->get_results($total_medium_close, ARRAY_A);
                        $data_total = $data_total[0];
                        $totalMediumClose = $data_total['total'];

                        $total_low_close = 'SELECT COUNT(*) AS total FROM ' . $dbprefix . 'messages WHERE msg_priority = 1 AND msg_status = "close"'
                        ;
                        $data_total = $wpdb->get_results($total_low_close, ARRAY_A);
                        $data_total = $data_total[0];
                        $totalLowClose = $data_total['total'];
                        ?>




                        <ul class="nav nav-pills">
                            <li class="active span10"><a href="#"><?php _e('Total Messages', 'ticketsys'); ?> <span
                                        class="badge"><?php echo $totalMsg ?></span></a></li>
                        </ul>

                        <br>

                        <ul class="nav nav-pills">

                            <li class=" active span3"><a href="#"><?php _e('Open Messages', 'ticketsys'); ?> <span
                                        class="badge"><?php echo $totalOpen ?></span></a></li>
                            <li><a href="#"><?php _e('Urgent Messages', 'ticketsys'); ?> <span
                                        class="badge"><?php echo $totalUrgentOpen ?></span></a></li>
                            <li><a href="#"><?php _e('High Priority', 'ticketsys'); ?> <span class="badge"><?php echo $totalHighOpen ?></span></a>
                            </li>
                            <li><a href="#"><?php _e('Medium Priority', 'ticketsys'); ?> <span
                                        class="badge"><?php echo $totalMediumOpen ?></span></a></li>
                            <li><a href="#"><?php _e('Low Priority', 'ticketsys'); ?> <span class="badge"><?php echo $totalLowOpen ?></span></a></li>

                        </ul>

                        <br>

                        <ul class="nav nav-pills">

                            <li class="active span3"><a href="#"><?php _e('Pending Messages', 'ticketsys'); ?> <span
                                        class="badge"><?php echo $totalPending ?></span></a></li>
                            <li><a href="#"><?php _e('Urgent Messages', 'ticketsys'); ?> <span class="badge"><?php echo $totalUrgentPending ?></span></a>
                            </li>
                            <li><a href="#"><?php _e('High Priority', 'ticketsys'); ?> <span class="badge"><?php echo $totalHighPending ?></span></a>
                            </li>
                            <li><a href="#"><?php _e('Medium Priority', 'ticketsys'); ?> <span class="badge"><?php echo $totalMediumPending ?></span></a>
                            </li>
                            <li><a href="#"><?php _e('Low Priority', 'ticketsys'); ?> <span class="badge"><?php echo $totalLowPending ?></span></a>
                            </li>

                        </ul>


                        <br>

                        <ul class="nav nav-pills">

                            <li class="active span3"><a href="#"><?php _e('Close Messages', 'ticketsys'); ?> <span
                                        class="badge"><?php echo $totalClose ?></span></a></li>
                            <li><a href="#"><?php _e('Urgent Messages', 'ticketsys'); ?> <span
                                        class="badge"><?php echo $totalUrgentClose ?></span></a></li>
                            <li><a href="#"><?php _e('High Priority', 'ticketsys'); ?> <span class="badge"><?php echo $totalHighClose ?></span></a>
                            </li>
                            <li><a href="#"><?php _e('Medium Priority', 'ticketsys'); ?> <span
                                        class="badge"><?php echo $totalMediumClose ?></span></a></li>
                            <li><a href="#"><?php _e('Low Priority', 'ticketsys'); ?> <span class="badge"><?php echo $totalLowClose ?></span></a>
                            </li>

                        </ul>


                    </div>


                </div>

                <?php
            } else {
                header("location:admin.php");
            }
            ?>
        </div>
    </div>
</div>

<?php wp_enqueue_script('jquery'); ?>
<script type="text/javascript">
</script>

</body>
</html>
