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
if (array_key_exists($gaRuntime['requestPath'], REDIRECTS)) {
  gfRedirect(REDIRECTS[$gaRuntime['requestPath']]);
}

$gvExtraSimpleTags  = EMPTY_ARRAY;
$gvExtraRegexTags   = EMPTY_ARRAY;

if ($gaRuntime['requestPath'] == '/docs/phoenix-extensions/') {
  // [fxaddon] (Firefox CAA)
  $gvFxForkedRegex        = "\[fxaddon fxslug=\"(.*)\" fxname=\"(.*)\" pmslug=\"(.*)\" pmname=\"(.*)\"\]";
  $gvFxForkedReplace      = '<tr>' .
                            '<td style="color: #00c019;">Forked</td>' .
                            '<td><a href="caa:addon/$1" target="_blank">$2</a></td>' .
                            '<td><a href="https://addons.palemoon.org/addon/$3/" target="_blank">$4</a></td>' .
                            '</tr>';
  $gvExtraRegexTags[$gvFxForkedRegex] = $gvFxForkedReplace;

  $gvFxBadRegex           = "\[fxaddon fxslug=\"(.*)\" fxname=\"(.*)\" fxreason=\"(.*)\"\]";
  $gvFxBadReplace         = '<tr>' .
                            '<td style="color: #BF0000;">Unforkable</td>' .
                            '<td><a href="caa:addon/$1" target="_blank">$2</a></td>' .
                            '<td style="color: #BF0000;">$3</td>' .
                            '</tr>';
  $gvExtraRegexTags[$gvFxBadRegex] = $gvFxBadReplace;

  $gvFxRegex              = "\[fxaddon fxslug=\"(.*)\" fxname=\"(.*)\"\]";
  $gvFxReplace            = '<tr>' .
                            '<td>Forkable</td>' .
                            '<td colspan="2"><a href="caa:addon/$1" target="_blank">$2</a></td>' .
                            '</tr>';
  $gvExtraRegexTags[$gvFxRegex] = $gvFxReplace;

  // [joaddon] (JustOff)
  $gvJustOffForkedRegex   = "\[joaddon joslug=\"(.*)\" joname=\"(.*)\" pmslug=\"(.*)\" pmname=\"(.*)\"\]";
  $gvJustOffForkedReplace = '<tr>' .
                            '<td style="color: #00c019;">Forked</td>' .
                            '<td><a href="https://github.com/JustOff/$1" target="_blank">$2</a></td>' .
                            '<td><a href="https://addons.palemoon.org/addon/$3/" target="_blank">$4</a></td>' .
                            '</tr>';
  $gvExtraRegexTags[$gvJustOffForkedRegex] = $gvJustOffForkedReplace;

  $gvJustOffRegex         = "\[joaddon joslug=\"(.*)\" joname=\"(.*)\"\]";
  $gvJustOffReplace       = '<tr>' .
                            '<td>Up for grabs!</td>' .
                            '<td colspan="2"><a href="https://github.com/JustOff/$1" target="_blank">$2</a></td>' .
                            '</tr>';
  $gvExtraRegexTags[$gvJustOffRegex] = $gvJustOffReplace;

  // [riaddon] Riiis
  $gvRiiisForkedRegex     = "\[riaddon rislug=\"(.*)\" riname=\"(.*)\" pmslug=\"(.*)\" pmname=\"(.*)\"\]";
  $gvRiiisForkedReplace   = '<tr>' .
                            '<td><small>$1</small></td>' .
                            '<td>$2</td>' .
                            '<td style="background-color: #eaf6eb !important;"><a href="https://addons.palemoon.org/addon/$3/" target="_blank">$4</a></td>' .
                            '</tr>';
  $gvExtraRegexTags[$gvRiiisForkedRegex] = $gvRiiisForkedReplace;

  $gvRiiisBadRegex        = "\[riaddon rislug=\"(.*)\" riname=\"(.*)\" rireason=\"(.*)\"\]";
  $gvRiiisBadReplace      = '<tr>' .
                            '<td><small>$1</small></td>' .
                            '<td>$2</td>' .
                            '<td style="background-color: #f6eaeb !important; border-spacing: 0px;"><small>$3</small></td>' .
                            '</tr>';
  $gvExtraRegexTags[$gvRiiisBadRegex] = $gvRiiisBadReplace;
}

if (!empty($gvExtraSimpleTags)) {
  $gmGenerateContent->extraSimpleTags = $gvExtraSimpleTags;
}

if (!empty($gvExtraRegexTags)) {
  $gmGenerateContent->extraRegexTags = $gvExtraRegexTags;
}

$gmGenerateContent->loadAndDisplay($gaRuntime['requestPath']);


// No idea what we should do so 404
gfHeader(404);

// ====================================================================================================================

?>
