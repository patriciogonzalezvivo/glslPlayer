<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
<?php
    if (!empty($_GET['log'])) {
        echo '
        <title>'.$_GET['log'].'</title>';
    } else {
        echo '
        <title>GlslPlayer</title>';
    }
?>

        <!— Open Graph data —>
        <meta property="og:type" content="article"/>
        <meta property="og:title" content="GLSL Player"/>
        <meta property="og:site_name" content="The Book of Shaders"/>
        <meta property="og:description" content="Display your GLSL shaders as artworks"/>
<?php
    if (!empty($_GET['log'])) {
        echo '        
        <meta property="og:image" content="http://thebookofshaders.com/log/'.$_GET['log'].'.png"/>
        <meta property="og:image:secure_url" content="https://thebookofshaders.com/log/'.$_GET['log'].'.png"/>';
    } else {
        echo '
        <meta property="og:image" content="https://thebookofshaders.com/thumb.png"/>';
    }

    echo'
        <meta property="og:image:type" content="image/png"/>
        <meta property="og:image:width" content="500" />
        <meta property="og:image:height" content="500" />';
?>
        <!-- jQuery -->
        <script src="src/jquery.js"></script>

        <!-- Bootstrap -->
        <link href="src/bootstrap.min.css" rel="stylesheet">
        <script src="src/bootstrap.min.js"></script>
        <!-- Fetch -->
        <script type="text/javascript" src="src/fetch.js"></script>
        <!-- Marked: markdown parser -->
        <script type="text/javascript" src="src/marked.js"></script>
        <!-- GLSL Canvas -->
        <script type="text/javascript" src="https://thebookofshaders.com/glslCanvas/GlslCanvas.js"></script>
        <link href="src/style.css" rel="stylesheet">

    </head>
    <body>
        <div id="wrapper" >
            <div id="page-content-wrapper">
                <a href="#menu-toggle" class="btn btn-default" id="menu-toggle"><span class="glyphicon glyphicon-menu-hamburger" aria-hidden="true"></span></a>
                <canvas id="glslCanvas" data-fragment-url="src/moon.frag" width="800" height="600"></canvas>
            </div>
            <div id="sidebar-wrapper">
                <p class="label" id="title"></p>
                <p class="label" id="author"></p>
                <div class="label" id="description"></div>
            </div>
        </div>
    <!-- /#wrapper -->
    </body>
    <script type="text/javascript" src="src/main.js"></script>
    <script>
        $("#menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
        });
        (function(i,s,o,g,r,a,m){i["GoogleAnalyticsObject"]=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,"script","//www.google-analytics.com/analytics.js","ga");
        ga("create", "UA-18824436-2", "auto");
        ga("send", "pageview");
    </script>
</html>