<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.

// == | Main | ========================================================================================================

const SITE_NAME = 'Pale Moon - Developer Site';
const SKIN = 'palemoon';
$post = gfSuperVar('post', 'content');

if ($post) {
  require_once(MODULES['content']);
  generateContent('/content-test/', $post);
}
else {
  $content = '<form id="content" accept-charset="UTF-8" autocomplete="on" method="POST" enctype="multipart/form-data">' .
             '<textarea style="width: 1195px; resize: none;" name="content" rows="36" name="content"></textarea>' .
             '</br>' .
             '<button type="submit" value="Submit" style="float: right;">Test</button><br />' .
             '</form>';
  gfGenContent('Content Test', $content);
}


// ====================================================================================================================

?>