<?php

/**
 * Created by JetBrains PhpStorm.
 * User: SUDARSHAN
 * Date: 2/23/13
 * Time: 11:11 PM
 * To change this template use File | Settings | File Templates.
 */
session_start();
require_once('xml.php');

$style = json_decode(stripcslashes($_GET['jsonStr']));
//echo stripcslashes($_GET['jsonStr']);
$xmlParser = new crxml();
$xmlParser->loadXML(self::get_contents('design.xml'));
//print_r($xmlParser->design);
//print_r($style);exit;
//echo $style->headerTopBGColor;exit;

$total_style_node = count(simplexml_load_string($xmlParser->xml())->style);
//print_r($total_style_node);exit;

$xmlParser->design->style[$total_style_node]['name'] = 'style' . ($total_style_node + 1);
$xmlParser->design->style[$total_style_node]->header->color[0] = $style->headerTopBGColor;
$xmlParser->design->style[$total_style_node]->header->color[1] = $style->headerMiddleBGColor;
$xmlParser->design->style[$total_style_node]->header->color[2] = $style->headerBottomBGColor;
$xmlParser->design->style[$total_style_node]->headerhover->color[0] = $style->headerHoverTopBGColor;
$xmlParser->design->style[$total_style_node]->headerhover->color[1] = $style->headerHoverMiddleBGColor;
$xmlParser->design->style[$total_style_node]->headerhover->color[2] = $style->headerHoverBottomBGColor;
$xmlParser->design->style[$total_style_node]->menuseparator->color[0] = $style->menuSeparatorTopBGColor;
$xmlParser->design->style[$total_style_node]->menuseparator->color[1] = $style->menuSeparatorMiddleBGColor;
$xmlParser->design->style[$total_style_node]->menuseparator->color[2] = $style->menuSeparatorBottomBGColor;
$xmlParser->design->style[$total_style_node]->fontcolor->color = $style->fontColor;
$xmlParser->design->style[$total_style_node]->lddmenu->topbgcolor = $style->lddMenuTopbgColor;
$xmlParser->design->style[$total_style_node]->lddmenu->footbgcolor = $style->lddMenuFootbgColor;
$xmlParser->design->style[$total_style_node]->lddmenu->footfontcolor = $style->lddMenuFootfontColor;
$xmlParser->design->style[$total_style_node]->lddmenu->icon = $style->lddMenulefticon;
$xmlParser->design->style[$total_style_node]->lddmenu->lihoverbg = $style->lddMenulihoverbg;
$xmlParser->design->style[$total_style_node]->lddmenu->liahoverbordertop = $style->lddMenuliahoverbordertop;
$xmlParser->design->style[$total_style_node]->lddmenu->liahoverborderbottom = $style->lddMenuliahoverborderbottom;
$xmlParser->design->style[$total_style_node]->lddmenu->liahoverborderright = $style->lddMenuliahoverborderright;
$xmlParser->design->style[$total_style_node]->lddmenu->liahoverbg = $style->lddMenuliahoverbg;
$xmlParser->design->style[$total_style_node]->lddmenu->liahovercolor = $style->lddMenuliahovercolor;
$xmlParser->design->style[$total_style_node]->lddmenu->rotation = $style->lddMenuRotation;
$xmlParser->design->style[$total_style_node]->lddmenu->rotationspeed = $style->lddMenuRotationSpeed;
$xmlParser->design->style[$total_style_node]->lddmenu->slidetime = $style->lddMenuSlideTime;
$xmlParser->design->style[$total_style_node]->widgetcolor->color[0] = $style->widgetMenucolor1;
$xmlParser->design->style[$total_style_node]->widgetcolor->color[1] = $style->widgetMenucolor2;
$xmlParser->design->style[$total_style_node]->widgetcolor->color[2] = $style->widgetMenucolor3;
$xmlParser->design->style[$total_style_node]->widgetcolor->color[3] = $style->widgetMenucolor4;
$xmlParser->design->style[$total_style_node]->widgetcolor->color[4] = $style->widgetMenucolor5;

if (self::put_contents('design.xml', $xmlParser->xml())) {
    //$_SESSION['read_xml_first'] = 'unread';
    unset($_SESSION['read_xml_first']);
    $xmlParser->loadXML(self::get_contents('style_loader.xml'));
    $xmlParser->design->stylenumber = ($total_style_node + 1);
    $xmlParser->design->totalstyle = ($total_style_node + 1);
    if (self::put_contents('style_loader.xml', $xmlParser->xml())) {
        _e('success', 'ticketsys');
    } else {
        _e('designed changed but not loader', 'ticketsys');
    }
} else {
    _e('fail', 'ticketsys');
}

//$style = json_decode($_GET['jsonStr']);*/
?>