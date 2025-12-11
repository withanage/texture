<?php

/**
 * @file plugins/generic/texture/classes/handlers/ORKGHandlerJATSHeader.php
 *
 * Copyright (c) 2025 Simon Fraser University
 * Copyright (c) 2025 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class ORKGHandlerJATSHeader
 *
 * @ingroup plugins_generic_texture
 *
 * @brief Texture editor plugin
 */

namespace APP\plugins\generic\texture\classes\handlers;

use RuntimeException;
use SimpleXMLElement;

class ORKGHandlerJATSHeader
{
	private SimpleXMLElement $contentDOM;

	// Namespace constants
	private const XLINK_NAMESPACE = 'http://www.w3.org/1999/xlink';
	private const ALI_NAMESPACE = 'http://www.niso.org/schemas/ali/1.0';

	// global  variables
	private const PUBLISHER_NAME = 'TIB Open Publishing';
	private const LICENSE_URL = 'http://creativecommons.org/licenses/by/4.0/';
	private const LICENSE_IMAGE_URL = 'https://mirrors.creativecommons.org/presskit/buttons/88x31/svg/by-sa.svg';

	public function __construct(string $content)
	{
		$this->contentDOM = new SimpleXMLElement($content);
		$this->registerNamespaces();
	}

	private function registerNamespaces(): void
	{
		$this->contentDOM->registerXPathNamespace('xlink', self::XLINK_NAMESPACE);
		$this->contentDOM->registerXPathNamespace('ali', self::ALI_NAMESPACE);
	}

	public function process(): string
	{
		$this->addJournalMeta();
		$this->updateContribGroup();
		$this->modifyPermissions();
		$this->addBackElement();

		return $this->formatXML();
	}

	private function addJournalMeta(): void
	{
		$front = $this->contentDOM->front ?? null;
		if (!$front) {
			throw new RuntimeException("Invalid XML: Missing <front> element.");
		}

		$journalMeta = $front->addChild('journal-meta');
		$journalMeta->addChild('journal-id', '')->addAttribute('journal-id-type', 'publisher-id');
		$journalMeta->addChild('issn', 'X')->addAttribute('pub-type', 'epub');

		$publisher = $journalMeta->addChild('publisher');
		$publisher->addChild('publisher-name', self::PUBLISHER_NAME);
	}

	private function updateContribGroup(): void
	{
		$contribGroups = $this->contentDOM->xpath('//article-meta/contrib-group');
		if (empty($contribGroups)) {
			throw new RuntimeException("Invalid XML: Missing <contrib-group> element.");
		}

		$contribGroup = $contribGroups[0];
		$contribGroup->addChild('!--', 'X'); // Replace with meaningful logic if necessary
	}

	private function modifyPermissions(): void
	{
		$permissions = $this->contentDOM->xpath('//article-meta/permissions');
		if (empty($permissions)) {
			throw new RuntimeException("Invalid XML: Missing <permissions> element.");
		}

		$license = $permissions[0]->license ?? $permissions[0]->addChild('license');
		$license->addAttribute('license-type', 'open-access');
		$license->addAttribute('xlink:href', self::LICENSE_URL);
		$license->addAttribute('xml:lang', 'en');
	}

	private function addInlineGraphicToLicense(SimpleXMLElement $license): void
	{
		$licenseP = $license->{'license-p'} ?? $license->addChild('license-p');
		$inlineGraphic = $licenseP->addChild('inline-graphic');
		$inlineGraphic->addAttribute('xlink:href', self::LICENSE_IMAGE_URL);
	}

	private function addBackElement(): void
	{
		$backElements = $this->contentDOM->xpath('//back');
		if (count($backElements) < 1) {
			$this->contentDOM->addChild('back');
		}
	}

	private function formatXML(): string
	{
		$domDocument = dom_import_simplexml($this->contentDOM)->ownerDocument;
		$domDocument->formatOutput = true;
		return $domDocument->saveXML();
	}
}
