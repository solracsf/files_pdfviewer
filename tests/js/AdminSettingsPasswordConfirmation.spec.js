/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
import { mount } from '@vue/test-utils'
import { afterEach, describe, expect, it, vi } from 'vitest'
import AdminSettings from '../../src/components/AdminSettings.vue'

vi.mock('@nextcloud/axios', () => ({ default: { get: vi.fn(), put: vi.fn() } }))
vi.mock('@nextcloud/dialogs', () => ({ showError: vi.fn() }))
vi.mock('@nextcloud/password-confirmation', () => ({ confirmPassword: vi.fn() }))

const axios = (await import('@nextcloud/axios')).default
const { showError } = await import('@nextcloud/dialogs')
const { confirmPassword } = await import('@nextcloud/password-confirmation')

afterEach(() => {
	vi.clearAllMocks()
})

// Drains the microtask queue, so the number of awaits does not have to track
// how many promises the component chains internally.
function flush() {
	return new Promise((resolve) => setTimeout(resolve))
}

describe('AdminSettings password confirmation', () => {
	const toggle = async () => {
		axios.get.mockResolvedValue({ data: { ocs: { data: { enableScripting: false } } } })
		const wrapper = mount(AdminSettings)
		await flush()

		await wrapper.find('input[type="checkbox"]').trigger('change')
		await flush()
	}

	it('asks for the password before saving', async () => {
		confirmPassword.mockResolvedValue()
		axios.put.mockResolvedValue({ data: { ocs: { data: { enableScripting: true } } } })

		await toggle()

		expect(confirmPassword.mock.invocationCallOrder[0])
			.toBeLessThan(axios.put.mock.invocationCallOrder[0])
	})

	it('does not save, and does not complain, when the confirmation is cancelled', async () => {
		confirmPassword.mockRejectedValue(new Error('cancelled'))

		await toggle()

		expect(confirmPassword).toHaveBeenCalled()
		expect(axios.put).not.toHaveBeenCalled()
		expect(showError).not.toHaveBeenCalled()
	})
})
