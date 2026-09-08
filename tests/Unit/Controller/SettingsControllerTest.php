<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Files_PDFViewer\Tests\Unit\Controller;

use OCA\Files_PDFViewer\AppInfo\Application;
use OCA\Files_PDFViewer\Controller\SettingsController;
use OCP\AppFramework\Http\Attribute\PasswordConfirmationRequired;
use OCP\IConfig;
use OCP\IRequest;
use ReflectionMethod;
use Test\TestCase;

class SettingsControllerTest extends TestCase {

	/** @var IConfig */
	private $config;

	/** @var SettingsController */
	private $controller;

	protected function setUp(): void {
		$this->config = $this->createMock(IConfig::class);
		$this->controller = new SettingsController(
			$this->createMock(IRequest::class),
			$this->config,
		);

		parent::setUp();
	}

	public function testSetEnableScripting(): void {
		$this->config->expects($this->once())
			->method('setAppValue')
			->with(Application::APP_ID, 'enable_scripting', 'yes');

		$this->assertSame(['enableScripting' => true], $this->controller->setEnableScripting(true)->getData());
	}

	public function testTurningScriptingOnNeedsAConfirmedPassword(): void {
		$attributes = (new ReflectionMethod(SettingsController::class, 'setEnableScripting'))
			->getAttributes(PasswordConfirmationRequired::class);

		$this->assertCount(1, $attributes);
	}

	public function testReadingTheSettingsDoesNotNeedAConfirmedPassword(): void {
		$attributes = (new ReflectionMethod(SettingsController::class, 'getSettings'))
			->getAttributes(PasswordConfirmationRequired::class);

		$this->assertCount(0, $attributes);
	}
}
