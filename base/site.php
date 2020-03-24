<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.

// == | Functions | ===================================================================================================

// ====================================================================================================================

// == | Main | ========================================================================================================

if ($gaRuntime['requestPath'] == '/') {
  $content = '<p>Welcome! This NEW Developer Site, interestingly enough, is actually in development. So none of its content is up yet.</p>' . 
             '<p>We expect to have at least the updated build documentation up in the next several hours. In any event, we applogize for any inconvenence.</p>';
  gfGenContent('Pale Moon Developer Site', $content);
}

gfHeader(404);

// ====================================================================================================================

?>