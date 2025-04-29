<?php

import('plugins.generic.texture.handlers.ServiceFileHandler');
import('plugins.generic.texture.handlers.ORKGHandlerJATSHeader');

class ORKGFileHandler extends ServiceFileHandler
{
	private const ORKG_EXPORT_URL = 'https://orkg.org/simcomp/thing/export';
	private const MIME_TYPE = 'text/xml';
	private string $fileType = 'xml';


	public function setAdditionalFileMetadata(array &$metadata): void
	{
		$metadata['mimetype'] = self::MIME_TYPE;
		$metadata['name'] = parent::getServiceFile() . '.' . $this->getFileTyle();

	}

	public function modifyContent(string &$content): void
	{
		import('plugins.generic.texture.handlers.ORKGHandlerJATSHeader');
		try {
			$processor = new ORKGHandlerJATSHeader($content);
			$content = $processor->process();
		} catch (Exception $e) {
			echo "Error: " . $e->getMessage();
		}



	}

	public function cleanServiceFilePath(&$serviceFile): string
	{

		$serviceFile = trim($serviceFile);

		if (empty($serviceFile)) {
			$this->handle404('Empty file path provided.');
		}

		if (filter_var($serviceFile, FILTER_VALIDATE_URL)) {
			$path = parse_url($serviceFile, PHP_URL_PATH);

			if ($path === null) {
				$this->handle404('Invalid URL path: ' . $serviceFile);
			}

			$parts = explode('/', trim($path, '/'));
			$lastPart = end($parts);

			if (preg_match('/^R\d{6}$/', $lastPart)) {  // Assuming 6-digit R numbers
				return $lastPart;
			}

			$this->handle404($serviceFile . ' is not a valid ORKG R-number.');
		}

		return $serviceFile;
	}

	protected function downloadServiceFile(): string
	{
		$queryParams = http_build_query([
			'format' => strtoupper($this->getFileTyle()),
			'thing_key' => parent::getServiceFile(),
			'thing_type' => 'REVIEW',
		]);

		$tempFile = parent::createTempFilePath();
		$downloadUrl = self::ORKG_EXPORT_URL . '?' . $queryParams;

		if (!$this->downloadFile($downloadUrl, $tempFile)) {
			throw new RuntimeException('Failed to download file from ORKG service');
		}

		return $tempFile;
	}

	function getFileTyle(): string
	{
		return $this->fileType;
	}



	protected function validateDownloadedFile(string $filePath): void
	{
		if (!file_exists($filePath) || filesize($filePath) === 0) {
			throw new RuntimeException('Downloaded file is invalid or empty');
		}

		if (@simplexml_load_file($filePath) === false) {
			throw new RuntimeException('Downloaded file is not valid XML');
		}
	}

	protected function validateServiceFileId(): void
	{
		$serviceFile = parent::getServiceFile();
		if (!$serviceFile || !preg_match('/^[a-zA-Z0-9_-]+$/', $serviceFile)) {
			throw new InvalidArgumentException('Invalid service file identifier');
		}
	}
}
