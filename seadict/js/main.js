var url_ajax = 'server.php';

function search_syllable(){
    var word = $("#txtSearch").val().trim();    
    var inputdata = {
        action:"search_syllable",
        word:word
    }
    $.post(url_ajax, inputdata,
    function(ajaxdata){
        console.log(ajaxdata.data);
        html_result(ajaxdata);  
    },'json');    
}

/**
* Parse json data and show on page
*/
function html_result(ajaxdata)
{
    var i;
    var data = ajaxdata.data;
    $("#divResult").empty();
    for(i=0; i<=6; i++)
    {                
        if (data.hasOwnProperty(i + ""))
        {
            //Each collection of the same tone
            var divTone = $("#divSample").clone().appendTo("#divResult");
            $(divTone).children(".cssToneTitle").text(map_tone_title(i));
            var divWordList = $(divTone).children(".cssWordList");
            $(divWordList).empty();
            //Loop through each word
            $.each(data[i+""], function(keyword, arr_word){
                var divOneWord = $("<div>").appendTo(divWordList);
                divOneWord.addClass('cssOneWord');
                $(divOneWord).prepend("<img class='cssBullet' src='image/bullet1.gif' />");
                divOneWord.append("<span class='cssMainOneWord'>" + keyword + "</span>: ");                
                $.each(arr_word, function(k, arr_fullword){
                    var spanFullWord = $("<span>").appendTo(divOneWord);
                    spanFullWord.addClass('cssFullWord');
                    spanFullWord.append(arr_fullword.fullword + ", ");                            
                });
            });
            
            $(divTone).show();
        }        
    }
}

function map_tone_title(tone)
{
    var tone_title = "";
    switch(tone)
    {
        case 0:
            tone_title = "Thanh ngang";
            break;
        case 1:
            tone_title = "Thanh sắc";
            break;
        case 2:
            tone_title = "Thanh huyền";
            break;
        case 3:
            tone_title = "Thanh hỏi";
            break;
        case 4:
            tone_title = "Thanh ngã";
            break;
        case 5:
            tone_title = "Thanh nặng";
            break;        
    }
    return tone_title;
}

/**
* Init events and stuff
*/
$(document).ready(function() {    
    //Prevent form submit
    $("#frmMain").submit(function(e){
        e.preventDefault();
        search_syllable();
    }); 
           
    $(document).on("click", ".cssToneTitle", function(event){
        $(this).parent().children(".cssWordList").toggle();
        $(this).parent().children("hr").toggle();
    });
    
    //Set CSS
    $(".cssDivTone").addClass("rounded-corners");
    $(".cssOneWord").prepend("<img class='cssBullet' src='image/bullet1.gif' />");    
});