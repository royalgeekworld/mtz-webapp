<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.

// == | Setup | =======================================================================================================

error_reporting(E_ALL);
ini_set("display_errors", "on");

// This has to be defined using the function at runtime because it is based
// on a variable. However, constants defined with the language construct
// can use this constant by some strange voodoo. Keep an eye on this.
// NOTE: DOCUMENT_ROOT does NOT have a trailing slash.
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT']);

const NEW_LINE              = "\n";
const EMPTY_STRING          = "";
const EMPTY_ARRAY           = [];
const SPACE                 = " ";
const DOT                   = ".";
const SLASH                 = "/";
const DASH                  = "-";
const WILDCARD              = "*";

// Define basic constants for the software
const SOFTWARE_NAME       = 'Selene';
const SOFTWARE_VERSION    = '1.0.0a1';
const SOFTWARE_REPO       = 'https://repo.palemoon.org/MoonchildProductions/selene';
const DATASTORE_RELPATH   = '/datastore/';
const OBJ_RELPATH         = '/.obj/';
const BASE_RELPATH        = '/base/';
const SKIN_RELPATH        = '/skin/';
const COMPONENTS_RELPATH  = '/components/';
const MODULES_RELPATH     = '/modules/';
const LIB_RELPATH         = '/libraries/';

// Define components
const COMPONENTS = array(
  'site'            => ROOT_PATH . BASE_RELPATH . 'site.php',
  'special'         => ROOT_PATH . BASE_RELPATH . 'special.php'
);

// Define modules
const MODULES = array(
  'content'         => ROOT_PATH . MODULES_RELPATH . 'moduleContent.php'
);

// Define libraries
const LIBRARIES = null;

// ====================================================================================================================

// == | Global Functions | ============================================================================================

/**********************************************************************************************************************
* Basic Content Generation using the Special Component's Template
***********************************************************************************************************************/
function gfGenContent($aTitle, $aContent, $aTextBox = null, $aList = null, $aError = null) {
  $templateHead = @file_get_contents('./skin/special/template-header.xhtml');
  $templateFooter = @file_get_contents('./skin/special/template-footer.xhtml');

  // Make sure the template isn't busted, if it is send a text only error as an array
  if (!$templateHead || !$templateFooter) {
    gfError([__FUNCTION__ . ': Special Template is busted...', $aTitle, $aContent], -1);
  }

  // Can't use both the textbox and list arguments
  if ($aTextBox && $aList) {
    gfError(__FUNCTION__ . ': You cannot use both textbox and list');
  }

  // Anonymous function to determin if aContent is a string-ish or not
  $notString = function() use ($aContent) {
    return (!is_string($aContent) && !is_int($aContent)); 
  };

  // If not a string var_export it and enable the textbox
  if ($notString()) {
    $aContent = var_export($aContent, true);
    $aTextBox = true;
    $aList = false;
  }

  // Use either a textbox or an unordered list
  if ($aTextBox) {
    // We are using the textbox so put aContent in there
    $aContent = '<textarea style="width: 1195px; resize: none;" name="content" rows="36" readonly>' .
                $aContent .
                '</textarea>';
  }
  elseif ($aList) {
    // We are using an unordered list so put aContent in there
    $aContent = '<ul><li>' . $aContent . '</li><ul>';
  }

  // Set page title
  $templateHead = str_replace('<title></title>',
                  '<title>' . $aTitle . ' - ' . SOFTWARE_NAME . ' ' . SOFTWARE_VERSION . '</title>',
                  $templateHead);

  // If we are generating an error from gfError we want to clean the output buffer
  if ($aError) {
    ob_get_clean();
  }

  // Send an html header
  header('Content-Type: text/html', false);

  // write out the everything
  print($templateHead . '<h2>' . $aTitle . '</h2>' . $aContent . $templateFooter);

  // We're done here
  exit();
}

/**********************************************************************************************************************
* Error function that will display data (Error Message)
**********************************************************************************************************************/
function gfError($aValue, $aMode = 0) {
  $varExport  = var_export($aValue, true);
  $jsonEncode = json_encode($aValue, 448); // JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
  
  $pageHeader = array(
    'default' => 'Unable to Comply',
    'fatal'   => 'Fatal Error',
    'php'     => 'PHP Error',
    'output'  => 'Output'
  );

  switch($aMode) {
    case -1:
      // Text only
      header('Content-Type: text/plain', false);
      if (is_string($aValue) || is_int($aValue)) {
        print($aValue);
      }
      else {
        print($varExport);
      }
      break;
    case 1:
      gfGenContent($pageHeader['php'], $aValue, null, true, true);
      break;
    default:
      gfGenContent($pageHeader['default'], $aValue, null, true, true);
  }

  exit();
}

/**********************************************************************************************************************
* PHP Error Handler
**********************************************************************************************************************/

function gfErrorHandler($errno, $errstr, $errfile, $errline) {
  $errorCodes = array(
    E_ERROR => 'Fatal Error',
    E_WARNING => 'Warning',
    E_PARSE => 'Parse',
    E_NOTICE => 'Notice',
    E_CORE_ERROR => 'Fatal Error (Core)',
    E_CORE_WARNING => 'Warning (Core)',
    E_COMPILE_ERROR => 'Fatal Error (Compile)',
    E_COMPILE_WARNING => 'Warning (Compile)',
    E_USER_ERROR => 'Fatal Error (User Generated)',
    E_USER_WARNING => 'Warning (User Generated)',
    E_USER_NOTICE => 'Notice (User Generated)',
    E_STRICT => 'Strict',
    E_RECOVERABLE_ERROR => 'Fatal Error (Recoverable)',
    E_DEPRECATED => 'Depercated',
    E_USER_DEPRECATED => 'Depercated (User Generated)',
    E_ALL => 'All',
  );

  $errorType = $errorCodes[$errno] ?? $errno;
  $errorMessage = $errorType . ': ' . $errstr . ' in ' .
                  str_replace(ROOT_PATH, '', $errfile) . ' on line ' . $errline;

  if (!(error_reporting() & $errno)) {
    // Don't do jack shit because the developers of PHP think users shouldn't be trusted.
    return;
  }

  gfError($errorMessage, 1);
}

set_error_handler("gfErrorHandler");

/**********************************************************************************************************************
* Unified Var Checking
*
* @param $_type           Type of var to check
* @param $_value          GET/PUT/SERVER/FILES/EXISTING Normal Var
* @param $_allowFalsy     Optional - Allow falsey returns (really only works with case var)
* @returns                Value or null
**********************************************************************************************************************/
function gfSuperVar($_type, $_value, $_allowFalsy = null) {
  $finalValue = null;

  switch ($_type) {
    case 'get':
      $finalValue = $_GET[$_value] ?? null;

      if ($finalValue) {
        $finalValue = preg_replace('/[^-a-zA-Z0-9_\-\/\{\}\@\.\%\\:s\,]/', '', $_GET[$_value]);
      }

      break;
    case 'post':
      $finalValue = $_POST[$_value] ?? null;
      break;
    case 'server':
      $finalValue = $_SERVER[$_value] ?? null;
      break;
    case 'var':
      $finalValue = $_value ?? null;
      break;
    default:
      gfError('Incorrect var check');
  }

  if (!$_allowFalsy && (empty($finalValue) || $finalValue === 'none' || $finalValue === '')) {
    return null;
  }

  return $finalValue;
}

/**********************************************************************************************************************
* Check if a module is in $arrayIncludes
*
* @param $_value    A module
* @returns          true or null depending on if $_value is in $arrayIncludes
**********************************************************************************************************************/
function gfEnsureModule($_value) {
  if (!array_key_exists('arrayIncludes', $GLOBALS)) {
    gfError('$arrayIncludes is not defined');
  }
  
  if (!in_array($_value, $GLOBALS['arrayIncludes'])) {
    return null;
  }
  
  return true;
}

/**********************************************************************************************************************
* Sends HTTP Headers to client using a short name
*
* @param $_value    Short name of header
**********************************************************************************************************************/
function gfHeader($aHeader) {
  $headers = array(
    404             => 'HTTP/1.1 404 Not Found',
    501             => 'HTTP/1.1 501 Not Implemented',
    'html'          => 'Content-Type: text/html',
    'text'          => 'Content-Type: text/plain',
    'xml'           => 'Content-Type: text/xml',
    'json'          => 'Content-Type: application/json',
    'css'           => 'Content-Type: text/css',
  );
  
  if (!headers_sent() && array_key_exists($aHeader, $headers)) {   
    if (in_array($aHeader, [404, 501])) {
      gfError($headers[$aHeader]);
    }

    header($headers[$aHeader]);
  }
}

/**********************************************************************************************************************
* Sends HTTP Header to redirect the client to another URL
*
* @param $_strURL   URL to redirect to
**********************************************************************************************************************/
// This function sends a redirect header
function gfRedirect($aURL) {
	header('Location: ' . $aURL , true, 302);
  
  // We are done here
  exit();
}

/**********************************************************************************************************************
* Polyfills for missing/proposed functions
* str_starts_with, str_ends_with, str_contains
*
* @param $haystack  string
* @param $needle    substring
* @returns          true if substring exists in string else false
**********************************************************************************************************************/

if (!function_exists('str_starts_with')) {
  function str_starts_with($haystack, $needle) {
     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
  }
}

// --------------------------------------------------------------------------------------------------------------------

if (!function_exists('str_ends_with')) {
  function str_ends_with($haystack, $needle) {
    $length = strlen($needle);
    if ($length == 0) {
      return true;
    }

    return (substr($haystack, -$length) === $needle);
  }
}

// --------------------------------------------------------------------------------------------------------------------

if (!function_exists('str_contains')) {
  function str_contains($haystack, $needle) {
    if (strpos($haystack, $needle) > -1) {
      return true;
    }
    else {
      return false;
    }
  }
}

// ====================================================================================================================

// == | Main | ========================================================================================================

// Define an array that will hold the current application state
$gaRuntime = array(
  'currentScheme'       => gfSuperVar('server', 'SCHEME'),
  'phpServerName'       => gfSuperVar('server', 'SERVER_NAME'),
  'phpRequestURI'       => gfSuperVar('server', 'REQUEST_URI'),
  'remoteAddr'          => gfSuperVar('server', 'HTTP_X_FORWARDED_FOR') ??
                           gfSuperVar('server', 'REMOTE_ADDR'),
  'requestComponent'    => gfSuperVar('get', 'component'),
  'requestPath'         => gfSuperVar('get', 'path'),
);

// --------------------------------------------------------------------------------------------------------------------

// Offline check
if (file_exists(ROOT_PATH . SLASH . '.offline')) {
  gfError('Site Offline');
}

// --------------------------------------------------------------------------------------------------------------------

// Root (/) won't set a component or path
if (!$gaRuntime['requestComponent'] && !$gaRuntime['requestPath']) {
  $gaRuntime['requestComponent'] = 'site';
  $gaRuntime['requestPath'] = '/';
}
// The SPECIAL component overrides the SITE component
elseif (str_starts_with($gaRuntime['phpRequestURI'], '/special/')) {
  $gaRuntime['requestComponent'] = 'special';
}

// --------------------------------------------------------------------------------------------------------------------

// Load component based on requestComponent
if ($gaRuntime['requestComponent'] && array_key_exists($gaRuntime['requestComponent'], COMPONENTS)) {
  require_once(COMPONENTS[$gaRuntime['requestComponent']]);
}
else {
  if (!$gaRuntime['debugMode']) {
    gfHeader(404);
  }
  gfError('Invalid component');
}

// ====================================================================================================================

?>