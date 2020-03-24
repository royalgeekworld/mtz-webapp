<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.

// This has to be defined using the function at runtime because it is based
// on a variable. However, constants defined with the language construct
// can use this constant by some strange voodoo. Keep an eye on this.
// NOTE: DOCUMENT_ROOT does NOT have a trailing slash.
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT']);

// Define basic constants for the software
const SOFTWARE_NAME       = 'Selene';
const SOFTWARE_VERSION    = '1.0.0a1';
const SOFTWARE_REPO       = 'https://github.com/MoonchildProductions/selene';
const DATASTORE_RELPATH   = '/datastore/';
const OBJ_RELPATH         = '/.obj/';
const BASE_RELPATH        = '/base/';
const SKIN_RELPATH        = '/skin/';
const COMPONENTS_RELPATH  = '/components/';
const DATABASES_RELPATH   = '/databases/';
const MODULES_RELPATH     = '/modules/';
const LIB_RELPATH         = '/libraries/';
const NEW_LINE            = "\n";

// Define components
const COMPONENTS = array(
  'site'            => ROOT_PATH . BASE_RELPATH . 'site.php',
  'special'         => ROOT_PATH . BASE_RELPATH . 'special.php'
);

// Define modules
const MODULES = null;

// Define databases
const DATABASES = null;

// Define libraries
const LIBRARIES = null;

/* Known Application IDs
 * Application IDs are normally in the form of a {GUID} or user@host ID.
 * Pale Moon (2):     {8de7fcbb-c55c-4fbe-bfc5-fc555c87dbc4}
 * Basilisk (4):      {Firefox}
 * Ambassador (8):    {4523665a-317f-4a66-9376-3763d1ad1978}
 * Borealis (16):     {a3210b97-8e8a-4737-9aa0-aa0e607640b9}
 * Interlink (32):    {Thunderbird}
 *
 * Firefox:           {ec8030f7-c20a-464f-9b0e-13a3a9e97384}
 * Thunderbird:       {3550f703-e582-4d05-9a08-453d09bdfdc6}
 * SeaMonkey:         {92650c4d-4b8e-4d2a-b7eb-24ecf4f6b63a}
 * Fennec (Android):  {aa3c5121-dab2-40e2-81ca-7ea25febc110}
 * Fennec (XUL):      {a23983c0-fd0e-11dc-95ff-0800200c9a66}
 * Sunbird:           {718e30fb-e89b-41dd-9da7-e25a45638b28}
 * Instantbird:       {33cb9019-c295-46dd-be21-8c4936574bee}
 * Adblock Browser:   {55aba3ac-94d3-41a8-9e25-5c21fe874539} */
const TOOLKIT_ID    = 'toolkit@mozilla.org';
const TOOLKIT_ALTID = 'toolkit@palemoon.org';
const TOOLKIT_BIT   = 1;

const LICENSES = array(
  'Apache-2.0'                => 'Apache License 2.0',
  'Apache-1.1'                => 'Apache License 1.1',
  'BSD-3-Clause'              => 'BSD 3-Clause',
  'BSD-2-Clause'              => 'BSD 2-Clause',
  'GPL-3.0'                   => 'GNU General Public License 3.0',
  'GPL-2.0'                   => 'GNU General Public License 2.0',
  'LGPL-3.0'                  => 'GNU Lesser General Public License 3.0',
  'LGPL-2.1'                  => 'GNU Lesser General Public License 2.1',
  'AGPL-3.0'                  => 'GNU Affero General Public License v3',
  'MIT'                       => 'MIT License',
  'MPL-2.0'                   => 'Mozilla Public License 2.0',
  'MPL-1.1'                   => 'Mozilla Public License 1.1',
  'Custom'                    => 'Custom License',
  'PD'                        => 'Public Domain',
  'COPYRIGHT'                 => ''
);

?>