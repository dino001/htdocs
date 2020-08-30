var url_ajax = 'server.php';
var intervalID = null;

function get_random_word(){
    var input_data = {
        action:"get_random_word",
        length_min: $("#wordCountSlider").slider( "values", 0 ),
        length_max: $("#wordCountSlider").slider( "values", 1 )
    };
    $.post(
        url_ajax,
        input_data,
        function(ajaxdata){
            console.log(ajaxdata);
            $("#mainWord").html(ajaxdata.data.random_word);
        },
        'json'
    );
}

function startRandomizing() {
    get_random_word();
    intervalID = setInterval(function(){
        get_random_word();
    }, $("#intervalSlider").slider("value") * 1000);
}

function stopRandomizing() {
    clearInterval(intervalID);
}


/**
* Init events and stuff
*/
$(document).ready(function() {
    $("#btnStartStop").click(function(){
        var isStarted = $(this).data('started');
        if (isStarted == 1) {
            //We should stop
            $(this).html("Bắt đầu");
             $(this).data('started', 0);
             stopRandomizing();
        } else {
            //We should start
            $(this).html("Tạm dừng");
            $(this).data('started', 1);
            startRandomizing();
        }
    });

    $("#btnOneOff").click(function(){
        get_random_word();
    });

    $("#wordCountSlider").slider({
        range: true,
        min: 1,
        max: 4,
        values: [1, 4],
        slide: function( event, ui ) {
            $( "#wordCountDisplay" ).html( ui.values[ 0 ] + " - " + ui.values[ 1 ] );
        }
    });
    $( "#wordCountDisplay" ).html( $("#wordCountSlider").slider( "values", 0 ) + " - " + $("#wordCountSlider").slider( "values", 1 ) );

     $("#intervalSlider").slider({
        range: "min",
        min: 1,
        max: 20,
        value: 4,
        slide: function( event, ui ) {
            $( "#intervalTimeDisplay" ).html( ui.value );
        }
    });
    $( "#intervalTimeDisplay" ).html( $("#intervalSlider").slider("value") );
});