<html>
<head>
	<meta charset="utf-8">
	<title>Post Process Zanata</title>
	<link rel="stylesheet" href="style.css">
</head>
<?php
set_time_limit(0);
require_once("conf.php");

if (array_key_exists('poFile', $_FILES) && in_array(pathinfo($_FILES['poFile']['name'], PATHINFO_EXTENSION), array('po', 'pot')))
{
	header("Content-Type: application/octet-stream");
	header("Content-disposition: attachment; filename=main.po");
	$uploadFile = $GLOBALS['l10ntools']['uploadDir'] . basename($_FILES['poFile']['name']);

	if (move_uploaded_file($_FILES['poFile']['tmp_name'], $uploadFile))
	{
		require_once($GLOBALS['l10ntools']['pophpPath'] . 'POUtils.php');

		$file = new POFile($uploadFile);

		echo POUtils::getGettextHeader();

		foreach ($file->getEntries() as $entry)
		{
		    $newEntry = new POEntry(stripslashes($entry->getSource()), stripslashes($entry->getTarget()));
		    echo $newEntry;
		}

		unlink($uploadFile);
	}
	else
	{
		echo "File could not be uploaded";
	}
}

?>
<body>
<h1>Post-traitement Zanata</h1>
<div id=explanation>
Cette page applique un traitement sur le fichier PO exporté de Zanata afin
que ce dernier soit <strong>prêt à être importé dans CKTranslate</strong>.<br>
Il faut simplement envoyer le fichier exporté de Zanata via le formulaire
ci-dessous et le fichier traité sera automatiquement téléchargé.
</div><br><br>
<form enctype="multipart/form-data" action="" method="post">
  <label>Fichier PO exporté de Zanata :</label><br>
  <input name="poFile" type="file"><br><br>
  <input type="submit" value="Go">
</form>
</body>
</html>
