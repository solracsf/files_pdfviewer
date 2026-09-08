/**
 * SPDX-FileCopyrightText: 2020 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
/**
 * The Nextcloud root URL, provided by templates/viewer.php: the viewer is
 * rendered as a blank page, so the router can not resolve it here.
 */
export function getNextcloudRootUrl() {
	return document.head.dataset.rooturl || '/'
}

/**
 * Send the browser back to Nextcloud when the viewer was opened on its own.
 */
export default function() {
	if (window.location !== window.parent.location) {
		return
	}

	window.location.href = getNextcloudRootUrl()
}
