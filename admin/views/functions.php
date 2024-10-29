<?php

function setPriority($id) {
    switch ($id) {
        case 1 :
            return __('Critical', 'ticketsys');
            break;

        case 2 :
            return __('High', 'ticketsys');
            break;

        case 3 :
            return __('Normal', 'ticketsys');
            break;

        case 4 :
            return __('Low', 'ticketsys');
            break;
    }
}

function createDbScript($tbl = null) {

    $answers = " CREATE TABLE IF NOT EXISTS `answers` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `a_msg_id` int(11) NOT NULL,
			  `a_date` datetime NOT NULL,
			  `a_content` text NOT NULL,
			  `a_email` varchar(100) NOT NULL,
			  `a_account` int(11) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;\n ";

    $messages = " CREATE TABLE IF NOT EXISTS `messages` (
			  `msg_id` int(11) NOT NULL AUTO_INCREMENT,
			  `msg_ticket_id` varchar(50) NOT NULL,
			  `msg_date` datetime NOT NULL,
			  `msg_update_date` datetime NOT NULL,
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

function getDataDiff($date1) {

    $return = '~ ';
    date_default_timezone_set('America/New_York');
    $date1 = new DateTime($date1);
    $date2 = new DateTime(strtotime(time()));

    $diff = $date1->diff($date2);
    if ($diff->y != 0) {
        $diff->y == 1 ? $return .= '1y ' : $return .= $diff->y . 'y ';
    }
    if ($diff->m != 0) {
        $diff->m == 1 ? $return .= '1m ' : $return .= $diff->m . 'm ';
    }
    if ($diff->y != 0) {
        return $return . '';
    }

    if ($diff->d != 0) {
        $diff->d == 1 ? $return .= '1d ' : $return .= $diff->d . 'd ';
    }
    if ($diff->m != 0) {
        return $return . '';
    }

    if ($diff->h != 0) {
        $diff->h == 1 ? $return .= '1h ' : $return .= $diff->h . 'h ';
    }
    if ($diff->y == 0 && $diff->m == 0 && $diff->d != 0) {
        return $return . '';
    }

    if ($diff->i != 0) {
        $diff->i == 1 ? $return .= '1m ' : $return .= $diff->i . 'm ';
    }
    if ($diff->y == 0 && $diff->m == 0 && $diff->d == 0 && $diff->h != 0) {
        return $return . '';
    }

    if ($diff->y == 0 && $diff->m == 0 && $diff->d == 0 && $diff->h == 0 && $diff->i != 0) {
        return $return . '';
    }

    if ($diff->s != 0) {
        $diff->s == 1 ? $return .= '1s ' : $return .= $diff->s . 's ';
    }

    return $return . '';
}

function getDataDiff_Org($date1) {

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

?>