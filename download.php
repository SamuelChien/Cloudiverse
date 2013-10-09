<?php

include ("lib.php");
if (isset($_GET["filename"])) {
	$_GET["password"] = "abcdef";
	$filenames = GuessFileNames($_GET["filename"], 3);
	DownloadFiles($filenames, $DROPBOXKEY);
	$result = DecryptAndJoin($filenames, $_GET["password"], $_GET["filename"]);

	$result = str_replace($TEMPDIR, $FRONTURL, $result);

	if ($result != "FAIL") {
		echo $result;
	} else { echo "<br/>failed to retrieve file!"; }
	DeleteFiles($filenames);
	exit;

	// print_r($result);
}

?>
<html>
<body>

<form action="download.php" method="post"
enctype="multipart/form-data">
<label for="file">Filename:</label>
file name to retrieve (in cloud)<input type="text" name="filename"/><br>
password<input type="text" name="password"/><br>
<input type="submit" name="submit" value="Download">
</form>

</body>
</html>
