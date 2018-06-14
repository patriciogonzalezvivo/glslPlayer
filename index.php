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
        <script type="text/javascript" src="http://patriciogonzalezvivo.github.io/glslCanvas/dist/GlslCanvas.js"></script>
        <link href="src/style.css" rel="stylesheet">

    </head>
    <body>
        <div id="wrapper" >
            <div id="page-content-wrapper">
                <a href="#menu-toggle" class="btn btn-default" id="menu-toggle"><span class="glyphicon glyphicon-menu-hamburger" aria-hidden="true"></span></a>
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
}
                " width="800" height="600"></canvas>
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