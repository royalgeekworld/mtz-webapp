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
  '/build/'                                   => ['build-index','Building Pale Moon'],
  '/build/windows/'                           => ['build-windows', 'Building Pale Moon: Microsoft Windows'],
  '/build/linux/'                             => ['build-linux', 'Building Pale Moon: GNU Linux'],
  '/build/mac/'                               => ['build-mac', 'Building Pale Moon: Apple Macintosh'],
  '/build/sunos/'                             => ['build-sunos', 'Building Pale Moon: SunOS-based Systems'],

  // Add-ons
  '/addons/'                                  => ['addons-index', 'Add-ons: Overview'],
  '/addons/concepts/'                         => ['addons-concepts', 'Add-ons: Concepts', true],
  '/addons/extensions/'                       => ['addons-extensions', 'Add-ons: Extensions', true],
  '/addons/themes/'                           => ['addons-themes', 'Add-ons: Themes'],
  '/addons/themes/complete/'                  => ['addons-themes-complete', 'Add-ons: Complete Themes'],
  '/addons/themes/personas/'                  => ['addons-themes-personas', 'Add-ons: Personas (Lightweight Themes)'],
  '/addons/themes/pm-history/'                => ['addons-themes-pmhistory', 'Add-ons: Pale Moon Theme History'],
  '/addons/site/'                             => ['addons-site', 'Add-ons: Site'],

  // Misc docs that need a place to live
  '/docs/'                                    => ['docs-index', 'Docs', true],
  '/docs/primer/'                             => ['docs-primer', 'Get Started: The primer to creating the future!'],
  '/docs/bounty/'                             => ['docs-bounty', 'Development Bounty Program'],
  '/docs/syntax/'                             => ['docs-syntax', 'Site-Specific Syntax'],
  '/docs/profile-migration/'                  => ['docs-profile-migration', 'Profile Migration'],
);

// This constant array contains redirects (largely old wiki urls accounting for the fact that we add / to non-file.ext urls here)
const REDIRECTS = array(
  // Obsolete URLs since the Developer Site Started
  '/addons/resources/'                                      => '/addons/concepts/',
  '/addons/site/phoebus-code/'                              => '/docs/syntax/#phoebusCode',

  // Old Wiki URLS
  '/Main_Page/'                                             => '/',
  '/Developer_Guide/'                                       => '/',
  '/Developer_Guide:Build_Instructions/'                    => '/build/',
  '/Developer_Guide:Build_Instructions/Pale_Moon/'          => '/build/',
  '/Developer_Guide:Build_Instructions/Pale_Moon/Windows/'  => '/build/windows/',
  '/Developer_Guide:Build_Instructions/Pale_Moon/Linux/'    => '/build/linux/',
  '/Developer_Guide:Build_Instructions/Pale_Moon/macOS/'    => '/build/mac/',
  '/Add-ons/'                                               => '/addons/',
  '/Add-ons:Extensions/'                                    => '/addons/extensions/',
  '/Add-ons:Extensions/Toolkit/'                            => '/addons/extensions/',
  '/Add-ons:Extensions/Bootstrap/'                          => '/addons/extensions/',
  '/Add-ons:Extensions/Jetpack/'                            => '/addons/extensions/',
  '/Add-ons:Themes/'                                        => '/addons/themes/',
  '/Add-ons:Themes/Complete/'                               => '/addons/themes/complete/',
  '/Add-ons:Themes/Complete/Changes/'                       => '/addons/themes/pm-history/',
  '/Add-ons:Themes/Persona/'                                => '/addons/themes/personas/',
  '/Add-ons:Site/'                                          => '/addons/site/',
  '/Add-ons:Site/Submit/'                                   => '/addons/site/',
  '/Add-ons:Site/Manifest_Files/'                           => '/docs/syntax/#phoebusCode',
);

require_once(MODULES['content']);

// ====================================================================================================================

// == | Main | ========================================================================================================

// If the URL is known to us pass it to generateContent
if (array_key_exists($gaRuntime['requestPath'], CONTENT)) {
  generateContent($gaRuntime['requestPath']);
}

// This will handle redirects largely from the old wiki urls to the new urls
if (array_key_exists($gaRuntime['requestPath'], REDIRECTS)) {
  gfRedirect(REDIRECTS[$gaRuntime['requestPath']]);
}

// No idea what we should do so 404
gfHeader(404);

// ====================================================================================================================

?>
