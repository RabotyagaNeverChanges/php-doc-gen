<?php

namespace PhpDocGen\YandexDisk;

class DiskManager {
	private string $oAuthToken;
	
	public function __construct(string $oAuthToken) {
		$this->oAuthToken = $oAuthToken;
	}
	
	public function download(string $remotePath, string $localPath) {
	
	}
	
	protected function getUploadLink() {
	
	}
	
	public function upload(string $loacalPath, string $remotePath) {
	
	}
	
	public function publish(string $remotePath) {
		
	}
}