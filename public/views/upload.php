<?php
$error = null;
$filename = null;
$status = 0;

if (isset($_FILES['uploadFile'])) {
    $files = $_FILES['uploadFile'];
    $ticketid = $_POST['ticketid'];
    $instance = $_POST['instance'];


    $upload_dir = wp_upload_dir();
    $savefilepath = $upload_dir['basedir'] . '/b1st/attachements/' . $ticketid;
    if (isset($_POST['answerid'])) {
        if (!file_exists($savefilepath)) {
            mkdir($savefilepath);
            chmod ($savefilepath,0755);
        }
        $answerid = $_POST['answerid'];
        $savefilepath .= '/' . $answerid;
    }

    mkdir($savefilepath);
    // chmod ($savefilepath,777);
    $tempArr = array();
    $count = 0;
    $max = count($files['name']);
    $limit = $config->maxuploads;
    while ($count < $max && $count < $limit) {
        $temp['name'] = $files['name'][$count];
        $temp['type'] = $files['type'][$count];
        $temp['tmp_name'] = $files['tmp_name'][$count];
        $temp['error'] = $files['error'][$count];
        $temp['size'] = $files['size'][$count];
        array_push($tempArr, $temp);
        $count++;
    }
    foreach ($tempArr as $key => $file) {
        $filename = $file['name'];
        $targetpath = $savefilepath . '/' . $file['name'];
        if (@move_uploaded_file($file['tmp_name'], $targetpath)) {
            $error = __('Attachment upload finished!', 'ticketsys');
            $status = 2;

            $scan_res = scanFile($config->metascan, $targetpath);
            if ($scan_res != 0 && $scan_res != 4 && $scan_res != 7) {
                unlink($targetpath);
            }
        } else {
            $error = __('Attachment upload failed!', 'ticketsys');
            $status = 3;
            break;
        }
    }
} else {
    $error = __('Input error!', 'ticketsys');
}
$error = "";
?>
<script type="text/javascript">
    <!--
    window.top.window.uploadEnd("<?php echo $error; ?>", "<?php echo $instance; ?>");
    error = "<?php echo $error; ?>";
    status = "<?php echo $status; ?>";
    //-->
</script>
