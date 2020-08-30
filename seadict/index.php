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
        <title>SeaDict</title>
        <link rel="icon" type="image/png" href="favicon.ico">
        <link rel="stylesheet" type="text/css" href="css/main.css">
        <script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
        <script type="text/javascript" src="js/main.js?v=<?=time()?>"></script>
    </head>
    <body>
        <?php include_once("top_menu.php"); ?>
        <div id="divWrapperMain">
            <form id="frmMain" action="" method="post">
                <div id="divSearchBar">
                    <input type="text" id="txtSearch" name="txtSearch" />
                    <button id="btnSearch">Tìm</button>
                </div>
                <br/>
                <div id="divResult">
                </div>

                <div id="divSample" class="cssDivTone" style="display: none;">
                    <div class="cssToneTitle">Thanh ngang</div>
                    <hr/>
                    <div class="cssWordList">
                        <div class="cssOneWord"><span class="cssMainOneWord">biên</span>: biên hòa, biên phòng</div>
                        <div class="cssOneWord"><span class="cssMainOneWord">biên</span>: biên hòa, biên phòng</div>
                        <div class="cssOneWord"><span class="cssMainOneWord">biên</span>: biên hòa, biên phòng</div>
                    </div>
                </div>
            </form>
        </div>
    </body>
</html>
