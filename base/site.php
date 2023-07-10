<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.

// == | Setup | =======================================================================================================

gfImportModules('generateContent');

const SITE_NAME = 'Metropolis-based WebApp';
const SKIN = 'mtz-webapp';

// ====================================================================================================================

// == | Main | ========================================================================================================

$gmGenerateContent->loadAndDisplay($gaRuntime['qPath']);

// No idea what we should do so 404
gfHeader(404);

// ====================================================================================================================

?>
