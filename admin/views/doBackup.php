<?php
$this->require_once_includes("functions");

// select db
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD,DB_NAME);

// create database
$dbScript = createDbScript();

//get all of the tables

$tables = "{$dbprefix}messages,{$dbprefix}answers,{$dbprefix}faqs,{$dbprefix}msg_rating,{$dbprefix}accounts";
$tables = explode(',', $tables);

//cycle through
$time_start = microtime(true);
foreach ($tables as $table) {
    $query = mysqli_query($link,'SELECT * FROM ' . $table);
    $num_fields = mysqli_num_fields($query);

    for ($i = 0; $i < $num_fields; $i++) {
        while ($row = mysqli_fetch_row($query)) {
            $dbScript .= 'INSERT INTO ' . $table . ' VALUES(';
            for ($j = 0; $j < $num_fields; $j++) {
                $row[$j] = addslashes($row[$j]);
                $row[$j] =str_replace("\n","\\n",$row[$j]);
                if (isset($row[$j])) {
                    $dbScript .= '"' . $row[$j] . '"';
                } else {
                    $dbScript .= '""';
                }
                if ($j < ($num_fields - 1)) {
                    $dbScript .= ',';
                }
            }
            $dbScript .= ");\n";
        }
    }
    $dbScript .= "\n\n\n";
}


$date = date('Y-m-d H-i-s', time());

//save file
$filename = 'db-backup-' . $date . '.sql';
$wp_filesystem->put_contents(self::$PATH_BACKUP  . $filename ,$dbScript, 0755);
$time_end = microtime(true);
$execution_time = round(($time_end - $time_start), 2);

echo "A backup file with name $filename of size " . round(
        (filesize(self::$PATH_BACKUP . $filename) / (1024 * 1024)),
        2
    ) . " MB is created in $execution_time seconds. Now, we are refreshing page to update backup list at your page.";


?>


