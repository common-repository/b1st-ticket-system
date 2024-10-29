<?php
$this->require_once_includes('nocsrf');
$this->include_includes('functions');
$target = $this->renderAction(null, 'ticketsys-dobackup-settings', false);
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

 <?php  $this->SetMessage(7) ; ?> 

<script language="javascript">

    var request = createRequestObject();
    var dataReturn = '';
    var ajaxTimeout = '';
    var enterChecker = false;

    function createRequestObject() {
        var ro;
        var browser = navigator.appName;
        if (browser == 'Microsoft Internet Explorer') {
            ro = new ActiveXObject('Microsoft.XMLHTTP');
        }
        else {
            ro = new XMLHttpRequest();
        }
        return ro;
    }

    function makeRequest(url, fun) {
        enterChecker = false;
        request.open('get', url);
        request.onreadystatechange = function() {
            handleResponse(fun);
        }

        try {
            request.send(null);
        }
        catch (err) {
            alert('Error occured: ' + err);
            showHidePreloader(false);
        }
    }

    function makePostRequest(url, params, fun) {
        request.open('POST', url, true);

        //Send the proper header information along with the request
        request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        request.setRequestHeader('Content-length', params.length);
        request.setRequestHeader('Connection', 'close');

        request.onreadystatechange = function() {//Call a function when the state changes.
            if (request.readyState == 4 && request.status == 200) {
                dataReturn = request.responseText;
                if (fun != '')
                    ajaxTimeout = setTimeout(fun + '()', 200);
            }
        }

        try {
            request.send(params);
        }
        catch (err) {
            alert('Error occured: ' + err);
            showHidePreloader(false);
        }
    }

    function handleResponse(fun) {
        if (request.readyState < 4) {
            ajaxTimeout = setTimeout('handleResponse(\'' + fun + '\')', 10);
        }
        else if (request.readyState == 4 && !enterChecker) {
            enterChecker = true;
            var response = request.responseText;
            dataReturn = response;

            if (fun != '')
                ajaxTimeout = setTimeout(fun + '()', 200);
        }
    }

    function stopAjax() {
        clearTimeout('ajaxTimeout');
        ajaxTimeout = '';
    }


    function showContent() {
        showHidePreloader(false);
        document.getElementById('resultDiv').innerHTML = dataReturn;
        document.getElementById('resultDiv').style.display = 'block';
        location.reload()
    }

    function showHidePreloader(show) {
        if (show)
            document.getElementById('preloader').style.display = 'block';
        else
            document.getElementById('preloader').style.display = 'none';
    }
</script>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">


            <?php
            if (isset($_GET['delete'])) {
                unlink(self::$PATH_BACKUP . $_GET['delete']);
                $succesDelete = 1;
            }
            if (isset($_GET['add'])) {
                //Backup_Database () ;
                //$succesAdd = 1 ;
            }
            if (isset($_GET['restore'])) {

                $backupFile = self::$PATH_CONFIG . $_GET['restore'];
                $this->include_includes('restore_db');
                $this->restore_db();
            }
            ?>


            <h3><?php _e('Administration', 'ticketsys'); ?></h3>
            <ul class="nav nav-tabs">
                <?php createMenu(7, $_SESSION['admin']); ?>
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



            <br>
            <?php
            if (isset($succesDelete) && $succesDelete == 1) {
                echo '<div class="alert alert-success">' . __('Your database has been deleted', 'ticketsys') . '</div>';
            }
            ?>
            <td style="text-align:left; width: 30%;"><input type="button" <?php $this->SetCability(); ?> class="btn btn-info"
                                                              onclick="showHidePreloader(true);
                                                                      makeRequest('<?php echo $target; ?>', 'showContent')"
                                                              value="<?php _e('Create new backup', 'ticketsys'); ?>"/></td>

            <div class="alert alert-info" id="email_message"> <?php _e('Only the admin can delete or restore database, still
                others can create database backup', 'ticketsys'); ?>.
            </div>

            <table class="table table-stripped" style="width: 65%;">
                <?php
                $path = self::$PATH_BACKUP;
// sort files in descending  order using second parameter
                $files = scandir($path, 1);
                ?>
                <tr class="description">
                    <td style="text-align:left; width: 50%;"><?php _e('file name', 'ticketsys'); ?></td>
                    <td style="text-align:left; width: 20%;"><?php _e('size', 'ticketsys'); ?></td>
                    <td colspan="2" style="text-align:center; width: 30%;"><?php _e('actions', 'ticketsys'); ?></td>
                </tr>

                <?php
                foreach ($files as $file) {
                    if ($file != '.' && $file != '..') { {
                            ?>

                            <tr class="description">
                                <td style="text-align:left; width: 50%;"><?php echo htmlspecialchars($file) ?>
                                <td style="text-align:left; width: 20%;"><?php
                                    echo round(
                                            (filesize(self::$PATH_BACKUP . htmlspecialchars($file)) / (1024 * 1024)), 2
                                    ) . ' MB'
                                    ?>
                                <td style="text-align:left; width: 15%;"><a <?php if($config->deleteconfirm=='yes'){?>onclick="return confirm('<?php echo __('Are you sure you want to delete this item?','ticketsys')?> ')" <?php }?>
                                        href="<?php echo $this->renderAction(); ?>&delete=<?php echo htmlspecialchars($file) ?>">
                                        <input type="button" <?php $this->SetCability(); ?>
                                            class="btn btn-danger" value =" <?php _e('Delete', 'ticketsys'); ?>" <?php
                                       if ($_SESSION['admin'] == false) {
                                                                                    echo " disabled ";
                                                                                }
                                                                                ?>
                                       />
                                    </td>

                                <td style="text-align:left; width: 15%;"><input type="button" <?php $this->SetCability(); ?>
                                                                                class="btn btn-success" <?php
                                                                                if ($_SESSION['admin'] == false) {
                                                                                    echo " disabled";
                                                                                }
                                                                                ?>
                                                                                onclick="showHidePreloader(true);
                                                                                                    makeRequest('<?php $this->renderAction('restore=' . htmlspecialchars($file), 'ticketsys-restoredb-settings') ?>', 'showContent')" value="<?php _e('Restore', 'ticketsys'); ?>"/>
                                </td>

                            </tr>
                            <?php
                        }
                    }
                }
                ?>


                <div class="row-fluid">
                    <div class="span12">
                        <br />

                        <div id="preloader" style="display: none;"><img style="display:none;" src="<?php echo self::$PATH_IMAGES; ?>ajax-loade.gif"/></div>
                        <div class="alert alert-success" id="resultDiv" style="display: none;"></div>

                    </div>
                </div>
        </div>
    </div>
</div>
