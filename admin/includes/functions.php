<?php

function setPriority($id) {
    switch ($id) {
        case 1 :
            return "Critical";
            break;

        case 2 :
            return "High";
            break;

        case 3 :
            return "Normal";
            break;

        case 4 :
            return "Low";
            break;
    }
}

function createDbScript($tbl = null) {

    $answers = " CREATE TABLE IF NOT EXISTS `answers` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `a_msg_id` int(11) NOT NULL,
			  `a_msg_date` varchar(16) NOT NULL,
			  `a_response` varchar(16) NOT NULL,
			  `a_date` varchar(16) NOT NULL,
			  `a_content` text NOT NULL,
			  `a_email` varchar(100) NOT NULL,
			  `a_account` int(11) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;\n ";

    $messages = " CREATE TABLE IF NOT EXISTS `messages` (
			  `msg_id` int(11) NOT NULL AUTO_INCREMENT,
			  `msg_ticket_id` varchar(50) NOT NULL,
			  `msg_date` varchar(16) NOT NULL,
			  `msg_update_date` varchar(16) NOT NULL,
			  `msg_subject` varchar(100) NOT NULL,
			  `msg_name` varchar(100) NOT NULL,
			  `msg_email` varchar(100) NOT NULL,
			  `msg_content` text NOT NULL,
			  `msg_division` varchar(50) NOT NULL,
			  `msg_phone` varchar(25) NOT NULL,
			  `msg_priority` int(11) NOT NULL DEFAULT '3',
			  `msg_status` varchar(20) NOT NULL,
			  `msg_spam` tinyint(1) NOT NULL DEFAULT '0',
			  `msg_product` varchar(100) NOT NULL,
			  PRIMARY KEY (`msg_id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;\n ";

    $accounts = " CREATE TABLE IF NOT EXISTS `accounts` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `username` varchar(50) NOT NULL,
				  `password` varchar(50) NOT NULL,
				  `email` varchar(100) NOT NULL,
				  `delete_right` int(11) NOT NULL,
				  `close_right` int(11) NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;\n ";

    $faqs = " CREATE TABLE IF NOT EXISTS `faqs` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `message` LONGTEXT NOT NULL,
				  `privacy` int(11) NOT NULL,
				  `product` varchar(50) NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;\n ";

    $faq_rating = " CREATE TABLE IF NOT EXISTS `faq_rating` (
				  `votes` int(11) NOT NULL AUTO_INCREMENT,
				  `fid` int(11) NOT NULL,
				  `stars` int(11) NOT NULL
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;\n ";


    if ($tbl == 1) {
        return $messages;
    }
    if ($tbl == 2) {
        return $answers;
    }
    if ($tbl == 4) {
        return $accounts;
    }
    if ($tbl == 3) {
        return $messages . $answers;
    }
    if ($tbl == 5) {
        return $messages . $accounts;
    }
    if ($tbl == 7) {
        return $messages . $answers . $accounts;
    }
    if ($tbl == 8) {
        return $faqs;
    }
}

function getDateDiff_Org($date1) {

    $return = '';
    date_default_timezone_set('America/New_York');
    $date1 = new DateTime($date1);
    $date2 = new DateTime(strtotime(time()));

    $diff = $date1->diff($date2);
    if ($diff->y != 0) {
        $diff->y == 1 ? $return .= '1 year ' : $return .= $diff->y . ' years ';
    }
    if ($diff->m != 0) {
        $diff->m == 1 ? $return .= '1 month ' : $return .= $diff->m . ' months ';
    }
    if ($diff->y != 0) {
        return $return . '';
    }

    if ($diff->d != 0) {
        $diff->d == 1 ? $return .= '1 day ' : $return .= $diff->d . ' days ';
    }
    if ($diff->m != 0) {
        return $return . '';
    }

    if ($diff->h != 0) {
        $diff->h == 1 ? $return .= '1 hour ' : $return .= $diff->h . ' hours ';
    }
    if ($diff->y == 0 && $diff->m == 0 && $diff->d != 0) {
        return $return . '';
    }

    if ($diff->i != 0) {
        $diff->i == 1 ? $return .= '1 minute ' : $return .= $diff->i . ' minutes ';
    }
    if ($diff->y == 0 && $diff->m == 0 && $diff->d == 0 && $diff->h != 0) {
        return $return . '';
    }

    if ($diff->y == 0 && $diff->m == 0 && $diff->d == 0 && $diff->h == 0 && $diff->i != 0) {
        return $return . '';
    }

    if ($diff->s != 0) {
        $diff->s == 1 ? $return .= '1 second ' : $return .= $diff->s . ' seconds ';
    }

    return $return . '';
}

function createMenu($which, $who) {

    $config = simplexml_load_file(plugin_dir_path(__FILE__) . '../../config/config.xml');
    echo '<li ';
    if ($which == 1) {
        echo 'class="active"';
    }
    echo '><a href="?page=ticketsys-messages-settings">' . __('Messages', 'ticketsys') . '</a></li>
<li ';
    if ($which == 2) {
        echo 'class="active"';
    }
    echo '><a href="?page=ticketsys-users-settings">' . __('Users', 'ticketsys') . '</a></li> ';
    if ($config->productOption == "yes") {
        echo ' <li ';
        if ($which == 3)
            echo 'class="active"';
        echo '><a href="?page=ticketsys-products-settings">' . __('Products', 'ticketsys') . '</a></li>';
    }
    echo '<li ';
    if ($which == 4) {
        echo 'class="active"';
    }
    echo '><a href="?page=ticketsys-divisions-settings">' . __('Divisions', 'ticketsys') . '</a></li>
      <li ';
    if ($which == 5) {
        echo 'class="active"';
    }
    echo '><a href="?page=ticketsys-email-settings">' . __('Emails', 'ticketsys') . '</a></li>

 <li ';
    if ($which == 9) {
        echo 'class="active"';
    }
    echo '><a href="?page=ticketsys-faq-settings">' . __('Faqs', 'ticketsys') . '</a></li>     

 <li ';

    if ($who == true) {
        if ($which == 6) {
            echo 'class="active"';
        }
        echo '><a href="?page=ticketsys-settings-settings">' . __('Settings', 'ticketsys') . '</a></li>
	  <li ';
    }
    if ($which == 7) {
        echo 'class="active"';
    }
    echo '><a href="?page=ticketsys-backup-settings">' . __('Backup', 'ticketsys') . '</a></li>
	  <li ';
    if ($which == 8) {
        echo 'class="active"';
    }
    echo '><a href="?page=ticketsys-stats-settings">' . __('Statistics', 'ticketsys') . '</a></li>';
}

function stopInjection($string) {

    $string = mysql_real_escape_string($string);
    $string = stripslashes($string);
    $string = strip_tags($string);

    return $string;
}

function time_elapsed($ptime) {
    $etime = time() - $ptime;

    if ($etime < 1) {
        return _e('just now', 'ticketsys');
    }

    $a = array(12 * 30 * 24 * 60 * 60 => __('Y', 'ticketsys'),
        30 * 24 * 60 * 60 => __('M', 'ticketsys'),
        24 * 60 * 60 => __('d', 'ticketsys'),
        60 * 60 => __('h', 'ticketsys'),
        60 => __('m', 'ticketsys'),
        1 => __('s', 'ticketsys')
    );

    foreach ($a as $secs => $str) {
        $d = $etime / $secs;
        if ($d >= 1) {
            $r = round($d);
            //return $r . ' ' . $str . ($r > 1 ? 's' : '') . ' ago';
            return $r . ' ' . $str;
        }
    }
}

function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'Y',
        'm' => 'M',
        'w' => 'W',
        'd' => 'd',
        'h' => 'h',
        'i' => 'm',
        's' => 's',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full)
        $string = array_slice($string, 0, 1);
    //return $string ? implode(', ', $string) . ' ago' : 'just now';
    return $string ? implode(', ', $string) : 'just now';

    // how to output
    //echo time_elapsed_string('2013-05-01 00:22:35');
    //echo time_elapsed_string('@1367367755'); # timestamp input
    //echo time_elapsed_string('2013-05-01 00:22:35', true);
}

function time_elapsed_nginx($datetime, $full = false) {

//Use this code as it will run in both apache and nginx environments without any error:
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    if (isset($diff)) {
        $string = array(
            'y' => 'Y',
            'm' => 'M',
            'd' => 'd',
            'h' => 'h',
            'i' => 'm'
        );

        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full)
            $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }
    else {
        return 0;
    }
}

?>