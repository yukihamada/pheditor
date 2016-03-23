<?php

/*
 * Pheditor
 * PHP file editor
 * Hamid Samak
 * https://github.com/hamidsamak/pheditor
 * Release under MIT license
 */

define('PASSWORD', 'c7ad44cbad762a5da0a452f9e854fdc1e0e7a52a38015f23f3eab1d80b931dd472634dfac71cd34ebc35d16ab7fb8a90c81f975113d6c7538dc69dd8de9077ec');
define('EDITABLE_FORMATS', 'txt,php,htm,html,js,css,tpl,xml,md');
define('LOG_FILE', __DIR__ . DIRECTORY_SEPARATOR . '.phedlog');

if (file_exists(LOG_FILE)) {
	$log = unserialize(file_get_contents(LOG_FILE));

	if (isset($log[$_SERVER['REMOTE_ADDR']]) && $log[$_SERVER['REMOTE_ADDR']]['num'] > 3 && time() - $log[$_SERVER['REMOTE_ADDR']]['time'] < 86400)
		die('This IP address is blocked due to unsuccessful login attempts.');

	foreach ($log as $key => $value)
		if (time() - $value['time'] > 86400) {
			unset($log[$key]);

			$log_updated = true;
		}

	if (isset($log_updated))
		file_put_contents(LOG_FILE, serialize($log));
}

session_start();

if (isset($_SESSION['pheditor_admin']) === false || $_SESSION['pheditor_admin'] !== true) {
	if (isset($_POST['pheditor_password']))
		if (hash('sha512', $_POST['pheditor_password']) === PASSWORD) {
			$_SESSION['pheditor_admin'] = true;

			redirect();
		} else {
			$error = 'The entry password is not correct.';

			$log = file_exists(LOG_FILE) ? unserialize(file_get_contents(LOG_FILE)) : array();

			if (isset($log[$_SERVER['REMOTE_ADDR']]) === false)
				$log[$_SERVER['REMOTE_ADDR']] = array('num' => 0, 'time' => 0);

			$log[$_SERVER['REMOTE_ADDR']]['num'] += 1;
			$log[$_SERVER['REMOTE_ADDR']]['time'] = time();

			file_put_contents(LOG_FILE, serialize($log));
		}

	die('<title>Pheditor</title><form method="post"><div style="text-align:center"><h1><a href="http://github.com/hamidsamak/pheditor" target="_blank" title="PHP file editor" style="color:#444;text-decoration:none" tabindex="3">Pheditor</a></h1>' . (isset($error) ? '<p style="color:#dd0000">' . $error . '</p>' : null) . '<input name="pheditor_password" type="password" value="" placeholder="Password&hellip;" tabindex="1"><br><br><input type="submit" value="Login" tabindex="2"></div></form>');
}

if (isset($_GET['logout'])) {
	unset($_SESSION['pheditor_admin']);

	redirect();
}

if (isset($_POST['action'])) {
	switch ($_POST['action']) {
		case 'open':
			if (isset($_POST['file']) && file_exists(__DIR__ . DIRECTORY_SEPARATOR . $_POST['file']))
				echo br2nl(highlight_string(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $_POST['file']), true));
			break;

		case 'save':
			if (isset($_POST['file']) && isset($_POST['data'])) {
				file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . $_POST['file'], $_POST['data']);
				echo br2nl(highlight_string(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $_POST['file']), true));
			}
			break;

		case 'reload':
			echo files(__DIR__);
			break;

		case 'password':
			if (isset($_POST['password']) && empty($_POST['password']) === false) {
				$contents = file(__FILE__);

				foreach ($contents as $key => $line)
					if (strpos($line, 'define(\'PASSWORD\'') !== false) {
						$contents[$key] = "define('PASSWORD', '" . hash('sha512', $_POST['password']) . "');\n";

						break;
					}

				file_put_contents(__FILE__, implode($contents));

				echo 'Password changed successfully.';
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

function redirect($address = null) {
	if (empty($address))
		$address = $_SERVER['PHP_SELF'];

	header('Location: ' . $address);
	exit;
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
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
	padding: 0;
	margin: 10px;
	display: inline-block;
}

h1 a {
	color: #444;
}

#top {
	border-bottom: 1px dotted #ccc;
}

header {
	width: 20%;
	float: left;
}

nav {
	width: 80%;
	float: right;
}

#status {
	float: left;
	margin-top: 15px;
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
}

ul.menu li {
	margin: 0;
	float: right;
	list-style-type: none;
	padding: 10px 10px 0 0;
}

ul.files {
	padding: 0;
	margin: 10px 30px 0 30px;
}

ul.files li {
	padding-bottom: 5px;
	list-style-type: none;
}

ul.files li.dir:before { content: "+"; margin-right: 5px; }
ul.files li.file { cursor: default; margin-left: 15px; }
ul.files li.file.editable { list-style-type: disc; margin-left: 15px; }

@media screen and (max-width: 1000px) {
	#status {
		margin-left: 10px;
	}

	#sidebar {
		width: auto;
		float: none;
	}

	#editor {
		width: auto;
		float: none;
		border-left: 0;
		border-top: 1px dotted #ccc;
	}
}
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
	var file = element.getAttribute("data-file");

	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (xhttp.readyState == 4 && xhttp.status == 200) {
			var editor = id("editor");

			editor.innerHTML = xhttp.responseText;
			editor.setAttribute("data-file", file);

			id("save").setAttribute("disabled", "");
			id("close").removeAttribute("disabled");

			id("status").innerHTML = file;
		}
	}
	xhttp.open("POST", "<?=$_SERVER['PHP_SELF']?>", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("action=open&file=" + encodeURIComponent(file));
}

function saveFile() {
	var editor = id("editor");
	var file = editor.getAttribute("data-file");

	editor.innerHTML = editor.innerHTML.replace(/<br(\s*)\/*>/ig, "\n");

	if (file.length < 1)
		file = prompt("Please enter file name with full path", "new-file.php");

	if (file != null && file.length > 0) {
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (xhttp.readyState == 4 && xhttp.status == 200) {
				editor.innerHTML = xhttp.responseText;

				id("save").setAttribute("disabled", "");
				reloadFiles();
			}
		}
		xhttp.open("POST", "<?=$_SERVER['PHP_SELF']?>", true);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send("action=save&file=" + encodeURIComponent(file) + "&data=" + encodeURIComponent(editor.textContent));
	}
}

function reloadFiles() {
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (xhttp.readyState == 4 && xhttp.status == 200) {
			id("sidebar").innerHTML = xhttp.responseText;
		}
	}
	xhttp.open("POST", "<?=$_SERVER['PHP_SELF']?>", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("action=reload");
}

function closeFile() {
	var editor = id("editor");

	editor.innerHTML = "";
	editor.setAttribute("data-file", "");

	id("save").setAttribute("disabled", "");
	id("close").setAttribute("disabled", "");

	id("status").innerHTML = "";
}

function editorChange() {
	id("save").removeAttribute("disabled");
}

function changePassword() {
	var password = prompt("Please enter new password:");

	if (password != null && password.length > 0) {
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (xhttp.readyState == 4 && xhttp.status == 200)
				alert(xhttp.responseText);
		}
		xhttp.open("POST", "<?=$_SERVER['PHP_SELF']?>", true);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send("action=password&password=" + password);
	}
}

window.onload = function() {
	window.onresize = function() {
		if (window.innerWidth <= 1000) {
			id("sidebar").style.height = "";
			id("editor").style.height = "";
			id("editor").style.minHeight = "100px";
		} else {
			id("sidebar").style.height = (window.innerHeight - id("top").clientHeight - 5) + "px";
			id("editor").style.height = (window.innerHeight - 25 - id("top").clientHeight) + "px";
		}
	}

	window.onresize();

	id("save").setAttribute("disabled", "");
	id("close").setAttribute("disabled", "");
}

document.onkeydown = function(event) {
	if (event.ctrlKey == true)
		if (event.keyCode == 83) {
			event.preventDefault();

			id("save").click();
		} else if (event.keyCode == 87) {
			event.preventDefault();

			id("close").click();
		}
}
</script>
</head>
<body>

<div id="top">
	<header>
		<h1><a href="http://github.com/hamidsamak/pheditor" target="_blank" title="PHP file editor">Pheditor</a></h1><span><a href="javascript:void(0);" onclick="return changePassword();">[Password]</a> &nbsp; <a href="<?=$_SERVER['PHP_SELF']?>?logout=1">[Logout]</a></span>
	</header>

	<nav>
		<div id="status"></div>

		<ul class="menu">
			<li><button id="close" onclick="return closeFile();" disabled>Close</button></li>
			<li><button id="save" onclick="return saveFile();" disabled>Save</button></li>
		</ul>
	</nav>

	<div style="clear:both"></div>
</div>

<div>
	<div id="sidebar"><?=files(__DIR__)?></div>
	<div id="editor" data-file="" contenteditable="true" onkeyup="return editorChange();"></div>
</div>

</body>
</html>