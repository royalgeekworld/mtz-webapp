<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.

// == | Setup | =======================================================================================================

gfImportModules('generateContent');

const SITE_NAME = 'Pale Moon - Developer Site';
const SKIN = 'palemoon';

// This constant array contains redirects (largely old wiki urls accounting for the fact that we add / to non-file.ext urls here)
const REDIRECTS = array();

// ====================================================================================================================

// == | Main | ========================================================================================================

// This will handle redirects largely from the old wiki urls to the new urls
if (array_key_exists($gaRuntime['qPath'], REDIRECTS)) {
  gfRedirect(REDIRECTS[$gaRuntime['qPath']]);
}

$gmGenerateContent->loadAndDisplay($gaRuntime['qPath']);

// No idea what we should do so 404
gfHeader(404);

// ====================================================================================================================

?>
