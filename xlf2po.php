<?php
set_time_limit(0);
require_once("conf.php");

if (array_key_exists('xlfFile', $_FILES)
&& (pathinfo($_FILES['xlfFile']['name'], PATHINFO_EXTENSION) === 'xlf')
&& (array_key_exists('destLocale', $_POST)))
{
	$destLocale = $_POST['destLocale'];
	$uploadFile = $GLOBALS['l10ntools']['uploadDir'] . basename($_FILES['xlfFile']['name']);

	if (move_uploaded_file($_FILES['xlfFile']['tmp_name'], $uploadFile))
	{
		require_once($GLOBALS['l10ntools']['pophpPath'] . 'POFile.php');

		$output = $GLOBALS['l10ntools']['uploadDir'] . $destLocale . '.po';
		$tempXlf = $GLOBALS['l10ntools']['uploadDir'] . $destLocale . '.temp.xlf';

		chdir($GLOBALS['l10ntools']['l10nScriptsPath']);
		exec("php lb2xliff.php $uploadFile en-GB $tempXlf $destLocale");

		system("xliff2po $tempXlf $output", $res);
		var_dump($res);
		unlink($tempXlf);

		exec("msguniq $output --no-location --no-wrap --sort-output -o $output");

		$file = new POFile($output);
		header("Content-Type: application/octet-stream");
		header("Content-disposition: attachment; filename=lang.po");

		foreach ($file->getEntries() as $entry)
		    echo $entry;

		//unlink($output);
		unlink($uploadFile);
	}
	else
	{
		echo "File could not be uploaded";
	}
}

?>
<html>
<head>
	<meta charset="utf-8">
	<title>XLIFF to PO Converter</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
<h1>XLF => PO</h1>
<div id=explanation>
Cette page convertit un fichier XLF au format PO.
</div><br><br>
<form enctype="multipart/form-data" action="" method="post">
	<label>Langue destination :</label><br>
  <select name="destLocale">
  	<option>fr-FR</option>
  	<option>de-DE</option>
  	<option>es-ES</option>
  	<option>it-IT</option>
  	<option>ja-JP</option>
  </select><br><br>
  <label>Fichier XLF :</label><br>
  <input name="xlfFile" type="file"><br><br>
  <input type="submit" value="Go">
</form>
</body>
</html>
