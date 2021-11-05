<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.

// == | Setup | =======================================================================================================

gfImportModules('generateContent');

const SITE_NAME = 'Pale Moon - Developer Site';
const SKIN = 'palemoon';

// This constant array contains redirects (largely old wiki urls accounting for the fact that we add / to non-file.ext urls here)
const REDIRECTS = array(
  // Obsolete URLs since the Developer Site Started
  '/addons/resources/'                                      => '/addons/concepts/',
  '/addons/site/phoebus-code/'                              => '/docs/syntax/#phoebusCode',
  '/build/mac/'                                             => '/build/',
  '/docs/releng-guidelines/'                                => '/docs/release-engineering/',

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
