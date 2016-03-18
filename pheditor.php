<?php

/*
 * Pheditor
 * online PHP file editor
 * Hamid Samak
 * https://github.com/hamidsamak/pheditor
 * Release under MIT license
 */

define('EDITABLE_FORMATS', 'txt,php,htm,html,js,css,tpl,xml');

if (isset($_POST['action'])) {
	switch ($_POST['action']) {
		case 'open':
			if (isset($_POST['file']) && file_exists(__DIR__ . DIRECTORY_SEPARATOR . $_POST['file']))
				echo br2nl(highlight_string(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $_POST['file']), true));
			break;

		case 'save':
			if (isset($_POST['file']) && file_exists(__DIR__ . DIRECTORY_SEPARATOR . $_POST['file']) && isset($_POST['data'])) {
				file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . $_POST['file'], $_POST['data']);
				echo br2nl(highlight_string(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $_POST['file']), true));
			}
			break;
	}

	exit;
}

function files($dir, $display = 'block') {
	$formats = explode(',', EDITABLE_FORMATS);

	$data = '<ul class="files" style="display:' . $display . '">';
	$files = array_slice(scandir($dir), 2);

	asort($files);

	foreach ($files as $key => $file) {
		if ($dir . DIRECTORY_SEPARATOR . $file == __FILE__)
			continue;

		if (is_dir($dir . DIRECTORY_SEPARATOR . $file))
			$data .= '<li class="dir"><a href="javascript:void(0);" onclick="return expandDir(this);">' . $file . '</a>' . files($dir . DIRECTORY_SEPARATOR . $file, 'none') . '</li>';
		else {
			$is_editable = strpos($file, '.') === false || in_array(substr($file, strrpos($file, '.') + 1), $formats);

			$data .= '<li class="file ' . ($is_editable ? 'editable' : null) . '">';

			if ($is_editable === true)
				$data .= '<a href="javascript:void(0);" onclick="return openFile(this);" data-file="' . str_replace(__DIR__ . '/', '', $dir . DIRECTORY_SEPARATOR . $file) . '">';

			$data .= $file;

			if ($is_editable)
				$data .= '</a>';

			$data .= '</li>';
		}
	}
	
	$data .= '</ul>';

	return $data;
}

function br2nl($string) {
	$string = str_replace(array("\r\n", "\r", "\n"), '', $string);
	$string = str_replace('<br />', "\n", $string);

	return $string;
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Pheditor</title>
<style type="text/css">
body {
	margin: 0;
	padding: 0;
	color: #444;
}

a, a:visited, a:focus {
	color: #444;
	text-decoration: none;
}

a:hover {
	color: #000;
}

h1 {
	margin: 0;
	padding: 0;
	float: left;
}

h1 a {
	color: #444;
}

#top {
	padding: 10px;
	border-bottom: 1px dotted #ccc;
}

#sidebar {
	width: 19%;
	float: left;
}

#editor {
	width: 79%;
	float: right;
	padding: 10px;
	overflow-y: auto;
	white-space: pre-wrap;
	border-left: 1px dotted #ccc;
}

ul.menu {
	margin: 0;
	padding: 0;
	float: right;
}

ul.menu li {
	margin: 0;
	padding: 0 0 0 10px;
	float: right;
	list-style-type: none;
}

ul.files {
	margin: 10px 30px 0 30px;
	padding: 0;
}

ul.files li {
	padding-bottom: 5px;
	list-style-type: none;
}

ul.files li.dir:before { content: "+"; margin-right: 5px; }
ul.files li.file { margin-left: 15px; cursor: default; }
ul.files li.file.editable { list-style-type: disc; margin-left: 15px; }

</style>
<script type="text/javascript">
function id(id) {
	return document.getElementById(id);
}

function expandDir(element) {
	var ul = element.nextSibling;
	
	if (ul.style.display == "none")
		ul.style.display = "block";
	else
		ul.style.display = "none";
}

function openFile(element) {
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (xhttp.readyState == 4 && xhttp.status == 200) {
			var editor = id("editor");

			editor.innerHTML = xhttp.responseText;
			editor.setAttribute("data-file", element.getAttribute("data-file"));

			id("save").setAttribute("disabled", "");
			id("close").removeAttribute("disabled");
		}
	}
	xhttp.open("POST", "<?=$_SERVER['PHP_SELF']?>", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("action=open&file=" + encodeURIComponent(element.getAttribute("data-file")));
}

function saveFile() {
	var editor = id("editor");
	
	editor.innerHTML = editor.innerHTML.replace(/<br(\s*)\/*>/ig, "\n");
	
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (xhttp.readyState == 4 && xhttp.status == 200) {
			editor.innerHTML = xhttp.responseText;

			id("save").setAttribute("disabled", "");
		}
	}
	xhttp.open("POST", "<?=$_SERVER['PHP_SELF']?>", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("action=save&file=" + encodeURIComponent(editor.getAttribute("data-file")) + "&data=" + encodeURIComponent(editor.textContent));
}

function closeFile() {
	var editor = id("editor");

	editor.innerHTML = "";
	editor.setAttribute("data-file", "");

	id("save").setAttribute("disabled", "");
	id("close").setAttribute("disabled", "");
}

function checkStatus() {
	id("save").removeAttribute("disabled");
}

window.onload = function() {
	window.onresize = function() {
		id("sidebar").style.height = (window.innerHeight - id("top").clientHeight - 5) + "px";
		id("editor").style.height = (window.innerHeight - 25 - id("top").clientHeight) + "px";
	}

	window.onresize();
	id("save").setAttribute("disabled", "");
	id("close").setAttribute("disabled", "");
}
</script>
</head>
<body>

<div id="top">
	<h1><a href="http://github.com/hamidsamak/pheditor" target="_blank">Pheditor</a></h1>
	<ul class="menu">
		<li><button id="close" onclick="return closeFile();" disabled>Close</button></li>
		<li><button id="save" onclick="return saveFile();" disabled>Save</button></li>
	</ul>
	<div style="clear:both"></div>
</div>

<div>
	<div id="sidebar"><?=files(__DIR__)?></div>
	<div id="editor" data-file="" contenteditable="true" onkeyup="return checkStatus();"></div>
</div>

</body>
</html>