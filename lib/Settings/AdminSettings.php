<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Files_PDFViewer\Settings;

use OCA\Files_PDFViewer\AppInfo\Application;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IL10N;
use OCP\Settings\IDelegatedSettings;

class AdminSettings implements IDelegatedSettings {

	public function __construct(
		private IL10N $l10n,
	) {
	}

	#[\Override]
	public function getForm(): TemplateResponse {
		return new TemplateResponse(Application::APP_ID, 'admin');
	}

	#[\Override]
	public function getSection(): string {
		return 'office';
	}

	#[\Override]
	public function getPriority(): int {
		return 50;
	}

	#[\Override]
	public function getName(): ?string {
		return $this->l10n->t('PDF viewer');
	}

	#[\Override]
	public function getAuthorizedAppConfig(): array {
		return [Application::APP_ID => ['/^enable_scripting$/']];
	}
}
