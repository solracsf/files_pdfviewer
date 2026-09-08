/**
 * SPDX-FileCopyrightText: 2020 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
const fs = require('fs')
const path = require('path')
const AdmZip = require('adm-zip')
const axios = require('axios')
const cliProgress = require('cli-progress')
const npmPackageLock = require('./package-lock.json')

const pdfjsDir = path.resolve(__dirname, 'js', 'pdfjs')
const pdfjsDistDir = path.resolve(__dirname, 'node_modules', 'pdfjs-dist')

// The release archive and the pdfjs-dist package are built from the same
// sources, so the files they have in common have to be identical. npm checked
// pdfjs-dist against the integrity hash in package-lock.json, which is what
// makes comparing against it worth anything: the release archive itself is
// downloaded over plain HTTPS with nothing to check it against.
const verifiedDirectories = [
	['build', 'build'],
	['cmaps', 'web/cmaps'],
	['standard_fonts', 'web/standard_fonts'],
	['web/images', 'web/images'],
]

/**
 * @return {number} the number of files that matched
 */
function verifyAgainstPdfjsDist() {
	if (!fs.existsSync(pdfjsDistDir)) {
		throw new Error('"node_modules/pdfjs-dist" is missing, so the download can not be verified. Install the dependencies first.')
	}

	let verified = 0

	for (const [distDirectory, releaseDirectory] of verifiedDirectories) {
		const distPath = path.join(pdfjsDistDir, distDirectory)

		for (const entry of fs.readdirSync(distPath, { withFileTypes: true })) {
			const distFile = path.join(distPath, entry.name)
			const releaseFile = path.join(pdfjsDir, releaseDirectory, entry.name)

			// The release does not ship the minified builds.
			if (!entry.isFile() || !fs.existsSync(releaseFile)) {
				continue
			}

			if (!fs.readFileSync(distFile).equals(fs.readFileSync(releaseFile))) {
				throw new Error(`${releaseDirectory}/${entry.name} of the pdf.js release does not match pdfjs-dist`)
			}

			verified++
		}
	}

	return verified
}

// Fetching pdf.js build release
const PDFJSversion = npmPackageLock.packages['node_modules/pdfjs-dist'].version
console.info('Fetching pdfjs', PDFJSversion)

// Init progress
const pdfjsProgress = new cliProgress.SingleBar({}, cliProgress.Presets.shades_classic)
pdfjsProgress.start(100, 0)

axios.get(`https://github.com/mozilla/pdf.js/releases/download/v${PDFJSversion}/pdfjs-${PDFJSversion}-dist.zip`, {
	onDownloadProgress: ({loaded, total}) => {
		pdfjsProgress.update(loaded / total * 100)

		if (loaded === total) {
			pdfjsProgress.update(100)
			pdfjsProgress.stop()
			console.info('Done! \n')
		}
	},
	responseType: 'arraybuffer',
}).catch(err => {
	throw new Error(`Unable to download pdfjs dist: ${err.message}`)
}).then(response => {
	const zip = new AdmZip(response.data)
	zip.extractAllTo(pdfjsDir)

	console.info(`Verified ${verifyAgainstPdfjsDist()} files against pdfjs-dist ${PDFJSversion}`)
	// pdfjs-dist ships the library, not the viewer application, so these have
	// nothing to be compared against.
	console.info('Not covered by that check: web/viewer.mjs, web/viewer.css, web/locale')
}).catch(err => {
	pdfjsProgress.stop()
	console.error(err.message)
	process.exitCode = 1
})
