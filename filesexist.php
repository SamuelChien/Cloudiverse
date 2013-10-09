<?php

include ("lib.php");

if (isset($_GET) && isset($_GET["filename"])) {

	$filenames = GuessFileNames($_GET["filename"], 3);

	if (CheckFilesExist($filenames)) {
		echo "TRUE";
	} else {
		echo "FALSE";
	}
}
