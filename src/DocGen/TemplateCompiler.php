<?php

namespace PhpDocGen\DocGen;

use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;
use PhpOffice\PhpWord\TemplateProcessor;

class TemplateCompiler {
	private string $templateDir;
	private string $documentDir;
	
	public function __construct(string $storage) {
		$this->templateDir = $storage . "/templates";
		$this->documentDir = $storage . "/documents";
	}
	
	private function generateDocumentName(string $filename, string $extension): string {
		$timestamp = date("Y-m-d__h-i-s");
		return $filename . "__" . $timestamp . "." . $extension;
	}
	
	public function compileTemplate(string $filename, array $context = []): string {
		$extensionDelimiterPos = strrpos($filename, ".");
		$extension = substr($filename, $extensionDelimiterPos + 1);
		$filename = substr($filename, 0, $extensionDelimiterPos);
		
		try {
			$templateProcessor = new TemplateProcessor(
				$this->templateDir . "/" . $filename . "." . $extension
			);
			$templateProcessor->setValues($context);
			$documentName = $this->generateDocumentName($filename, $extension);
			$templateProcessor->saveAs($this->documentDir . "/" . $documentName);
			return $documentName;
			
		} catch (CopyFileException  $e) {
			throw new \Exception("Given file is corrupted or doesn't exist");
		} catch (CreateTemporaryFileException $e) {
			throw new \Exception("Unable to create temporary file");
		}
	}
}