<?php

		
// select db
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

//get all of the tables

$tables = "{$dbprefix}messages,{$dbprefix}answers";
$tables = explode(',', $tables);


$delete_messages = "delete from {$dbprefix}messages;";
$delete_answers = "delete from {$dbprefix}answers;";

mysqli_query($link, $delete_messages);

mysqli_query($link, $delete_answers);

$fileName = self::$PATH_BACKUP . $_GET['restore'];
$fileContent = $wp_filesystem->get_contents($fileName);
$rows = explode(";\n", $fileContent);

$i = -6; // commands of dropping and creating tables

$time_start = microtime(true);
$allQueries = '';
foreach ($rows as $row) {
    if (strlen($row) > 1) {
        $i++;
        mysqli_query($link, $row);
    }
}


$time_end = microtime(true);
$execution_time = round(($time_end - $time_start), 2);

if ($i == 0) {
    _e('No record exist.', 'ticketsys');
} else {
    if ($i == 1) {
        _e('Only one record exist and was successfully restored.', 'ticketsys');
    } else {
        if ($i > 1) {
            printf(__('%1$s rows were successfully restored in %2$s seconds.', 'ticketsys'), $i, $execution_time);
        }
    }
}