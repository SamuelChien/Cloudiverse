<?php
include ("lib.php");
// print_r($_POST);

print_r($_FILES);
$_POST["filename"] = $_FILES["file"]["name"];
if (isset($_POST["filename"])) {
	$_POST["password"] = "abcdef";
	$result = SplitAndEncrypt($_FILES["file"], 3, $_POST["filename"], $_POST["password"]);
	print_r($result);
	echo "<br/>Tried to upload..<br/>";
	UploadFiles($result, $DROPBOXKEY);
	DeleteFiles($result);
	
	exit;
	// print_r($result);
}

?>
<html>
<body>

<form action="upload.php" method="post"
enctype="multipart/form-data">
<label for="file">Filename:</label>
<input type="file" name="file" id="file"><br>
file name to store as (in cloud)<input type="text" name="filename"/><br>
password<input type="text" name="password"/><br>
<input type="submit" name="submit" value="Upload">
</form>

</body>
</html>
