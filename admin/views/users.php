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

<?php  $this->SetMessage(2) ; ?>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">

            <?php
            if (isset($_POST['add'])) {
                $user = new WP_User($_POST['add']);
                $del_right = 0;
                $user->add_cap('ticketsys-manage');
                if (isset($_POST['new_delete_right']) && $_POST['new_delete_right'] == 1) {
                    $user->add_cap('ticketsys-delete');
                }

                $close_right = 0;
                if (isset($_POST['new_close_right']) && $_POST['new_close_right'] == 1) {
                    $user->add_cap('ticketsys-close');
                }
                if (isset($_POST['new_read_right']) && $_POST['new_read_right'] == 1) {
                    $user->add_cap('ticketsys-read');
                }
				
				
                add_user_meta( get_current_user_id( ), 'user_signature', $_POST['user_signature']);

                $succesReg = 1;
                header('Location: ' . $this->renderAction(null, null, false));
            }



            if (isset($_GET['delete'])) {
                $user = new WP_User($_GET['delete']);
                $user->remove_cap('ticketsys-manage');
                $user->remove_cap('ticketsys-close');
                $user->remove_cap('ticketsys-delete');
                $user->remove_cap('ticketsys-read');

               	delete_user_meta( get_current_user_id( ), 'user_signature');
        		header('Location: ' . $this->renderAction(null, null, false));
            }

            if (isset($_POST['edit_id'])) {
                $user = new WP_User($_POST['edit_id']);
                if (isset($_POST['edit_delete_right']) && $_POST['edit_delete_right'] == 1) {
                    $user->add_cap('ticketsys-delete');
                } else {
                    $user->remove_cap('ticketsys-delete');
                }

                if (isset($_POST['edit_close_right']) && $_POST['edit_close_right'] == 1) {
                    $user->add_cap('ticketsys-close');
                } else {
                    $user->remove_cap('ticketsys-close');
                }
                if (isset($_POST['edit_read_right']) && $_POST['edit_read_right'] == 1) {
                    $user->add_cap('ticketsys-read');
                } else {
                    $user->remove_cap('ticketsys-read');
                }

                update_user_meta( get_current_user_id( ), 'user_signature', $_POST['user_signature']);
				header('Location: ' . $this->renderAction(null, null, false));
            }
            ?>


            <h3><?php _e('Administration', 'ticketsys'); ?></h3>
            <?php 
			// $all_meta_for_user = get_user_meta( get_current_user_id( ) );
		    // print_r( $all_meta_for_user );
			//echo self::$INIT_TIME ;
					 	        		 			 			 
			 ?>
            <ul class="nav nav-tabs">
                <?php createMenu(2, $_SESSION['admin']); ?>
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
                <div class="span10">

                    <?php
                    if (isset($_GET['edit'])) {

                        $useredit = new WP_User($_GET['edit']);
                        ?>
                        <h4 style="margin-top: 0;"><?php printf(__('Edit %s > informations', 'ticketsys'), htmlspecialchars($useredit->display_name)); ?> >

                        </h4>
                        <div class="alert alert-info" id="email_message"> <?php _e('Only the admin can edit user\'s name, email and privileges. Note that admin is not the user with name "admin", rather it is your first user ever created in your ticket system.', 'ticketsys'); ?>
                        </div>
                        <form class="form-inline" method="post" action="<?php $this->renderAction(); ?>">
                            <input type="hidden" name="edit_id" value="<?php echo htmlspecialchars($useredit->ID) ?>"/>
                            <input type="text"
                                   value="<?php echo htmlspecialchars($useredit->display_name) ?>"
                                   class="span2" <?php
                                   if (true) {
                                       echo " disabled";
                                   }
                                   ?> />

                            <br>

                            <label class="checkbox">
                                <input type="checkbox" name="edit_delete_right"
                                       value="1" <?php
                                       if ($useredit->has_cap('ticketsys-delete')) {
                                           echo "checked ";
                                       }
                                       ?> <?php
                                       if ($The_Admin == false) {
                                           echo " disabled";
                                       }
                                       ?>>
                                Delete right
                            </label>
                            <label class="checkbox">
                                <input type="checkbox" name="edit_close_right"
                                       value="1" <?php
                                       if ($useredit->has_cap('ticketsys-close')) {
                                           echo "checked";
                                       }
                                       ?><?php
                                       if ($The_Admin == false) {
                                           echo " disabled";
                                       }
                                       ?>>
                                Close right
                            </label>
                           <label class="checkbox">
                                <input type="checkbox" name="edit_read_right"
                                       value="1" <?php
                                       if ($useredit->has_cap('ticketsys-read')) {
                                           echo "checked";
                                       }
                                       ?><?php
                                       if ($The_Admin == false) {
                                           echo " disabled";
                                       }
                                       ?>>
                               Read all right
                            </label>
							
							<label class="">
							     <textarea name="user_signature" placeholder="<?php _e('Admin\'s signature', 'ticketsys'); ?>"><?php echo get_user_meta( get_current_user_id( ) , 'user_signature', true) ; ?></textarea>
                            </label>

                            <input type="<?php $this->SetType(); ?>" <?php $this->SetCability(); ?> value="<?php _e('Edit', 'ticketsys'); ?>" class="btn btn-success span1" style="height:32px;"/>
                        </form>

                        <?php
                    }

                    if (isset($succesEdit) && $succesEdit == 1) {
                        echo '<div class="alert alert-success">' . __('The account has been updated.', 'ticketsys') . '</div>';
                    }
                    ?>

                    <table class="table table-stripped">
                        <tr>
                            <th style="text-align:center"><?php _e('Name', 'ticketsys'); ?></th>          
                            <th style="text-align:center"><?php _e('Username', 'ticketsys'); ?></th>
                            <th style="text-align:center"><?php _e('Email', 'ticketsys'); ?></th>
                            <?php if ($close_right == 1) { ?>
                                <th style="text-align:center"><?php _e('Delete right', 'ticketsys'); ?></th><?php } ?>
                            <?php if ($delete_right == 1) { ?>
                                <th style="text-align:center"><?php _e('Close right', 'ticketsys'); ?></th><?php } ?> 
                            <?php if ($read_right == 1) { ?>
                                <th style="text-align:center"><?php _e('Read all right', 'ticketsys'); ?></th><?php } ?>
                            <th <?php if ($delete_right == 1) { ?>colspan="2"<?php } ?>
                                                                  style="text-align:center; "><?php _e('Action', 'ticketsys'); ?>
                            </th>

                        </tr>


                        <?php foreach ($users_can_access as $user) {
                            ?>

                            <tr class="description">
                                <td style="text-align:center"><?php echo htmlspecialchars($user['user']->display_name) ?></td>
                                <td style="text-align:center"><?php echo htmlspecialchars($user['user']->user_login) ?></td>
                                <td style="text-align:center"><?php echo htmlspecialchars($user['user']->user_email) ?></td>

                                <?php if ($delete_right == 1) { ?>
                                    <td style="text-align:center"><?php
                                        if ($user['user']->has_cap('ticketsys-delete')) {
                                            _e('Yes', 'ticketsys');
                                        } else {
                                            _e('No', 'ticketsys');
                                        }
                                        ?></td><?php } ?>

                                <?php if ($delete_right == 1) { ?>
                                    <td style="text-align:center"><?php
                                        if ($user['user']->has_cap('ticketsys-close')) {
                                            _e('Yes', 'ticketsys');
                                        } else {
                                            _e('No', 'ticketsys');
                                        }
                                        ?></td><?php } ?>


                                <?php if ($read_right == 1) { ?>
                                    <td style="text-align:center"><?php
                                        if ($user['user']->has_cap('ticketsys-read')) {
                                            _e('Yes', 'ticketsys');
                                        } else {
                                            _e('No', 'ticketsys');
                                        }
                                        ?></td><?php } ?>

                                <?php
                                    if ($user['edit']) { ?>
                                    <td style="text-align:center; width: 60px;padding-left: 0px;"><a
                                            href="<?php $this->renderAction('edit=' . $user['user']->ID) ?>">
                                            <button <?php $this->SetCability(); ?> class="btn btn-info"><?php _e('Edit', 'ticketsys'); ?></button>
                                        </a></td>
                                    <?php } ?>
                                <?php
                                    if ($user['delete']) { ?>
                                    <td style="text-align:center; width: 60px;padding-left: 0px;"><a
                                            href="<?php $this->renderAction('delete=' . $user['user']->ID) ?>">
                                            <button <?php if($config->deleteconfirm=='yes'){?>onclick="return confirm('<?php echo __('Are you sure you want to delete this item?','ticketsys')?> ')" <?php }?>  <?php $this->SetCability(); ?> class="btn btn-danger" ><?php _e('Delete', 'ticketsys'); ?></button>
                                        </a></td>
                                <?php } ?>

                            </tr>
                            <tr>
                                <td colspan="8">
                                    <table class="row-fluid innertable-rating" style="border: none;">
                                        <tr>
                                            <td><a class="button collapse" <?php $this->SetCability(); ?> href="javascript:void(0);" style="height: auto;"><?php _e('Show rating statistics', 'ticketsys'); ?></a></td>
                                        </tr>
                                        <tr class="row-fluid collapse-div" style="display: none;">
                                            <td>
                                                <span class="btn" style="display: block; background: #FFF; padding: 5px; text-align: left;">
                                                    <?php
                                                    $anss = $wpdb->get_col("SELECT id FROM b1st_ts_answers WHERE a_account = " . $user['user']->ID);
                                                    if ($anss) {
                                                        $anss = implode(',', $anss);
                                                        $rating = $wpdb->get_row("SELECT SUM(stars) AS 'stars', COUNT(votes) as 'count' FROM b1st_ts_msg_rating WHERE aid IN ($anss)");

                                                        $img = "";
                                                        $avg = $rating->stars / $rating->count;
                                                        $avg = floor($avg * 2) / 2;
                                                        switch ($avg) {
                                                            case 0 : $img = "0-0.gif";
                                                                break;
                                                            case 0.5 : $img = "0-5.gif";
                                                                break;
                                                            case 1 : $img = "1-0.gif";
                                                                break;
                                                            case 1.5 : $img = "1-5.gif";
                                                                break;
                                                            case 2 : $img = "2-0.gif";
                                                                break;
                                                            case 2.5 : $img = "2-5.gif";
                                                                break;
                                                            case 3 : $img = "3-0.gif";
                                                                break;
                                                            case 3.5 : $img = "3-5.gif";
                                                                break;
                                                            case 4 : $img = "4-0.gif";
                                                                break;
                                                            case 4.5 : $img = "4-5.gif";
                                                                break;
                                                            case 5 : $img = "5-0.gif";
                                                                break;
                                                        }
                                                        for ($i = 0; $i < 5; $i++) {
                                                            $rate_count = $wpdb->get_col("SELECT COUNT(votes) as 'count' FROM b1st_ts_msg_rating WHERE aid IN ($anss) AND stars = " . ($i + 1));
                                                            echo $rate_count[0];
                                                            for ($j = 0; $j <= $i; $j++) {
                                                                echo '&nbsp;<img src="' . plugins_url('../assets/images/rating/star.png', __FILE__) . '" />';
                                                            }
                                                            echo '<br/>';
                                                        }
                                                        echo __('Overall Rate', 'ticketsys') . ": $avg <img src='" . plugins_url('../assets/images/rating/' . $img, __FILE__) . "' />";
                                                    } else {
                                                        _e('No record', 'ticketsys');
                                                    }
                                                    ?>
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        <?php } ?>


                    </table>
                </div>


                <div class="span2">

                    <?php
                    if (isset($successReg) && $succesReg == 1) {
                        echo '<div class="alert alert-success">' . sprintf(__('%s can now log in.', 'ticketsys'), $_POST['new_username']) . ' </div>';
                    }
                    ?>


                    <?php if ($The_Admin == true) {
                        ?>
                        <h4 style="margin-top: 0;"><?php _e('Add a user', 'ticketsys'); ?></h4>

                        <form method="post" action="<?php $this->renderAction(); ?>">
                            <div class="row-fluid">
                                <div class="span12">
                                    <?php
                                    if (strlen($users_unauth_arr) > 0)
                                        wp_dropdown_users(array('include' => $users_unauth_arr, 'name' => 'add', 'class' => 'span10'));
                                    else {
                                        ?>
                                        <select class="span12" disabled="disabled"></select>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="row-fluid">
                                <div class="span12">
                                    <label class="checkbox">
                                        <input type="checkbox" name="new_delete_right"> <?php _e('Delete right', 'ticketsys'); ?>
                                    </label>
                                    <label class="checkbox">
                                        <input type="checkbox" name="new_close_right"> <?php _e('Close right', 'ticketsys'); ?>
                                    </label>
                                    <label class="checkbox">
                                        <input type="checkbox" name="new_read_right"> <?php _e('Read all right', 'ticketsys'); ?>
                                    </label>
                                    <label class="">
                                          <textarea name="user_signature" placeholder="<?php _e('Admin\'s signature', 'ticketsys'); ?>"><?php echo get_user_meta( get_current_user_id( ) , 'user_signature', true) ; ?></textarea>
                                    </label>
                                    <input type="<?php $this->SetType()?>"  <?php $this->SetCability() ?> value="<?php _e('Confirm', 'ticketsys'); ?>" class="btn btn-success " style="height:30px; width:100px;"/>
                                </div>
                            </div>

                        </form>
                    <?php }
                    ?>

                </div>

            </div>

        </div>
    </div>
</div>