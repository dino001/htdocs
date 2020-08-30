<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Cache-Control" content="no-cache">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta http-equiv="Lang" content="vn">
        <meta name="description" content="">
        <meta name="keywords" content="">
        <meta name="revisit-after" content="15 days">
        <title>SeaDict - Word Randomizer</title>
        <link rel="icon" type="image/png" href="favicon.ico">
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="css/main.css">
        <script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script type="text/javascript" src="js/randomizer.js?v=<?=time()?>"></script>
    </head>
    <body>
        <?php include_once("top_menu.php"); ?>
        <div id="divWrapperMain">
            <div id="wordDisplay">
                <span id="mainWord" style="font-size: xxx-large;">...</span>
            </div>
            <br><br>
            <div id="controller" style="font-family: Arial">
                <button id="btnStartStop" data-started="0">Bắt đầu</button><br><br>

                <button id="btnOneOff" data-started="0">Từ khác</button><br><br>

                <span>Độ dài của từ </span><span id="wordCountDisplay"></span><br><br>
                <div id="wordCountSlider" style="width: 120px;"></div><br><br>

                <span>Đổi từ mới sau </span><span id="intervalTimeDisplay"></span><span> giây</span><br><br>
                <div id="intervalSlider" style="width: 120px;"></div><br><br>
            </div>
        </div>
    </body>
</html>
