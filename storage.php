<?php

# Include the Dropbox SDK libraries
require_once "Dropbox/autoload.php";
require_once 'google-api-php-client/src/Google_Client.php';
require_once 'google-api-php-client/src/contrib/Google_DriveService.php';
require_once 'aws/aws-autoloader.php';

use \Dropbox as dbx;
use Aws\S3\S3Client;

class Storage{
	function authentication	($filepath, $uploadName){}
	function uploadFile		($localPath, $serverPath){}
	function downloadFile	($localPath, $serverPath){}
}
    
class Amazon extends Storage {
    var $service;
        
    public function authentication(){
        $key="AKIAJLGXKUIEXEKTPMUQ";
        $secret="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";
        $this->service = S3Client::factory(array(
            'key'    => $key,
            'secret' => $secret
        ));
	}
        
    public function uploadFile($localPath, $serverPath) {
         $result = $this->service->putObject(array(
                  'Bucket'     => 'cloudiverse',
                  'Key'        => $serverPath,
                  'SourceFile' => $localPath

     ));
         $this->service->waitUntilObjectExists(array(
                  'Bucket' => 	'cloudiverse',
                  'Key'    => 	$serverPath
         ));
     }
     public function downloadFile($localPath, $serverPath) {
        $this->service->getObject(array(
                 'Bucket' => 'cloudiverse',
                 'Key'    => $serverPath,
                 'SaveAs' => $localPath
        ));
    }        
}        
    
    
class Dropbox extends Storage{
	var $service;
	
	public function authentication(){
		$accessKey = "usjZMPSAPqUAAAAAAAAAAbREjdQblkVTN_g52u0Y9JtWag40N4ur46h_jRc_rOBV";
		$this->service = new dbx\Client($accessKey, "PHP-Example/1.0");
	}
	
	public function uploadFile($localPath, $serverPath) {
		$f = fopen($localPath, "rb");
		$result = $this->service->uploadFile("/".$serverPath, dbx\WriteMode::add(), $f);
		fclose($f);
		return true;
	}
	public function downloadFile($localPath, $serverPath) { 
		$f = fopen($localPath, "w+b");
		$fileMetadata = $this->service->getFile("/".$serverPath, $f);
		fclose($f);
		return true;
	}
}

class GoogleDoc extends Storage{
	var $service;
	public function authentication(){
		$client = new Google_Client();
		$client->setClientId('237252421904.apps.googleusercontent.com');
		$client->setClientSecret('gwlBtZodv9el72CzQwzaeiAY');
		$client->setRedirectUri('http://cloudiverse.com/oauth2callback');
		$client->setScopes(array('https://www.googleapis.com/auth/drive'));
		$this->service = new Google_DriveService($client);
		
		$accessArray = array(
			"access_token"	=>"ya29.AHES6ZQvrwBiYBfHyo9bdECVbw1IJ8WFoYJasKwWsKp9i08",
			"token_type"	=>"Bearer",
			"expires_in"	=>3600,
			"refresh_token"	=>"1\/O2BFBc4XITt0slpATl0gkmOLw3kbCbix37MudFXUJK4",
			"created"		=>1378657879
		);
		$client->setAccessToken(json_encode($accessArray));
	}
	
	public function uploadFile($localPath, $serverPath) {
		$file = new Google_DriveFile();
		$file->setTitle($serverPath);
		$file->setDescription('A test document');
		$file->setMimeType('text/plain');

		$data = file_get_contents($localPath);
		$createdFile = $this->service->files->insert($file, array(
		      'data' => $data,
		      'mimeType' => 'text/plain',
		    ));
	}
	
	public function downloadFile($localPath, $serverPath) { 
		$fileContent = "";
		$fileContent = $this->downloadFileByFileItem($this->retrieveFilesIDByFileName($serverPath));
		$f = fopen($localPath, "w+b");
		fwrite($f, $fileContent);
		fclose($f);
		return true;
	}
	
	public function downloadFileByFileItem($fileID) {
		$file = $this->service->files->get($fileID);
	  	$downloadUrl = $file["downloadUrl"];
	  	if ($downloadUrl) {
	    	$request = new Google_HttpRequest($downloadUrl, 'GET', null, null);
	    	$httpRequest = Google_Client::$io->authenticatedRequest($request);
	    	if ($httpRequest->getResponseHttpCode() == 200) {
	      		return $httpRequest->getResponseBody();
			} else {
					// An error occurred.
				   	return null;
			}
		} else {
			// The file doesn't have any content stored on Drive.
				return null;
		}
	}
	
	public function retrieveFilesIDByFileName($name) {
		$result = array();
		$pageToken = NULL;

		try {
			$parameters = array();
			if ($pageToken) {
				$parameters['pageToken'] = $pageToken;
			}
			$fileHugeArray = $this->service->files->listFiles($parameters);
		}catch (Exception $e) {
			print "An error occurred: " . $e->getMessage();
			$pageToken = NULL;
		}
		
		$fileArray = $fileHugeArray['items'];
		$fileID = "null";
		foreach($fileArray as $file)
		{
			if($file["title"] == $name)
			{
				$fileID = $file["id"];
				return $fileID;
			}
		}
		
		return $fileID;
	}
}


//$server = new Amazon();
//$server->authentication();
//$server->uploadFile("working-draft.txt", "moi.txt");
//$server->downloadFile("amaooo.txt", "koodooo.txt");

