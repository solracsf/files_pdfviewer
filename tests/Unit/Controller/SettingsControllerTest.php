<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Files_PDFViewer\Tests\Unit\Controller;

use OCA\Files_PDFViewer\AppInfo\Application;
use OCA\Files_PDFViewer\Controller\SettingsController;
use OCA\Files_PDFViewer\Settings\AdminSettings;
use OCP\AppFramework\Http\Attribute\AuthorizedAdminSetting;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\IConfig;
use OCP\IRequest;
use ReflectionMethod;
use Test\TestCase;

class SettingsControllerTest extends TestCase {

	private const ENDPOINTS = ['getSettings', 'setEnableScripting'];

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

	public function testGetSettings(): void {
		$this->config->method('getAppValue')
			->with(Application::APP_ID, 'enable_scripting', 'no')
			->willReturn('yes');

		$this->assertSame(['enableScripting' => true], $this->controller->getSettings()->getData());
	}

	public function testSetEnableScripting(): void {
		$this->config->expects($this->once())
			->method('setAppValue')
			->with(Application::APP_ID, 'enable_scripting', 'yes');

		$this->assertSame(['enableScripting' => true], $this->controller->setEnableScripting(true)->getData());
	}

	public function testEndpointsAreReachableByDelegatedAdmins(): void {
		foreach (self::ENDPOINTS as $endpoint) {
			$attributes = (new ReflectionMethod(SettingsController::class, $endpoint))
				->getAttributes(AuthorizedAdminSetting::class);

			$this->assertCount(1, $attributes, $endpoint);
			$this->assertSame(AdminSettings::class, $attributes[0]->newInstance()->getSettings(), $endpoint);
		}
	}

	public function testEndpointsAreNotReachableByRegularAccounts(): void {
		foreach (self::ENDPOINTS as $endpoint) {
			$attributes = (new ReflectionMethod(SettingsController::class, $endpoint))
				->getAttributes(NoAdminRequired::class);

			$this->assertCount(0, $attributes, $endpoint);
		}
	}
}
