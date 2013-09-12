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
		// Declare filenames
		$output = $GLOBALS['l10ntools']['uploadDir'] . $destLocale . '.po';
		$tempXlf = $GLOBALS['l10ntools']['uploadDir'] . $destLocale . '.temp.xlf';

		// Convert the file
		chdir($GLOBALS['l10ntools']['l10nScriptsPath']);
		exec("php lb2xliff.php $uploadFile en-GB $tempXlf $destLocale");

		exec("xliff2po -i $tempXlf -o $output");
		unlink($tempXlf);

		exec("msguniq $output --no-location --no-wrap --sort-output -o $output");

		// Prompt file download
		header("Content-Type: application/octet-stream");
		header("Content-disposition: attachment; filename=" . pathinfo($output, PATHINFO_BASENAME));

		readfile($output);

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
	<title>XLF => PO</title>
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
<?php
foreach ($GLOBALS['l10ntools']['destLocales'] as $locale)
	echo "<option value=$locale>$locale</option>";
?>
  </select><br><br>
  <label>Fichier XLF :</label><br>
  <input name="xlfFile" type="file"><br><br>
  <input type="submit" value="Go">
</form>
</body>
</html>
