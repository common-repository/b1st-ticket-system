<?php

function loadGmailMails($from = 0, $to = 0)
{
    $hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
    $username = 'mohamed.alyabbas@gmail.com';
    $password = "Mohamed2013";

    $inbox = imap_open($hostname, $username, $password) or die('Cannot connect to Gmail: ' . imap_last_error());
    $e = imap_search($inbox, 'ALL');

    $emails = array();

    if ($e) {
        rsort($e); //put the newest emails on top

        if ($to == 0) {
            $to = sizeof($e);
        }

        for ($i = $from; $i < $to; $i++) {
            $overview = imap_fetch_overview($inbox, $e[$i], 0);
            $message = imap_fetchbody($inbox, $e[$i], 1);

            preg_match('/(?P[a-zA-Z ]+)<(?P.+)>/', $overview[0]->from, $match);
            trim($match['name']);
            $emails[] = array(
                'read' => $overview[0]->seen,
                'subject' => $overview[0]->subject,
                'from' => array('name' => $match['name'], 'address' => $match['address']),
                'date' => $overview[0]->date,
                'message' => $message
            );
        }
    }

    imap_close($inbox);

    return $emails;
}

loadGmailMails(0, 1);
?>