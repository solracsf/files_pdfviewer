/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
import { afterEach, describe, expect, it } from 'vitest'
import { getNextcloudRootUrl } from '../../src/utils/redirectIfNotIframe.js'

afterEach(() => {
	delete document.head.dataset.rooturl
})

describe('getNextcloudRootUrl', () => {
	it('uses the root URL provided by the viewer template', () => {
		document.head.dataset.rooturl = 'https://cloud.example.com/nextcloud/'

		expect(getNextcloudRootUrl()).toBe('https://cloud.example.com/nextcloud/')
	})

	it('falls back to the web server root when the template provides nothing', () => {
		expect(getNextcloudRootUrl()).toBe('/')
	})
})
