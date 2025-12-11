<?php

/**
 * @file plugins/generic/texture/classes/handlers/ServiceFileHandler.php
 *
 * Copyright (c) 2025 Simon Fraser University
 * Copyright (c) 2025 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class ServiceFileHandler
 *
 * @ingroup plugins_generic_texture
 *
 * @brief Texture editor plugin
 */

namespace APP\plugins\generic\texture\classes\handlers;

use APP\core\Application;
use APP\core\Request;
use APP\core\Services;
use APP\publication\Publication;
use APP\submission\Submission;
use BadFunctionCallException;
use CurlHandle;
use InvalidArgumentException;
use PKP\context\Context;
use PKP\db\DAORegistry;
use PKP\submissionFile\SubmissionFile;
use RuntimeException;

abstract class ServiceFileHandler
{
	private const GENRE_KEY = 'SUBMISSION';

	private Context $context;
	private Publication $publication;
	private Request $request;
	private string $serviceFile;
	private string $serviceFilelocale;
	private string $stageId;
	private Submission $submission;

	private array $requiredMetadataFields = ['mimetype', 'name'];

	public function __construct()
	{
		$this->request = Application::get()->getRequest();
		$this->context = $this->request->getJournal();
	}

	public function getRequest(): Request
	{
		return $this->request;
	}

	/**
	 * Creates a temporary file path.
	 */
	function createTempFilePath(): string
	{
		return sys_get_temp_dir() . '/' . bin2hex(random_bytes(8));
	}

	/**
	 * Processes submission and redirects.
	 */
	public function processSubmissionAndRedirect(): void
	{
		$this->validateServiceFileId();
		$tempFile = $this->downloadServiceFile();
		$this->validateDownloadedFile($tempFile);
		$this->createSubmissionFile($tempFile);
		$this->cleanUpAndRedirect($tempFile);
	}

	/**
	 * Validates service file.
	 * @throws InvalidArgumentException If service data is invalid
	 */
	abstract protected function validateServiceFileId(): void;

	abstract protected function downloadServiceFile(): string;

	/**
	 * Validates the downloaded file.
	 * @param string $filePath Path to the downloaded file
	 * @throws RuntimeException If file is invalid
	 */
	abstract protected function validateDownloadedFile(string $filePath): void;

	/**
	 * Creates a submission file from the temporary file.
	 * @param string $sourcePath Path to the temporary file
	 * @return SubmissionFile The created submission file
	 */
	private function createSubmissionFile(string $sourcePath): SubmissionFile
	{
		$targetPath = $this->getSubmissionFilePath();
		$fileId = Services::get('file')->add($sourcePath, $targetPath);

		$submissionFile = DAORegistry::getDAO('SubmissionFileDAO')->newDataObject();
		$submissionFile->setAllData($this->getFileMetadata($fileId));

		Services::get('submissionFile')->add($submissionFile, $this->request);

		return $submissionFile;
	}

	/**
	 * Gets the submission file path.
	 * @return string Path for the submission file
	 */
	function getSubmissionFilePath(): string
	{
		return Services::get('submissionFile')->getSubmissionDir(
				$this->context->getId(),
				$this->submission->getId()
			) . DIRECTORY_SEPARATOR . uniqid() . '.' . $this->getFileTyle();
	}

	abstract function getFileTyle(): string;

	/**
	 * Gets the file metadata for submission.
	 * @param int $fileId The file ID
	 * @return array File metadata
	 */
	function getFileMetadata(int $fileId): array
	{
		$metadata = $this->buildBaseMetadata($fileId);
		$this->setAdditionalFileMetadata($metadata);
		$this->validateRequiredMetadata($metadata);

		return $metadata;
	}

	/**
	 * Builds the base metadata structure.
	 */
	protected function buildBaseMetadata(int $fileId): array
	{
		$genre = DAORegistry::getDAO('GenreDAO')
			->getByKey(self::GENRE_KEY, $this->submission->getData('contextId'));

		$stage = $this->getStage();

		return [
			'fileId' => $fileId,
			'assocType' => Application::ASSOC_TYPE_SUBMISSION_FILE,
			'genreId' => $genre->getId(),
			'locale' => $this->getServiceFilelocale(),
			'submissionId' => $this->submission->getId(),
			'fileStage' => $stage,
		];
	}

	public function getServiceFilelocale(): string
	{
		return $this->serviceFilelocale;
	}

	public function setServiceFilelocale(string $serviceFilelocale): void
	{
		$this->serviceFilelocale = $serviceFilelocale;
	}

	abstract function setAdditionalFileMetadata(array &$metadata): void;

	abstract function modifyContent(string &$content): void;

	/**
	 * Validates that required metadata fields are not null.
	 * @throws BadFunctionCallException If any required field is missing.
	 */
	protected function validateRequiredMetadata(array $metadata): void
	{
		foreach ($this->getRequiredMetadataFields() as $field) {
			if (!isset($metadata[$field]) || $metadata[$field] === null) {
				$this->handle404('Extending Service method  setAdditionalFileMetadata does not add metadata:   ' . $field);
				throw new BadFunctionCallException($field . ' is null');
			}
		}
	}

	public function getRequiredMetadataFields(): array
	{
		return $this->requiredMetadataFields;
	}

	/**
	 * Cleans up temporary file and redirects to workflow.
	 * @param string $tempFilePath Path to the temporary file
	 */
	function cleanUpAndRedirect(string $tempFilePath): void
	{
		$this->cleanUpTempFile($tempFilePath);
		$this->redirectToWorkflow();
	}

	/**
	 * Deletes the temporary file if it exists.
	 * @param string $tempFilePath Path to the temporary file
	 */
	function cleanUpTempFile(string $tempFilePath): void
	{
		if (file_exists($tempFilePath)) {
			unlink($tempFilePath);
		}
	}

	/**
	 * Redirects to workflow page.
	 */
	function redirectToWorkflow(): void
	{
		$this->request->redirectUrl($this->request->getRouter()->url(
			$this->request,
			null,
			'workflow',
			'index',
			[$this->submission->getId(), $this->stageId]
		));
	}

	public function getContext(): Context
	{
		return $this->context;
	}

	public function getPublication(): Publication
	{
		return $this->publication;
	}

	public function setPublication(Publication $publication): void
	{
		$this->publication = $publication;
	}

	public function getServiceFile(): string
	{
		return $this->serviceFile;
	}

	public function setServiceFile(string $serviceFile): void
	{
		$cleanServiceFilePath = $this->cleanServiceFilePath($serviceFile);
		$this->serviceFile = $this->sanitizeFilename($cleanServiceFilePath);
	}

	abstract function cleanServiceFilePath(string &$serviceFile): string;

	public function getStageId(): string
	{
		return $this->stageId;
	}

	public function setStageId(string $stageId): self
	{
		$this->stageId = $stageId;
		return $this;
	}

	protected function sanitizeFilename(string $filename): string
	{
		$clean = preg_replace('/[\x00-\x1F\x7F\/\\\\]/', '', $filename);
		return $clean;
	}

	public function getSubmission(): Submission
	{
		return $this->submission;
	}

	public function setSubmission(Submission $submission): void
	{
		$this->submission = $submission;
	}

	/**
	 * @return int
	 */
	public function getStage(): int
	{
		$stage = null;
		switch ($this->getStageId()) {
			case 1:
				$stage = SubmissionFile::SUBMISSION_FILE_SUBMISSION;
				break;
			case 2:
				$stage = SubmissionFile::SUBMISSION_FILE_INTERNAL_REVIEW_FILE;
				break;
			case 3:
				$stage = SubmissionFile::SUBMISSION_FILE_INTERNAL_REVIEW_FILE;
				break;
			case 4:
				$stage = SubmissionFile::SUBMISSION_FILE_COPYEDIT;
				break;
			case 5:
				$stage = SubmissionFile::SUBMISSION_FILE_PRODUCTION_READY;
				break;
		}
		return $stage;
	}

	/**
	 * Downloads a file from service.
	 * @param string $url URL to download from
	 * @param string $savePath Path to save the downloaded file
	 * @return bool True if download was successful
	 * @throws RuntimeException If download fails
	 */
	protected function downloadFile(string $url, string $savePath): bool
	{
		$curlHandle = $this->initializeCurlSession($url);
		$fileContent = $this->executeCurlRequest($curlHandle);
		$this->validateHttpResponse($curlHandle, $url);
		$this->modifyContent($fileContent);
		$this->writeContentToFile($fileContent, $savePath);

		return true;
	}

	private function initializeCurlSession(string $url): CurlHandle
	{
		$ch = curl_init();
		curl_setopt_array($ch, [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_MAXREDIRS => 5,
			CURLOPT_TIMEOUT => 60,
			CURLOPT_SSL_VERIFYPEER => true,
			CURLOPT_FAILONERROR => true
		]);

		return $ch;
	}

	private function executeCurlRequest(CurlHandle $curlHandle): string
	{
		$data = curl_exec($curlHandle);

		if (curl_errno($curlHandle)) {
			$error = curl_error($curlHandle);
			$url = curl_getinfo($curlHandle, CURLINFO_EFFECTIVE_URL);
			curl_close($curlHandle);
			$this->handle404($url . ' not found');
			throw new RuntimeException("cURL error: {$error}");
		}

		return $data;
	}

	/**
	 * Handles 404 errors.
	 * @param string $message Error message
	 */
	function handle404(string $message): void
	{
		header('HTTP/1.0 404 Not Found');
		fatalError($message);
	}

	private function validateHttpResponse(CurlHandle $curlHandle, string $originalUrl): void
	{
		$httpStatus = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
		curl_close($curlHandle);

		if ($httpStatus !== 200) {
			throw new RuntimeException(
				"Failed to download file from {$originalUrl}. HTTP status: {$httpStatus}"
			);
		}
	}

	private function writeContentToFile(string $content, string $savePath): void
	{
		$bytesWritten = file_put_contents($savePath, $content, LOCK_EX);

		if ($bytesWritten === false) {
			throw new RuntimeException("Failed to write file to {$savePath}");
		}
	}
}
