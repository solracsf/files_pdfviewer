/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
import { mount } from '@vue/test-utils'
import { describe, expect, it, vi } from 'vitest'
import PDFView from '../../src/views/PDFView.vue'

vi.mock('@nextcloud/initial-state', () => ({ loadState: () => false }))
vi.mock('@nextcloud/dialogs', () => ({ showError: vi.fn() }))

globalThis.OC = { appswebroots: {} }

describe('PDFView for a share that does not allow downloads', () => {
	const wrapper = mount(PDFView, {
		propsData: {
			fileid: 42,
			fileList: [{
				fileid: 42,
				shareAttributes: JSON.stringify([{ scope: 'permissions', key: 'download', value: false }]),
			}],
		},
	})

	it('explains why the file is not shown', () => {
		expect(wrapper.text()).toContain('the download needs to be allowed')
	})

	it('uses the empty content of the design system', () => {
		// "icon-error" has not existed in the server styles for a long time, so
		// the old markup rendered an empty box above the message.
		expect(wrapper.find('.empty-content').exists()).toBe(true)
		expect(wrapper.find('.icon-error').exists()).toBe(false)
	})
})
