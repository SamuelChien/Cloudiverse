<?php

$NUMPARTS = 3;
$TEMPDIR = "/home/tchack/public_html/tmp/";
$FRONTURL = "http://tchack.isidorechan.com/tmp/";

include ("lib.php");
exec('touch /home/tchack/public_html/cake/app/tmp/blahblah');
print_r($_POST);
if (isset($_POST["submit"]) && $_POST["submit"]=="Submit") {
if ($_FILES["file"]["error"] > 0)
  {
  echo "Error: " . $_FILES["file"]["error"] . "<br>";
  }
else
  {
  echo "Upload: " . $_FILES["file"]["name"] . "<br>";
  echo "Type: " . $_FILES["file"]["type"] . "<br>";
  echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
  echo "Stored in: " . $_FILES["file"]["tmp_name"];
	
	$eachFileSize = ceil($_FILES["file"]["size"]/$NUMPARTS);
	$extension = end(explode(".", $_FILES["file"]["name"]));
	$key = $_POST["password"];
	// $filename = generateRandomString(10)."_";
	$filename = $_POST["storename"]."_";
	echo "<br/>\neach file size: $eachFileSize";
	echo "<br/>\nfilename: $filename";
	exec("split -a 1 -d ".$_FILES["file"]["tmp_name"]." -b $eachFileSize $TEMPDIR"."$filename");
	for ($i=0; $i<$NUMPARTS; $i++) {
		$thispart = $TEMPDIR.$filename.$i;
		$destpart = $thispart.".".$extension;
		$desturl = $FRONTURL.$filename.$i.".".$extension;
		rename($thispart, $destpart);
		exec("mcrypt -k $key -a blowfish $destpart");
		echo "<br/>\n<a href='$desturl'>$desturl</a>";
	}
  }
}

?>
<html>
<body>

<form action="test2.php" method="post"
enctype="multipart/form-data">
<label for="file">Filename:</label>
<input type="file" name="file" id="file"><br>
name to store as<input type="text" name="storename"/><br>
password<input type="text" name="password"/><br>
<input type="submit" name="submit" value="Submit">
</form>

</body>
</html>
