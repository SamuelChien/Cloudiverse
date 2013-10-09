<?php

# Include the Dropbox SDK libraries
require_once "Dropbox/autoload.php";
use \Dropbox as dbx;

class Dropbox{ 
	public static function uploadFile($key, $filepath, $uploadName) {
		$dbxClient = new dbx\Client($key, "PHP-Example/1.0");
		$f = fopen($filepath, "rb");
		$result = $dbxClient->uploadFile($uploadName, dbx\WriteMode::add(), $f);
		fclose($f);
	}
	public static function downloadFile($key, $filepath, $downloadName) { 
		$dbxClient = new dbx\Client($key, "PHP-Example/1.0");
		$f = fopen($downloadName, "w+b");
		$fileMetadata = $dbxClient->getFile($filepath, $f);
		fclose($f);
	}
}

// Dropbox::uploadFile("usjZMPSAPqUAAAAAAAAAAbREjdQblkVTN_g52u0Y9JtWag40N4ur46h_jRc_rOBV", "working-draft.txt", "/hoohoo2.txt");
// Dropbox::downloadFile("usjZMPSAPqUAAAAAAAAAAbREjdQblkVTN_g52u0Y9JtWag40N4ur46h_jRc_rOBV", "/hoohoo.txt", "moo.txt");
