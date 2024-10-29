<?php
switch ($config->layout) {
    case 1:
        include 'index1.php';
        break;
    case 2:
        include 'index2.php';
        break;
    case 3:
        include 'index3.php';
        break;
    case 4:
        include 'index4.php';
        break;
    case 5:
        include 'index5.php';
        break;
    default:
        include 'index1.php';
        break;
}
?>
