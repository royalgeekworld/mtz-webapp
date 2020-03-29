<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.

function generateContent($aURL, $aBypass = null) {
  if (!$aBypass && (CONTENT[$aURL][2] ?? null)) {
    gfError('That content has not been ported yet...');
  }

  $contentDir = ROOT_PATH . BASE_RELPATH . 'content/';
  $skinDir = ROOT_PATH . SKIN_RELPATH . SKIN . '/';

  $template = @file_get_contents($skinDir . 'site-template.xhtml');
  $stylesheet = @file_get_contents($skinDir . 'site-stylesheet.css');
  $stylesheetHLJS = @file_get_contents($skinDir . '../../jsmodules/highlight/styles/github.css');
  $content = @file_get_contents($contentDir . CONTENT[$aURL][0] . '.content');

  if (!$template || !$stylesheet || !$content) {
    gfError('Failed to generate content.');
  }

  $finalContent = $template;

  $pageSubsts = array(
    '{%SITE_STYLESHEET}'      => $stylesheet,
    '{%HIGHLIGHT_STYLESHEET}' => $stylesheetHLJS,
    '{%PAGE_CONTENT}'         => '<h1>' . ($aURL == '/' ? 'Pale Moon Developer Site' : CONTENT[$aURL][1]) . '</h1>' .
                                 NEW_LINE . parseSeleneCode($content),
    '{%BASE_PATH}'            => str_replace(ROOT_PATH, '', $skinDir),
    '{%CONTENT_PATH}'         => str_replace(ROOT_PATH, '', $contentDir),
    '{%SITE_NAME}'            => SITE_NAME,
    '{%PAGE_TITLE}'           => $aURL == '/' ? 'Front Page' : CONTENT[$aURL][1],
    '{%COPYRIGHT_YEAR}'       => date("Y"),
    '{%SOFTWARE_NAME}'        => SOFTWARE_NAME,
    '{%SOFTWARE_VERSION}'     => SOFTWARE_VERSION,
    '{%SOFTWARE_REPO}'        => SOFTWARE_REPO,
  );

  foreach ($pageSubsts as $_key => $_value) {
    $finalContent = str_replace($_key, $_value, $finalContent);
  }

  gfHeader('html');
  print($finalContent);
  exit();
}

// --------------------------------------------------------------------------------------------------------------------

function parseSeleneCode($aContent) {     
  $aContent = preg_replace('/\<\!\-\- (.*) \-\-\>\n/iU', '', $aContent);
  $aContent = htmlentities($aContent, ENT_XHTML);

  $seleneCodeSubsts = array(
    '[title]'                                             => '<h1>',
    '[/title]'                                            => '</h1>',
    '[header]'                                            => '<h2>',
    '[/header]'                                           => '</h2>',
    '[section]'                                           => '<h3>',
    '[/section]'                                          => '</h3>',
    '[/span]'                                             => '</span>',    
    '[b]'                                                 => '<strong>',
    '[/b]'                                                => '</strong>',
    '[i]'                                                 => '<em>',
    '[/i]'                                                => '</em>',
    '[u]'                                                 => '<u>',
    '[/u]'                                                => '</u>',
    '[/ul]'                                               => '</ul>',
    '[/ol]'                                               => '</ol>',
    '[/li]'                                               => '</li>',
    '[/p]'                                                => '</p>',
    '[/table]'                                            => '</table>',
    '[/th]'                                               => '</th>',
    '[/tr]'                                               => '</tr>',
    '[/td]'                                               => '</td>',
    '[/caption]'                                          => '</caption>',
    '[/col]'                                              => '</col>',
    '[/colgroup]'                                         => '</colgroup>',
    '[/thead]'                                            => '</thead>',
    '[/tbody]'                                            => '</tbody>',
    '[/tfoot]'                                            => '</tfoot>',
    '[br]'                                                => '<br />',
    '[break]'                                             => '<br />',
    '[dblbreak]'                                          => '<br /><br />',
    '[/code]'                                             => '</code></pre>',
    '[/codeline]'                                         => '</code></pre>',
  );

  foreach ($seleneCodeSubsts as $_key => $_value) {
    $aContent = str_replace($_key, $_value, $aContent);
  }

  $seleneCodeSuperRegex = 'span|p|ul|ol|li|hr|table|th|tr|td|hr|caption|col|colgroup|thead|tbody|tfoot';
  $seleneCodeRegex = array(
    '\[title=\"(.*)\"\]'                                  => '<h1>$1</h1>',
    '\[header=\"(.*)\"\]'                                 => '<h2>$1</h2>',
    '\[section=\"(.*)\"\]'                                => '<h3>$1</h3>',
    '\[link=(.*)\](.*)\[\/link\]'                         => '<a href="$1">$2</a>',
    '\[url=(.*)\](.*)\[\/url\]'                           => '<a href="$1" target="_blank">$2</a>',
    '\[url\](.*)\[\/url\]'                                => '<a href="$1" target="_blank">$1</a>',
    '\[img(.*)\](.*)\[\/img\]'                            => '<img src="$2"$1 />',
    '\[code=(.*)\]'                                       => '<pre><code class="$1">',
    '\[codeline=(.*)\]'                                   => '<pre style="display: inline;"><code class="$1" style="display: inline; background-color: transparent !important;">',
    '\[(' . $seleneCodeSuperRegex . ')(.*)\]'             => '<$1$2>',
  );

  foreach ($seleneCodeRegex as $_key => $_value) {
    $aContent = preg_replace('/' . $_key . '/iU', $_value, $aContent);
  }

  return $aContent;
}

?>