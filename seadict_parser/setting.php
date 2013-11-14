<?php

    //$g_userId = isset($_SESSION['userId']) ? $_SESSION['userId'] : null;    
    
    $g_dateFormat = "d/m/Y";  //view format date only
    $g_dateTimeFormat = "d/m/Y h:i A";       //view format date time    
    //$g_dateTimeFormat = "Y/m/d G:i:s";
    $g_dateFormatDb = "Y-m-d H:i:s";     //json and db format 

    //security check
    //AIUser::checkSession();    
        
    $g_map_tone = array(
    0=>"Ngang",
    1=>"Sắc",
    2=>"Huyền",
    3=>"Hỏi",
    4=>"Ngã",
    5=>"Nặng");
    
    $g_map_wordtype = array(
    0=>"",
    1=>"Danh từ",
    2=>"Động từ",
    3=>"Tính từ");
    
?>
