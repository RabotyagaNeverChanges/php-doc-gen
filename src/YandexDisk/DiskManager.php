<?php

namespace PhpDocGen\YandexDisk;

class DiskManager {
	private string $oAuthToken;
	private string $baseApiUrl;
	
	public function __construct(string $oAuthToken) {
		$this->oAuthToken = $oAuthToken;
		$this->baseApiUrl = "https://cloud-api.yandex.net/v1/disk";
	}
	
	protected function getDownloadLink(string $path): string {
		$ch = curl_init($this->baseApiUrl . "/resources/download?path=" . urlencode($path));
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_VERBOSE, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			"Authorization: OAuth $this->oAuthToken",
		]);
		$response = curl_exec($ch);
		curl_close($ch);
		$decodedResponse = json_decode($response, true);
		return $decodedResponse["href"];
	}
	
	public function download(string $remotePath, string $localPath) {
		$downloadLink = $this->getDownloadLink($remotePath);
		$sourceFile = fopen($downloadLink, 'rb');
		if ($sourceFile) {
			$localFile = fopen($localPath, 'wb');
			if ($localFile) {
				while (!feof($sourceFile)) {
					fwrite($localFile, fread($sourceFile, 1024 * 8), 1024 * 8);
				}
			}
		}
		if ($sourceFile) {
			fclose($sourceFile);
		}
		if ($localFile) {
			fclose($localFile);
		}
	}
	
	protected function getUploadLink(string $path): string {
		$ch = curl_init($this->baseApiUrl . "/resources/upload/?path=" . urlencode($path));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: OAuth $this->oAuthToken"));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		$response = curl_exec($ch);
		curl_close($ch);
		$decodedResponse = json_decode($response, true);
		return $decodedResponse["href"];
	}
	
	public function upload(string $loacalPath, string $remotePath) {
		$uploadLink = $this->getUploadLink($remotePath);
		$fileSize = filesize($loacalPath);
		$fileDescriptor = fopen($loacalPath, "r");
		$ch = curl_init($uploadLink);
		curl_setopt($ch, CURLOPT_INFILE, $fileDescriptor);
		curl_setopt($ch, CURLOPT_INFILESIZE, $fileSize);
		curl_setopt($ch, CURLOPT_PUT, true);
		curl_setopt($ch, CURLOPT_UPLOAD, true);
		
		curl_exec($ch);
		curl_close($ch);
	}
	
	public function publish(string $remotePath) {
		$ch = curl_init($this->baseApiUrl . "/resources/publish/?path=" . urlencode($remotePath));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: OAuth $this->oAuthToken"));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_PUT, true);
		curl_exec($ch);
	}
	
	public function getMetaInfo(string $remotePath): array {
		$ch = curl_init($this->baseApiUrl . "/resources/?path=" . urlencode($remotePath));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: OAuth $this->oAuthToken"));
		$response = curl_exec($ch);
		$decodedResponse = json_decode($response, true);
		return $decodedResponse;
	}
}