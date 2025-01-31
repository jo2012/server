<?php

// AWS SDK PHP Client Library
require_once(KAutoloader::buildPath(KALTURA_ROOT_PATH, 'vendor', 'aws', 'aws-autoloader.php'));

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Aws\S3\Enum\CannedAcl;

/**
 * Extends the 'kFileTransferMgr' class & implements a file transfer manager using the Amazon S3 protocol with Authentication Version 4.
 * For additional comments please look at the 'kFileTransferMgr' class.
 *
 * @package infra
 * @subpackage Storage
 */
class s3Mgr extends kFileTransferMgr
{
	private $s3;
		
	protected $filesAcl = CannedAcl::PRIVATE_ACCESS;
	protected $s3Region = '';
	
	// instances of this class should be created usign the 'getInstance' of the 'kFileTransferMgr' class
	protected function __construct(array $options = null)
	{
		parent::__construct($options);
	
		if($options && isset($options['filesAcl']))
			$this->filesAcl = $options['filesAcl'];
			
		if($options && isset($options['s3Region']))
			$this->s3Region = $options['s3Region'];
			
		// do nothing
		$this->connection_id = 1; //SIMULATING!
	}



	public function getConnection()
	{
		return $this->connection_id;
	}

	/**********************************************************************/
	/* Implementation of abstract functions from class 'kFileTransferMgr' */
	/**********************************************************************/

	// sftp connect to server:port
	protected function doConnect($sftp_server, &$sftp_port)
	{
		return 1;
	}


	// login to an existing connection with given user/pass (ftp_passive_mode is irrelevant)
	protected function doLogin($sftp_user, $sftp_pass)
	{
		if(!class_exists('Aws\S3\S3Client')) {
			KalturaLog::err('Class Aws\S3\S3Client was not found!!');
			return false;
		}

		$this->s3 = S3Client::factory(
				array(
						'credentials' => array(
								'key'    => $sftp_user,
								'secret' => $sftp_pass,
						),
						'region' => $this->s3Region,
				)
		);
		
		$connectionSuccess = false;
		try
		{
			$buckets = $this->s3->listBuckets(); // just to check whether the connection is good, throws an exception in case of error
			$connectionSuccess = true;
		}
		catch ( Exception $e )
		{
			KalturaLog::err("Can't connect to S3: {$e->getMessage()}");
		}

		return $connectionSuccess;
	}


	// login using a public key
	protected function doLoginPubKey($user, $pubKeyFile, $privKeyFile, $passphrase = null)
	{
		return false;
	}


	// upload a file to the server (ftp_mode is irrelevant
	protected function doPutFile ($remote_file , $local_file)
	{
		list($bucket, $remote_file) = explode("/",ltrim($remote_file,"/"),2);
		KalturaLog::debug("remote_file: ".$remote_file);
		
		try
		{
			$res = $this->s3->putObject(array(
					'Bucket'       => $bucket,
					'Key'          => $remote_file,
					'SourceFile'   => $local_file,
					'ACL'          => $this->filesAcl,
			));

			KalturaLog::debug("File uploaded to Amazon, info: " . print_r($res, true));
			return true;
 		}
		catch ( Exception $e )
		{
			KalturaLog::err("error uploading file ".$local_file." s3 info ".print_r($info, true));
			return false;
		}
	}

	// download a file from the server (ftp_mode is irrelevant)
	protected function doGetFile ($remote_file, $local_file = null)
	{
		list($bucket, $remote_file) = explode("/",ltrim($remote_file,"/"),2);
		KalturaLog::debug("remote_file: ".$remote_file);

		$params = array(
				'Bucket' => $bucket,
				'Key'    => $remote_file,
			);		

		if($local_file)
		{
			$params['SaveAs'] = $local_file;
		}

		$response = $this->s3->getObject( $params );
		if($response && !$local_file)
			return $response['Body'];
			
		return $response;
	}

	// create a new directory
	protected function doMkDir ($remote_path)
	{
		return false;
	}

	// chmod the given remote file
	protected function doChmod ($remote_file, $chmod_code)
	{
		return false;
	}

	// return true/false according to existence of file on the server
	protected function doFileExists($remote_file)
	{
		list($bucket, $remote_file) = explode("/",ltrim($remote_file,"/"),2);
		if($this->isdirectory($remote_file)) {
			return true;
		}
		KalturaLog::debug("remote_file: ".$remote_file);

		$exists = $this->s3->doesObjectExist($bucket, $remote_file);
		return $exists;
	}

	private function isdirectory($file_name) {
		if(strpos($file_name,'.') === false) return TRUE;
		return false;
	}
	
	// return the current working directory
	protected function doPwd ()
	{
		return false;
	}

	// delete a file and return true/false according to success
	protected function doDelFile ($remote_file)
	{
		list($bucket, $remote_file) = explode("/",ltrim($remote_file,"/"),2);
		KalturaLog::debug("remote_file: ".$remote_file);

		$deleted = false;
		try
		{
			$this->s3->deleteObject(array(
					'Bucket' => $bucket,
					'Key' => $remote_file,
				));

			$deleted = true;
		}
		catch ( Exception $e )
		{
			KalturaLog::err("Couldn't delete file [$remote_file] from bucket [$bucket]: {$e->getMessage()}");
		}
		
		return $deleted;
	}

	// delete a directory and return true/false according to success
	protected function doDelDir ($remote_path)
	{
		return false;
	}

	protected function doList ($remote_path)
	{
		return false;
	}

	protected function doListFileObjects ($remoteDir)
	{
		return false;
	}

	protected function doFileSize($remote_file)
	{
		return false;
	}

	// execute the given command on the server
	private function execCommand($command_str)
	{
		return false;
	}
}
