<?php
	$loginPageTemplate = "
<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\"
	\"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"uk\" lang=\"uk\">
	<head>
		<meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\" />
		<title>\"Єврогазприлад\" - Вхід</title>
		<link type=\"text/css\" rel=\"stylesheet\" href=\"style.css\" />
	</head>
	<body>
		<div>
			<h1>Вхід</h1>
			<form method=\"post\" action=\"{$_SERVER["PHP_SELF"]}\" enctype=\"application/x-www-form-urlencoded\" accept-charset=\"UTF-8\">
				<p><label for=\"userName\">Логін:</label>
				<br />
				<input id=\"userName\" type=\"text\" title=\"Enter your name\" name=\"userName\" /></p>
				<p><label for=\"userPassword\">Пароль:</label>
				<br />
				<input id=\"userPassword\" type=\"password\" title=\"Enter your password\" name=\"userPassword\" /></p>
				<p><input type=\"submit\" name=\"Submit\" value=\"Увійти\" /></p>
			</form>
		</div>
	</body>
</html>";
?>