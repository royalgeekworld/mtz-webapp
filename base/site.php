<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.

// == | Setup | =======================================================================================================

const SITE_NAME = 'Pale Moon - Developer Site';
const SKIN = 'palemoon';

// This array constant holds the site map (for lack of a better term)
// Do mind the commas.
// Syntax: '/url/' => ['content-filename-without-extension', 'Page Title/Main Heading']
// The third index is a temporary flag to indicate that content should be there but I haven't ported it yet....
const CONTENT = array(
  // Root Index
  '/'                                         => ['root-index', 'Welcome to the Pale Moon Developer Site'],

  // Build Documentation
  '/build/'                                   => ['build-index','Building Pale Moon', true],
  '/build/windows/'                           => ['build-windows', 'Building Pale Moon: Windows'],
  '/build/linux/'                             => ['build-linux', 'Building Pale Moon: Linux', true],
  '/build/mac/'                               => ['build-mac', 'Building Pale Moon: MacOS X', true],
  '/build/sunos/'                             => ['build-sunos', 'Building Pale Moon: Solaris/Illumos', true],

  // Add-ons
  '/addons/'                                  => ['addons-index', 'Add-ons', true],
  '/addons/resources/'                        => ['addons-resources', 'Add-ons: Resources', true],
  '/addons/resources/install-manifest/'       => ['addons-resources-install-manifest', 'Add-ons: Install Manifest', true],
  '/addons/extensions/'                       => ['addons-extensions', 'Add-ons: Extensions', true],
  '/addons/extensions/jetpack/'               => ['addons-extensions-jetpack', 'Add-ons: Jetpack Style Extensions', true],
  '/addons/themes/'                           => ['addons-themes', 'Add-ons: Themes', true],
  '/addons/themes/complete/'                  => ['addons-themes-complete', 'Add-ons: Complete Themes', true],
  '/addons/themes/personas/'                  => ['addons-themes-personas', 'Add-ons: Personas (Lightweight Themes),', true],
  '/addons/themes/pm-history/'                => ['addons-themes-pmhistory', 'Add-ons: Pale Moon Theme History', true],

  // Add-ons Site
  '/addons-site/'                             => ['addons-site', 'Add-ons Site', true],
  '/addons-site/submit/'                      => ['addons-site-submit', 'Add-ons Site: Submission', true],
  '/addons-site/phoebus-code/'                => ['addons-site-phoebus-code', 'Add-ons Site: Phoebus Code Syntax', true],
);

// This constant array contains old wiki URLs (accounting for the fact that we add / to non-file.ext urls here)
const WIKI_URLS = array(
  '/Main_Page/' => '/',
  '/Developer_Guide/' => '/',
  '/Developer_Guide:Build_Instructions/'                    => '/build/',
  '/Developer_Guide:Build_Instructions/Pale_Moon/'          => '/build/',
  '/Developer_Guide:Build_Instructions/Pale_Moon/Windows/'  => '/build/windows/',
  '/Developer_Guide:Build_Instructions/Pale_Moon/Linux/'    => '/build/linux/',
  '/Developer_Guide:Build_Instructions/Pale_Moon/macOS/'    => '/build/mac/',
  '/Add-ons/'                                               => '/addons/',
  '/Add-ons:Extensions/'                                    => '/addons/extensions/',
  '/Add-ons:Extensions/Toolkit/'                            => '/addons/extensions/',
  '/Add-ons:Extensions/Bootstrap/'                          => '/addons/extensions/',
  '/Add-ons:Extensions/Jetpack/'                            => '/addons/extensions/jetpack/',
  '/Add-ons:Themes/'                                        => '/addons/themes/',
  '/Add-ons:Themes/Complete/'                               => '/addons/themes/complete/',
  '/Add-ons:Themes/Complete/Changes/'                       => '/addons/themes/pm-history/',
  '/Add-ons:Themes/Persona/'                                => '/addons/themes/personas/',
  '/Add-ons:Site/'                                          => '/addons-site/',
  '/Add-ons:Site/Submit/'                                   => '/addons-site/submit/',
  '/Add-ons:Site/Manifest_Files/'                           => '/addons-site/phoebus-code/',
  
);

$gaRuntime['requestBypass'] = gfSuperVar('get', 'bypass');

// ====================================================================================================================

// == | Functions | ===================================================================================================

function generateContent($aURL, $aBypass = null) {
  if (!$aBypass && (CONTENT[$aURL][2] ?? null)) {
    gfError('That content has not been ported yet...');
  }

  $contentDir = ROOT_PATH . BASE_RELPATH . 'content/';
  $skinDir = ROOT_PATH . SKIN_RELPATH . SKIN . '/';

  $template = @file_get_contents($skinDir . 'site-template.xhtml');
  $stylesheet = @file_get_contents($skinDir . 'site-stylesheet.css');
  $content = @file_get_contents($contentDir . CONTENT[$aURL][0] . '.content');

  if (!$template || !$stylesheet || !$content) {
    gfError('Failed to generate content.');
  }

  $finalContent = $template;

  $pageSubsts = array(
    '{%SITE_STYLESHEET}'      => $stylesheet,
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

// ====================================================================================================================

// == | Main | ========================================================================================================

// If the URL is known to us pass it to generateContent
if (array_key_exists($gaRuntime['requestPath'], CONTENT)) {
  generateContent($gaRuntime['requestPath'], $gaRuntime['requestBypass']);
}

// This will handle translation from the wiki urls to the new urls
if (array_key_exists($gaRuntime['requestPath'], WIKI_URLS)) {
  gfRedirect(WIKI_URLS[$gaRuntime['requestPath']]);
}

// No idea what we should do so 404
gfHeader(404);

// ====================================================================================================================

?>