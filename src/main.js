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
            document.getElementById("title").innerHTML = getTitle();
            document.getElementById("author").innerHTML = "by <strong>" + getAuthor() +"</strong>";
            document.getElementById("description").innerHTML = marked(getDescription());             
        })
}

function getTitle() {
    var result = sandbox_content.match(/\/\/\s*[T|t]itle\s*:\s*([\w|\s|\@|\(|\)|\-|\_]*)/i);
    if (result && !(result[1] === ' ' || result[1] === '')) {
        return result[1].replace(/(\r\n|\n|\r)/gm, '');
    }
    else {
        return "untitled";
    }
}

function getAuthor() {
    var result = sandbox_content.match(/\/\/\s*[A|a]uthor\s*[\:]?\s*([\w|\s|\@|\(|\)|\-|\_]*)/i);
    if (result && !(result[1] === ' ' || result[1] === '')) {
        return result[1].replace(/(\r\n|\n|\r)/gm, '');
    }
    else {
        return "unknown";
    }
}

function getDescription() {
    var result = sandbox_content.match(/\/\/\s*[D|d]escription\s*[\:]?.*(\s*\n\/\/.*)*/m);
    if (result) {
        console.log(result);
        return result[0].replace(/\/\/\s*((d|D)escription:\s*)?/gm, '');
    }
    else {
        return "";
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
    sandbox.setUniform("u_tex0","imgs/moon.jpg");
    sandbox.setUniform("u_logo","imgs/logo.jpg");
}