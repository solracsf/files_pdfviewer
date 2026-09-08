<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Files_PDFViewer\Controller;

use OCA\Files_PDFViewer\AppInfo\Application;
use OCA\Files_PDFViewer\Settings\AdminSettings;
use OCP\AppFramework\Http\Attribute\AuthorizedAdminSetting;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\OCSController;
use OCP\IConfig;
use OCP\IRequest;

class SettingsController extends OCSController {

	public function __construct(
		IRequest $request,
		private IConfig $config,
	) {
		parent::__construct(Application::APP_ID, $request);
	}

	/**
	 * Get current PDF viewer settings
	 */
	#[AuthorizedAdminSetting(settings: AdminSettings::class)]
	public function getSettings(): DataResponse {
		$enableScripting = $this->config->getAppValue(
			Application::APP_ID,
			'enable_scripting',
			'no'
		) === 'yes';

		return new DataResponse([
			'enableScripting' => $enableScripting,
		]);
	}

	/**
	 * Update PDF enable scripting setting
	 */
	#[AuthorizedAdminSetting(settings: AdminSettings::class)]
	public function setEnableScripting(bool $enableScripting): DataResponse {
		$this->config->setAppValue(
			Application::APP_ID,
			'enable_scripting',
			$enableScripting ? 'yes' : 'no'
		);

		return new DataResponse([
			'enableScripting' => $enableScripting,
		]);
	}
}
