<?php

/**
 * @file plugins/generic/texture/TexturePlugin.inc.php
 *
 * Copyright (c) 2003-2025 Simon Fraser University
 * Copyright (c) 2003-2025 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class TexturePlugin
 *
 * @ingroup plugins_generic_texture
 *
 * @brief Texture editor plugin
 */

namespace APP\plugins\generic\texture;

use APP\core\Application;
use APP\core\Request;
use APP\plugins\generic\texture\classes\handlers\TextureHandler;
use APP\template\TemplateManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request as IlluminateRequest;
use PKP\core\PKPBaseController;
use PKP\handler\APIHandler;
use PKP\linkAction\LinkAction;
use PKP\linkAction\request\AjaxModal;
use PKP\linkAction\request\OpenWindowAction;
use PKP\linkAction\request\PostAndRedirectAction;
use PKP\plugins\GenericPlugin;
use PKP\plugins\Hook;
use PKP\security\Role;
use PKP\submissionFile\SubmissionFile;

class TexturePlugin extends GenericPlugin
{
	public const string DAR_MANIFEST_FILE = 'manifest.xml';
	public const string DAR_MANUSCRIPT_FILE = 'manuscript.xml';
	public const string TEXTURE_DAR_FILE_TYPE = 'dar';
	public const string TEXTURE_ZIP_FILE_TYPE = 'zip';
	public const string TEXTURE_HTML_FILE_TYPE = 'html';
	public const array AUTHORIZED_ROLES = [
		Role::ROLE_ID_MANAGER,
		Role::ROLE_ID_SUB_EDITOR,
		Role::ROLE_ID_ASSISTANT,
		Role::ROLE_ID_REVIEWER,
		Role::ROLE_ID_AUTHOR
	];

	/**
	 * @copydoc Plugin::register()
	 */
	function register($category, $path, $mainContextId = null)
	{
		if (parent::register($category, $path, $mainContextId)) {
			if ($this->getEnabled()) {
				// Register callbacks.

				Hook::add('editorsubmissiondetailsfilesgridhandler::initfeatures', [$this, 'addActionsToFileGrid']);
				Hook::add('editorreviewfilesgridhandler::initfeatures', [$this, 'addActionsToFileGrid']);
				Hook::add('copyeditfilesgridhandler::initfeatures', [$this, 'addActionsToFileGrid']);
				Hook::add('productionreadyfilesgridhandler::initfeatures', [$this, 'addActionsToFileGrid']);
				Hook::add('LoadHandler', array($this, 'callbackLoadHandler'));
				Hook::add('TemplateManager::fetch', array($this, 'templateFetchCallback'));

				$this->_registerTemplateResource();

				$request = Application::get()->getRequest();
				$templateMgr = TemplateManager::getManager($request);

				// Hook::add('Dispatcher::dispatch', [$this, 'setupAPIHandler']);

				Hook::add('APIHandler::endpoints::submissions', $this->addRoute(...));

				$userRoleIds = array_map(fn($role) => $role->getId(), $request->getUser()?->getRoles($request->getContext()?->getId()));
				if (!empty($userRoleIds) && !empty(array_intersect($userRoleIds, TexturePlugin::AUTHORIZED_ROLES))) {
					$this->addResources($templateMgr, $request);
				}
			}
			return true;
		}
		return false;
	}

	function addActionsToFileGrid()
	{
		$request = Application::get()->getRequest();
		$dispatcher = $request->getDispatcher();
		$request->getRouter()->getHandler()->addAction(
			new LinkAction(
				'services_add_file',
				new AjaxModal(
					$dispatcher->url($request, Application::ROUTE_PAGE, null, 'texture', 'createServiceFileForm', null, $request->getUserVars()),
					__('plugins.generic.texture.createServiceFile.upload'),
					'modals_services_add_file'
				),
				__('plugins.generic.texture.createServiceFile.add_file'),
				''
			)
		);
	}

	/**
	 * Get texture editor URL.
	 */
	function getTextureUrl($request)
	{
		return $this->getPluginUrl($request) . '/texture';
	}

	/**
	 * Get plugin URL.
	 */
	function getPluginUrl($request)
	{
		return $request->getBaseUrl() . '/' . $this->getPluginPath();
	}

	/**
	 * Callback load handler.
	 * @see PKPPageRouter::route()
	 */
	public function callbackLoadHandler($hookName, $args)
	{
		$page = $args[0];
		$op = $args[1];

		switch ("$page/$op") {
			case 'texture/createGalley':
			case 'texture/editor':
			case 'texture/export':
			case 'texture/extract':
			case 'texture/json':
			case 'texture/save':
			case 'texture/createGalleyForm':
			case 'texture/createServiceFileForm':
			case 'texture/media':
				define('HANDLER_CLASS', 'TextureHandler');
				define('TEXTURE_PLUGIN_NAME', $this->getName());
				$args[2] = $this->getPluginPath() . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'TextureHandler.inc.php';
				break;
		}

		return false;
	}

	/**
	 * Adds additional links to submission files grid row.
	 */
	public function templateFetchCallback($hookName, $params)
	{
		$request = $this->getRequest();
		$router = $request->getRouter();
		$dispatcher = $router->getDispatcher();

		$templateMgr = $params[0];
		$resourceName = $params[1];
		if ($resourceName == 'controllers/grid/gridRow.tpl') {
			$row = $templateMgr->getTemplateVars('row');
			$data = $row->getData();
			if (is_array($data) && (isset($data['submissionFile']))) {
				$submissionFile = $data['submissionFile'];
				$fileExtension = strtolower($submissionFile->getData('mimetype'));

				// get stage ID
				$stageId = (int)$request->getUserVar('stageId');
				$fileStage = SubmissionFile::SUBMISSION_FILE_PRODUCTION_READY;

				if (strtolower($fileExtension) == 'text/xml') {
					$this->_editWithTextureAction($row, $dispatcher, $request, $submissionFile, $stageId);
					$this->_createGalleyAction($row, $dispatcher, $request, $submissionFile, $stageId, $fileStage);
					#$this->_exportAction($row, $dispatcher, $request, $submissionFile, $stageId, $fileStage);
				} elseif (strtolower($fileExtension) == TexturePlugin::TEXTURE_DAR_FILE_TYPE) {
					//	$this->_extractAction($row, $dispatcher, $request, $submissionFile, $stageId, $fileStage, TexturePlugin::TEXTURE_DAR_FILE_TYPE);
				} elseif (strtolower($fileExtension) == TexturePlugin::TEXTURE_ZIP_FILE_TYPE) {
					//	$this->_extractAction($row, $dispatcher, $request, $submissionFile, $stageId, $fileStage, TexturePlugin::TEXTURE_ZIP_FILE_TYPE);
				} elseif (strtolower($fileExtension) == TexturePlugin::TEXTURE_HTML_FILE_TYPE) {
					$this->_createGalleyAction($row, $dispatcher, $request, $submissionFile, $stageId, $fileStage);
				}
			}
		}
	}

	/**
	 * Export a dar archive.
	 */
	private function _exportAction($row, $dispatcher, $request, $submissionFile, int $stageId, int $fileStage): void
	{
		$row->addAction(new LinkAction(
			'texture_export',
			new OpenWindowAction(
				$dispatcher->url(
					$request,
					Application::ROUTE_PAGE,
					null,
					'texture',
					'export',
					null,
					array(
						'submissionId' => $submissionFile->getData('submissionId'),
						'submissionFileId' => $submissionFile->getData('id'),
						'stageId' => $stageId
					)
				)
			),
			__('plugins.generic.texture.links.exportDarArchive'),
			null
		));
	}

	/**
	 * Extract a dar archive.
	 */
	private function _extractAction($row, $dispatcher, $request, $submissionFile, int $stageId, int $fileStage, $zipType): void
	{
		$stageId = (int)$request->getUserVar('stageId');
		$zipLabel = ($zipType == TexturePlugin::TEXTURE_DAR_FILE_TYPE) ? 'plugins.generic.texture.links.extractDarArchive' : 'plugins.generic.texture.links.extractZipArchive';

		$actionArgs = array(
			'submissionId' => $submissionFile->getData('submissionId'),
			'submissionFileId' => $submissionFile->getData('id'),
			'stageId' => $stageId,
			'zipType' => $zipType
		);

		$path = $dispatcher->url($request, Application::ROUTE_PAGE, null, 'texture', 'extract', null, $actionArgs);
		$pathRedirect = $dispatcher->url($request, Application::ROUTE_PAGE, null, 'workflow', 'access', $actionArgs);
		$row->addAction(new LinkAction(
			'texture_import',
			new PostAndRedirectAction($path, $pathRedirect),
			__($zipLabel),
			null
		));
	}

	/**
	 * Adds edit with Texture action to files grid.
	 */
	private function _editWithTextureAction($row, $dispatcher, $request, $submissionFile, int $stageId): void
	{
		$row->addAction(new LinkAction(
			'texture_editor',
			new OpenWindowAction(
				$dispatcher->url(
					$request,
					Application::ROUTE_PAGE,
					null,
					'texture',
					'editor',
					null,
					array(
						'submissionId' => $submissionFile->getData('submissionId'),
						'submissionFileId' => $submissionFile->getData('id'),
						'stageId' => $stageId
					)
				)
			),
			__('plugins.generic.texture.links.editWithTexture'),
			null
		));
	}

	/**
	 * Adds create galley action to files grid.
	 */
	private function _createGalleyAction($row, $dispatcher, $request, $submissionFile, int $stageId, int $fileStage): void
	{
		$actionArgs = array(
			'submissionId' => $submissionFile->getData('submissionId'),
			'stageId' => $stageId,
			'fileStage' => $fileStage,
			'submissionFileId' => $submissionFile->getData('id')
		);
		$row->addAction(new LinkAction(
			'createGalleyForm',
			new AjaxModal(
				$dispatcher->url(
					$request,
					Application::ROUTE_PAGE,
					null,
					'texture',
					'createGalleyForm',
					null,
					$actionArgs
				),
				__('submission.layout.newGalley')
			),
			__('plugins.generic.texture.links.createGalley'),
			null
		));
	}

	/**
	 * This allows to add a route on the fly without defining an api controller.
	 * Hook: APIHandler::endpoints::submissions
	 * e.g. api/v1/submissions/{submissionId}/files/{submissionFileId}/texture/__action__
	 */
	public function addRoute(string $hookName, PKPBaseController $apiController, APIHandler $apiHandler): bool
	{
		$endPoints = ['createGalley', 'createGalleyForm', 'editor', 'export', 'extract', 'json', 'media'];

		$handler = new TextureHandler($this);

		foreach ($endPoints as $endPoint) {
			$apiHandler->addRoute(
				'POST',
				"texturePlugin/{submissionFileId}/{$endPoint}",
				fn(IlluminateRequest $request): JsonResponse => $handler->{$endPoint}($request),
				"texture.{$endPoint}",
				TexturePlugin::AUTHORIZED_ROLES
			);
		}

		return Hook::CONTINUE;
	}

	/**
	 * Add resources
	 */
	public function addResources(TemplateManager $templateMgr, Request $request): void
	{
		$templateMgr->addJavaScript(
			'TexturePluginJs',
			"{$request->getBaseUrl()}/{$this->getPluginPath()}/public/build/build.iife.js",
			[
				'inline' => false,
				'contexts' => ['backend'],
				'priority' => TemplateManager::STYLE_SEQUENCE_LAST
			]
		);
	}

	/**
	 * @copydoc Plugin::getDisplayName()
	 */
	function getDisplayName()
	{
		return __('plugins.generic.texture.displayName');
	}

	/**
	 * @copydoc Plugin::getDescription()
	 */
	function getDescription()
	{
		return __('plugins.generic.texture.description');
	}
}
