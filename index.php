<?php
session_start();
if(!array_key_exists("filename", $_SESSION))
    $_SESSION['filename'] = "";

if( isset( $_FILES['file'] ) ) {
    $file_contents = file_get_contents( $_FILES['file']['tmp_name'] );

    $filename = substr(sha1_file($_FILES['file']['tmp_name']), 0, 10);

    $_SESSION['filename'] = "s/" . $filename . ".png";

    if (!move_uploaded_file(
        $_FILES['file']['tmp_name'],
        sprintf('./s/%s.png', $filename)
    )) {
        die("error!");
    }

    header("Content-Type: " . $_FILES['file']['type'] );
    echo($file_contents);
}
else {
    header("HTTP/1.1 200 OK");
}
session_write_close();
?><!doctype html>
<html>
<head>
    <link href='style.css' rel='stylesheet' type='text/css'>
    <title>BlackHole :: Top Hat and Monocle</title>
</head>
<body>

    <div id="resulttext"></div>

    <div class="container" id="bhcontainer">
        <div class="cent">
            <img src="blackhole.jpg" alt="BlackHole" id="blackhole">
            <div id="whatdo">
                CTRL + V
            </div>
        </div>
    </div>


<script src="jquery.min.js"></script>
<script src="jquery.rotate.min.js"></script>
<script src="jquery.color.js"></script>
<script type="text/javascript">
var interval = 0;
var angle = 0;
var speed = 0.03;

$(document).ready(function(){
    interval = setInterval(function(){
          angle-=0.03;
         $("#blackhole").rotate(angle);
    },10);
    
})

var mac=navigator.platform.match(/(Mac|iPhone|iPod|iPad)/i)?true:false;
var defaulttext = "CTRL + V";    

if(mac){
    defaulttext = "&#8984; + V";
    $('#whatdo').html(defaulttext);
}


document.onpaste = function (e) {
    var items = e.clipboardData.items;
    var files = [];



    for( var i = 0, len = items.length; i < len; ++i ) {
        var item = items[i];
        if( item.kind === "file" ) {

            clearInterval(interval);
            interval = setInterval(function(){
                  angle+=0.9;
                 $("#blackhole").rotate(angle);
            },10);
            $('#whatdo').fadeOut('slow', function(){
                $(this).addClass('whatdo-small');
                $(this).html("Uploading!");
                $(this).fadeIn();
            });
            submitFileForm(item.getAsFile(), "paste");
        }
        else
        {
            $('#whatdo').fadeOut('slow', function(){
                $(this).addClass('whatdo-small');
                $(this).html("No Image! Try Again");
                $(this).fadeIn();
                interval = window.setTimeout(function() {
                    $('#whatdo').fadeOut('slow', function(){
                        $(this).html(defaulttext);
                        $(this).removeClass('whatdo-small');
                        $(this).fadeIn();
                    });
                }, 3000);

            })
        }
    }

};

function submitFileForm(file, type) {
    <?php session_start(); ?>
    var extension = file.type.match(/\/([a-z0-9]+)/i)[1].toLowerCase();
    var formData = new FormData();
    formData.append('file', file, "image_file");
    formData.append('extension', extension );
    formData.append("mimetype", file.type );
    formData.append('submission-type', type);

    var xhr = new XMLHttpRequest();
    xhr.responseType = "blob";
    xhr.open('POST', '<?php echo basename(__FILE__); ?>');
    xhr.onload = function () {
        if (xhr.status == 200) {
            console.log(xhr.response);
            /*var img = new Image();
            img.src = (window.URL || window.webkitURL)
                .createObjectURL( xhr.response );
            document.body.appendChild(img);*/
            clearInterval(interval);

            $('#blackhole').rotate(0);
            $('#whatdo').html("");

            $.get( "filefinder.php", function( data ) {
               $( "#resulttext" ).html("<?php echo("http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']) ?>" + data )
            });

            $('html, body').animate({
                backgroundColor: "#FFFFFF"
            }, 5000);
            var img = $('#blackhole');
            img.attr('src', (window.URL || window.webkitURL).createObjectURL( xhr.response )); 
        }
    };

    xhr.send(formData);
}
</script>
</body>
</html>