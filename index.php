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

        <script type="text/javascript" src="https://thebookofshaders.com/glslCanvas/GlslCanvas.js"></script>
        <script type="text/javascript" src="src/fetch.js"></script>

        <!— Open Graph data —>
        <meta property="og:type" content="article" />
        <meta property="og:title" content="GLSL Shader" />
        <meta property="og:site_name" content="The Book of Shaders"/>
        <meta property="og:description" content="The Book of Shaders player" />
<?php
    if (!empty($_GET['log'])) {
        echo '        <meta property="og:image" content="https://thebookofshaders.com/log/'.$_GET['log'].'.png"/>
        <meta property="og:image:type" content="image/png"/>
        <meta property="og:image:width" content="500" />
        <meta property="og:image:height" content="500" />';
    }
?>

        <!— Twitter Card—>
        <meta name="twitter:card" content="image">
        <meta name="twitter:site" content="@bookofshaders">
        <meta name="twitter:title" content="GLSL Shader">
        <meta name="twitter:description" content="The Book of Shaders player">
        <meta name="twitter:domain" content="thebookofshaders.com">
<?php
    if (!empty($_GET['log'])) {
        echo '
        <meta name="twitter:url" content="https://thebookofshaders.com/glslPlayer/?log='. $_GET['log'].'"/>
        <meta name="twitter:image" content="https://thebookofshaders.com/log/'.$_GET['log'].'.png"/>
        <meta name="twitter:image:width" content="500">
        <meta name="twitter:image:height" content="500">';
    }
?>
        <!-- Bootstrap Core CSS -->
        <link href="src/bootstrap.min.css" rel="stylesheet">
        <link href="src/simple-sidebar.css" rel="stylesheet">
        <!-- jQuery -->
        <script src="src/jquery.js"></script>
        <!-- Bootstrap Core JavaScript -->
        <script src="src/bootstrap.min.js"></script>

        <style>
            body {
                background: #101515;
                overflow-x: hidden;
            }

            #wrapper {
                padding-rigth: 0;
                -webkit-transition: all 0.5s ease;
                -moz-transition: all 0.5s ease;
                -o-transition: all 0.5s ease;
                transition: all 0.5s ease;
                width: 100%;
                height: 100%;
            }

            #wrapper.toggled {
                padding-rigth: 250px;
            }

            #sidebar-wrapper {
                z-index: 1000;
                position: fixed;
                right: 250px;
                width: 0;
                height: 100%;
                margin-rigth: -250px;
                overflow-y: auto;
                background: #000;
                -webkit-transition: all 0.5s ease;
                -moz-transition: all 0.5s ease;
                -o-transition: all 0.5s ease;
                transition: all 0.5s ease;
            }

            #wrapper.toggled #sidebar-wrapper {
                width: 250px;
            }

            #page-content-wrapper {
                width: 100%;
                height: 100%;
                position: absolute;
                padding: 15px;
            }

            #wrapper.toggled #page-content-wrapper {
                position: absolute;
                margin-right: -250px;
            }

            @media(min-width:768px) {
                #wrapper {
                    padding-rigth: 250px;
                }

                #wrapper.toggled {
                    padding-rigth: 0;
                }

                #sidebar-wrapper {
                    width: 250px;
                }

                #wrapper.toggled #sidebar-wrapper {
                    width: 0;
                }

                #page-content-wrapper {
                    padding: 20px;
                }

                #wrapper.toggled #page-content-wrapper {
                    margin-right: 0;
                }
            }

            #glslCanvas {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%,-50%);
            }​

            .label {
                color: white;
                font-family: Helvetica, Arial, sans-serif;
                text-decoration: none;
                white-space: normal;
            }

            #title {
                font-size: 24px;
                font-weight: 600;
                display: block;
                margin: auto;
                padding-top: 30px;
                padding-bottom: 20px;
                white-space: normal;
            }

            #author {
                font-size: 18px; 
                font-weight: 200;
                display: block;
                margin: auto;
                white-space: normal;
            }
        </style>
    </head>
    <body>
        <div id="wrapper" class="toggled">
            <div id="page-content-wrapper">
                <a href="#menu-toggle" class="btn btn-default" id="menu-toggle">
                    <canvas id="glslCanvas" data-fragment="
        // Author: Patricio Gonzalez Vivo

        #ifdef GL_ES
        precision mediump float;
        #endif

        #define PI 3.1415926535
        #define HALF_PI 1.57079632679

        uniform vec2 u_resolution;
        uniform float u_time;

        uniform sampler2D u_tex0;
        uniform vec2 u_tex0Resolution;

        uniform sampler2D u_logo;
        uniform vec2 u_logoResolution;

        float speedMoon = 0.01;
        float speedSun = 0.25;

        vec3 sphereNormals(in vec2 uv) {
            uv = fract(uv)*2.0-1.0; 
            vec3 ret;
            ret.xy = sqrt(uv * uv) * sign(uv);
            ret.z = sqrt(abs(1.0 - dot(ret.xy,ret.xy)));
            ret = ret * 0.5 + 0.5;    
            return mix(vec3(0.0), ret, smoothstep(1.0,0.98,dot(uv,uv)) );
        }

        vec2 sphereCoords(vec2 _st, float _scale){
            float maxFactor = sin(1.570796327);
            vec2 uv = vec2(0.0);
            vec2 xy = 2.0 * _st.xy - 1.0;
            float d = length(xy);
            if (d < (2.0-maxFactor)){
                d = length(xy * maxFactor);
                float z = sqrt(1.0 - d * d);
                float r = atan(d, z) / 3.1415926535 * _scale;
                float phi = atan(xy.y, xy.x);

                uv.x = r * cos(phi) + 0.5;
                uv.y = r * sin(phi) + 0.5;
            } else {
                uv = _st.xy;
            }
            return uv;
        }

        vec4 sphereTexture(in sampler2D _tex, in vec2 _uv) {
            vec2 st = sphereCoords(_uv, 1.0);

            float aspect = u_tex0Resolution.y/u_tex0Resolution.x;
            st.x = fract(st.x*aspect + u_time*speedMoon);

            return texture2D(_tex, st);
        }

        void main(){
            vec2 st = gl_FragCoord.xy/u_resolution.xy;
            st = (st-.5)*1.0+.5;
            if (u_resolution.y > u_resolution.x ) {
                st.y *= u_resolution.y/u_resolution.x;
                st.y -= (u_resolution.y*.5-u_resolution.x*.5)/u_resolution.x;
            } else {
                st.x *= u_resolution.x/u_resolution.y;
                st.x -= (u_resolution.x*.5-u_resolution.y*.5)/u_resolution.y;
            }

            vec4 color = vec4(1.0);
          
            color *= sphereTexture(u_tex0, st);

            // Calculate sun direction
            vec3 sunPos = normalize(vec3(cos(u_time*speedSun-HALF_PI),0.0,sin(speedSun*u_time-HALF_PI)));
            vec3 surface = normalize(sphereNormals(st)*2.0-1.0);
           
            // Add Shadows
            color *= dot(sunPos,surface);

            // Blend black the edge of the sphere
            float radius = 1.0-length( vec2(0.5)-st )*2.0;
            color *= smoothstep(0.001,0.02,radius);

            if (u_logoResolution.x > 0.0) {
                st -= 0.25;
                st *= 2.0;
                color.rgb += texture2D(u_logo,st).rgb * smoothstep(0.71,0.75, 1.0-dot(st-vec2(.5),st-vec2(.5)) );
            }
            gl_FragColor = color;
        }" width="800" height="600">
                    </canvas>
                </a>
            </div>
            <div id="sidebar-wrapper">
                <p class="label" id="title"></p>
                <p class="label" id="author"></p>
            </div>
        </div>
    <!-- /#wrapper -->
    </body>

    <script>
        $("#menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
        });

        var canvas = document.getElementById("glslCanvas");
        var sandbox = new GlslCanvas(canvas);
        var texCounter = 0;
        var sandbox_content = "";
        var sandbox_title = "";
        var sandbox_author = "";
        var sandbox_thumbnail = ""; 
        canvas.style.width = '100%';
        canvas.style.height = '100%';
        function parseQuery (qstr) {
            var query = {};
            var a = qstr.split('&');
            for (var i in a) {
                var b = a[i].split('=');
                query[decodeURIComponent(b[0])] = decodeURIComponent(b[1]);
            }
            return query;
        }
        function load(url) {
            // Make the request and wait for the reply
            fetch(url)
                .then(function (response) {
                    // If we get a positive response...
                    if (response.status !== 200) {
                        console.log('Error getting shader. Status code: ' + response.status);
                        return;
                    }
                    // console.log(response);
                    return response.text();
                })
                .then(function(content) {
                    sandbox_content = content;
                    sandbox.load(content);
                    var title = addTitle();
                    var author = addAuthor();
                    // if ( title === "unknown" && author === "unknown") {
                    //     document.getElementById("credits").style.visibility = "hidden";
                    // } else {
                    //     document.getElementById("credits").style.visibility = "visible";
                    // }                
                })
        }

        function addTitle() {
            var result = sandbox_content.match(/\/\/\s*[T|t]itle\s*:\s*([\w|\s|\@|\(|\)|\-|\_]*)/i);
            if (result && !(result[1] === ' ' || result[1] === '')) {
                sandbox_title = result[1].replace(/(\r\n|\n|\r)/gm, '');
                var title_el = document.getElementById("title").innerHTML = sandbox_title;
                return sandbox_title;
            }
            else {
                return "untitled";
            }
        }

        function addAuthor() {
            var result = sandbox_content.match(/\/\/\s*[A|a]uthor\s*[\:]?\s*([\w|\s|\@|\(|\)|\-|\_]*)/i);
            if (result && !(result[1] === ' ' || result[1] === '')) {
                sandbox_author = result[1].replace(/(\r\n|\n|\r)/gm, '');
                document.getElementById("author").innerHTML = sandbox_author;
                return sandbox_author;
            }
            else {
                return "unknown";
            }
        }

        var query = parseQuery(window.location.search.slice(1));
        if (query && query.log) {
            sandbox_thumbnail = 'https://thebookofshaders.com/log/' + query.log + '.png';
            load('https://thebookofshaders.com/log/' + query.log + '.frag');
        }
        if (window.location.hash !== '') {
            var hashes = location.hash.split('&');
            for (var i in hashes) {
                var ext = hashes[i].substr(hashes[i].lastIndexOf('.') + 1);
                var path = hashes[i];
                // Extract hash if is present
                if (path.search('#') === 0) {
                    path = path.substr(1);
                }
                if (ext === 'frag') {
                    load(path);
                }
                else if (ext === 'png' || ext === 'jpg' || ext === 'PNG' || ext === 'JPG') {
                    sandbox.setUniform("u_tex"+texCounter.toString(), path);
                    texCounter++;
                }
            }
        }
        if (texCounter === 0) {
            sandbox.setUniform("u_tex0","data/moon.jpg");
            sandbox.setUniform("u_logo","data/logo.jpg");
        }
    </script>
    <script>
        (function(i,s,o,g,r,a,m){i["GoogleAnalyticsObject"]=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,"script","//www.google-analytics.com/analytics.js","ga");
        ga("create", "UA-18824436-2", "auto");
        ga("send", "pageview");
    </script>
</html>