<?php

require_once 'akismet.class.php';
// Load array with comment data.

function checkAkismit($author, $email, $message)
{

    $config = simplexml_load_file(plugin_dir_path( __FILE__ ) .'../../config/config.xml');
    $baseurl = "http://" . $_SERVER['HTTP_HOST'];

    $comment = array(
        'author' => $author,
        'email' => $email,
        'website' => '',
        'body' => $message,
        'permalink' => $baseurl,
        'user_ip' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT']
    );

// Instantiate an instance of the class.
    $akismet = new Akismet($baseurl, $config->akismit, $comment);

// Test for errors.
    if ($akismet->errorsExist()) { // Returns true if any errors exist.
        if ($akismet->isError('AKISMET_INVALID_KEY')) {
            return 1; // "AKISMET_INVALID_KEY" ;
            // Do something.
        } elseif ($akismet->isError('AKISMET_RESPONSE_FAILED')) {
            return 2; // echo "AKISMET_RESPONSE_FAILED" ;
            // Do something.
        } elseif ($akismet->isError('AKISMET_SERVER_NOT_FOUND')) {
            return 3; // echo "AKISMET_SERVER_NOT_FOUND" ;
            // Do something.
        }
    } else {
        // No errors, check for spam.
        if ($akismet->isSpam()) { // Returns true if Akismet thinks the comment is spam.
            return 4; // "SPAM";
            // Do something with the spam comment.
        } else {
            return 0; // "OKAY";
            // Do something with the non-spam comment.
        }
    }

}


?>
