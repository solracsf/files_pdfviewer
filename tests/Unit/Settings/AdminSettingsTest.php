<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Files_PDFViewer\Tests\Unit\Settings;

use OCA\Files_PDFViewer\AppInfo\Application;
use OCA\Files_PDFViewer\Settings\AdminSettings;
use OCP\IL10N;
use OCP\Settings\IDelegatedSettings;
use Test\TestCase;

class AdminSettingsTest extends TestCase {

	/** @var AdminSettings */
	private $settings;

	protected function setUp(): void {
		$l10n = $this->createMock(IL10N::class);
		$l10n->method('t')->willReturnArgument(0);
		$this->settings = new AdminSettings($l10n);

		parent::setUp();
	}

	public function testCanBeDelegated(): void {
		$this->assertInstanceOf(IDelegatedSettings::class, $this->settings);
	}

	public function testDelegatesOnlyTheScriptingSetting(): void {
		$this->assertSame(
			[Application::APP_ID => ['/^enable_scripting$/']],
			$this->settings->getAuthorizedAppConfig(),
		);
	}

	public function testIsShownWithTheOtherDocumentSettings(): void {
		$this->assertSame('office', $this->settings->getSection());
	}
}
