<?php

include "storage.php";

$NUMPARTS = 3;
$TEMPDIR = "/home/tchack/public_html/tmp/";
$FRONTURL = "http://tchack.isidorechan.com/tmp/";
$DROPBOXKEY = "usjZMPSAPqUAAAAAAAAAAbREjdQblkVTN_g52u0Y9JtWag40N4ur46h_jRc_rOBV";

function GenerateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

function SplitAndEncrypt($file, $numparts, $filename, $password) {
	global $TEMPDIR;

	$result = array();
	if ($file["error"] > 0)
	{
		echo "Error: " . $file["error"] . "<br>";
	} else 	{
		$eachFileSize = ceil($file["size"]/$numparts);
		$extension = end(explode(".", $file["name"]));
		
		$filename = $filename."_";
		// echo "<br/>\neach file size: $eachFileSize";
		// echo "<br/>\nfilename: $filename";
		exec("split -a 1 -d ".$_FILES["file"]["tmp_name"]." -b $eachFileSize $TEMPDIR"."$filename");

		for ($i=0; $i<$numparts; $i++) {
			
			$filenameNoExtension = pathinfo($filename, PATHINFO_FILENAME);
			$rawName = $TEMPDIR.$filename.$i;
			$destinationName = $TEMPDIR.$filenameNoExtension."_".$i.".".$extension;
			rename($rawName, $destinationName);
			exec("mcrypt -k $password -a blowfish $destinationName");
			unlink($destinationName);
			
			array_push($result, $destinationName.".nc");
		}
	}
	
	return $result;
}

function UploadFiles($files, $dropboxkey) {

	if (count($files) != 3) { echo "unexpected number of files!"; exit; }

	$dropbox = new DropBox();
	$dropbox->authentication();
	$dropbox->uploadFile($files[0], basename($files[0]));
	$amazon = new Amazon();
	$amazon->authentication();
	$amazon->uploadFile($files[1], basename($files[1]));
	$googledrive = new GoogleDoc();
	$googledrive->authentication();
	$googledrive->uploadFile($files[2], basename($files[2]));

	/*
	for ($i = 0; $i < count($files); $i++ ){
		Dropbox::uploadFile($dropboxkey, $files[$i], "/".basename($files[$i]));
	}
*/
}

function DeleteFiles($files) {
	for ($i = 0; $i < count($files); $i++ ) {
		if (file_exists($files[$i])) {
			unlink($files[$i]);
		}
	}
}

function DownloadFiles($files, $dropboxkey) {

	if (count($files) != 3) { echo "unexpected number of files!"; exit; }

	$dropbox = new DropBox();
	$dropbox->authentication();
	$dropbox->downloadFile($files[0], basename($files[0]));
	$amazon = new Amazon();
	$amazon->authentication();
	$amazon->downloadFile($files[1], basename($files[1]));
	$googledrive = new GoogleDoc();
	$googledrive->authentication();
	$googledrive->downloadFile($files[2], basename($files[2]));
}

function GuessFileNames($name, $n) {
	global $TEMPDIR;

	$result = array();
	$filename = $TEMPDIR.pathinfo($name, PATHINFO_FILENAME);
	$extension = end(explode(".", $name));

	for ($i=0;$i<$n;$i++) {
		array_push($result, $filename."_".$i.".".$extension.".nc");
	}

	return $result;
}

function CheckFilesExist($files) {
	for ($i = 0; $i < count($files); $i++ ) {
		if (!file_exists($files[$i])) {
			return false;
		}
	}

	return true;
}

function DecryptAndJoin($files, $password, $destinationFile) {
	global $TEMPDIR;
	
	$joinString = "cat ";
	$extension = "";
	for ($i=0; $i<count($files); $i++) {
		// echo "<br/>mcrypt -k $password -d $files[$i]";
		if (!file_exists($files[$i])) {
			echo "<br/>file does not exist: $files[$i], check the file name!";
			return "FAIL";
		}
		if (file_get_contents($files[$i])=="") {
			echo "<br/>empty file returned: $files[$i], check the file name!";
			return "FAIL";
		}
		exec("mcrypt -k $password -d $files[$i]");
		$filename = $TEMPDIR.pathinfo($files[$i], PATHINFO_FILENAME);
		$extension = end(explode(".", $filename));
		$joinString .= " $filename ";
		if (!file_exists($filename)) {
			echo "<br/>file does not exist: $filename, decryption issue?";
			return "FAIL";
		}
	}

	$finalFile = $TEMPDIR.$destinationFile.GenerateRandomString(5);
	if ($extension != "") {
		$finalFile .= ".".$extension;
	}
	$joinString .= " > $finalFile";
	
	exec($joinString);
	
	// clean up
	for ($i=0; $i<count($files); $i++) {
		$filename = $TEMPDIR.pathinfo($files[$i], PATHINFO_FILENAME);
		if (file_exists($filename)) {
			unlink($filename);
		}
	}
	
	return $finalFile;
}

?>
