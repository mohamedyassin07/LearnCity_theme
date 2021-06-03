<?php 
$helpers =  array('base','general','debug','test','test_page','test_ouside_hooks','meta_data');
$dir = __DIR__;
foreach ($helpers as $helper) {
    include_once($dir.'/'.$helper.'.php');
}