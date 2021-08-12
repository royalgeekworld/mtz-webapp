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

define('DEBUG_MODE', $_GET['debug'] ?? null);

// --------------------------------------------------------------------------------------------------------------------

const PHP_ERROR_CODES       = array(
  E_ERROR                   => 'Fatal Error',
  E_WARNING                 => 'Warning',
  E_PARSE                   => 'Parse',
  E_NOTICE                  => 'Notice',
  E_CORE_ERROR              => 'Fatal Error (Core)',
  E_CORE_WARNING            => 'Warning (Core)',
  E_COMPILE_ERROR           => 'Fatal Error (Compile)',
  E_COMPILE_WARNING         => 'Warning (Compile)',
  E_USER_ERROR              => 'Fatal Error (User Generated)',
  E_USER_WARNING            => 'Warning (User Generated)',
  E_USER_NOTICE             => 'Notice (User Generated)',
  E_STRICT                  => 'Strict',
  E_RECOVERABLE_ERROR       => 'Fatal Error (Recoverable)',
  E_DEPRECATED              => 'Deprecated',
  E_USER_DEPRECATED         => 'Deprecated (User Generated)',
  E_ALL                     => 'All'
);

const HTTP_HEADERS          = array(
  404                       => 'HTTP/1.1 404 Not Found',
  501                       => 'HTTP/1.1 501 Not Implemented',
  'html'                    => 'Content-Type: text/html',
  'text'                    => 'Content-Type: text/plain',
  'xml'                     => 'Content-Type: text/xml',
  'json'                    => 'Content-Type: application/json',
  'css'                     => 'Content-Type: text/css',
);

// --------------------------------------------------------------------------------------------------------------------

const NEW_LINE              = "\n";
const EMPTY_STRING          = "";
const EMPTY_ARRAY           = [];
const SPACE                 = " ";
const WILDCARD              = "*";
const SLASH                 = "/";
const DOT                   = ".";
const DASH                  = "-";
const UNDERSCORE            = "_";
const DOTDOT                = DOT . DOT;

// --------------------------------------------------------------------------------------------------------------------

const DASH_SEPARATOR        = SPACE . DASH . SPACE;
const SCHEME_SUFFIX         = "://";

const PHP_EXTENSION         = DOT . 'php';
const INI_EXTENSION         = DOT . 'ini';
const XML_EXTENSION         = DOT . 'xml';
const TEMP_EXTENSION        = DOT . 'temp';
const XPINSTALL_EXTENSION   = DOT . 'xpi';
const RDF_EXTENSION         = DOT . 'rdf';
const JSON_EXTENSION        = DOT . 'json';

const JSON_ENCODE_FLAGS     = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
const FILE_WRITE_FLAGS      = "w+";

const XML_TAG               = '<?xml version="1.0" encoding="utf-8" ?>';

const REGEX_GET_FILTER      = "/[^-a-zA-Z0-9_\-\/\{\}\@\.\%\s\,]/";

// --------------------------------------------------------------------------------------------------------------------

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
  'special'         => ROOT_PATH . BASE_RELPATH . 'special.php',
  'mdn'             => ROOT_PATH . COMPONENTS_RELPATH . 'mdn/mdn.php'
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
* Polyfills for str_starts_with, str_ends_with, str_contains
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

  // Compatibility with previously used polyfill function name
  function startsWith(...$aArgs) {
    return str_starts_with(...$aArgs);
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

  // Compatibility with previously used polyfill function name
  function endsWith(...$aArgs) {
    return str_ends_with(...$aArgs);
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

  // Compatibility with previously used polyfill function name
  function contains(...$aArgs) {
    return str_contains(...$aArgs);
  }
}

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
*
* This version of the function can emit the error as xml or text depending on the environment.
* It also can use gfGenContent() if defined and has the same signature.
* It also has its legacy ability for generic output if the error message is not a string as formatted json
* regardless of the environment.
**********************************************************************************************************************/
function gfError($aValue, $phpError = false) { 
  $pageHeader = array(
    'default' => 'Unable to Comply',
    'fatal'   => 'Fatal Error',
    'php'     => 'PHP Error',
    'output'  => 'Output'
  );

  $externalOutput = function_exists('gfGenContent');
  $isCLI = (php_sapi_name() == "cli");

  if (is_string($aValue) || is_int($aValue)) {
    $eContentType = 'text/xml';
    $ePrefix = $phpError ? $pageHeader['php'] : $pageHeader['default'];

    if ($externalOutput || $isCLI) {
      $eMessage = $aValue;
    }
    else {
      $eMessage = XML_TAG . NEW_LINE . '<error title="' . $ePrefix . '">' . $aValue . '</error>';
    }
  }
  else {
    $eContentType = 'application/json';
    $ePrefix = $pageHeader['output'];
    $eMessage = json_encode($aValue, JSON_ENCODE_FLAGS);
  }

  if ($externalOutput) {
    if ($phpError) {
      gfGenContent($ePrefix, $eMessage, null, true, true);
    }

    gfGenContent($ePrefix, $eMessage);
  }
  elseif ($isCLI) {
    print('========================================' . NEW_LINE .
          $ePrefix . NEW_LINE .
          '========================================' . NEW_LINE .
          $eMessage . NEW_LINE);
  }
  else {
    header('Content-Type: ' . $eContentType, false);
    print($eMessage);
  }

  // We're done here.
  exit();
}

/**********************************************************************************************************************
* PHP Error Handler
**********************************************************************************************************************/
function gfErrorHandler($eCode, $eString, $eFile, $eLine) {
  $eType = PHP_ERROR_CODES[$eCode] ?? $eCode;
  $eMessage = $eType . ': ' . $eString . SPACE . 'in' . SPACE .
                  str_replace(ROOT_PATH, '', $eFile) . SPACE . 'on line' . SPACE . $eLine;

  if (!(error_reporting() & $eCode)) {
    // Don't do jack shit because the developers of PHP think users shouldn't be trusted.
    return;
  }

  gfError($eMessage, true);
}

// Set error handler fairly early...
set_error_handler("gfErrorHandler");

/**********************************************************************************************************************
* Unified Var Checking
*
* @param $aVarType        Type of var to check
* @param $aVarValue       GET/SERVER/EXISTING Normal Var
* @param $aFalsy          Optional - Allow falsey returns on var/direct
* @returns                Value or null
**********************************************************************************************************************/
function gfSuperVar($aVarType, $aVarValue, $aFalsy = null) {
  // Set up the Error Message Prefix
  $ePrefix = __FUNCTION__ . DASH_SEPARATOR;
  $rv = null;

  // Turn the variable type into all caps prefixed with an underscore
  $varType = UNDERSCORE . strtoupper($aVarType);

  // General variable absolute null check unless falsy is allowed
  if ($varType == "_VAR" || $varType == "_DIRECT"){
    $rv = $aVarValue ?? null;

    if ($rv && !$aFalsy) {
      if (empty($rv) || $rv === 'none' || $rv === EMPTY_STRING) {
        return null;
      }
    }

    return $rv;
  }

  // This handles the superglobals
  switch($varType) {
    case '_SERVER':
    case '_GET':
    case '_FILES':
    case '_POST':
    case '_COOKIE':
    case '_SESSION':
      $rv = $GLOBALS[$varType][$aVarValue] ?? null;
      break;
    default:
      // We don't know WHAT was requested but it is obviously wrong...
      gfError($ePrefix . 'Incorrect Var Check');
  }
  
  // We always pass $_GET values through a general regular expression
  // This allows only a-z A-Z 0-9 - / { } @ % whitespace and ,
  if ($rv && $varType == "_GET") {
    $rv = preg_replace(REGEX_GET_FILTER, EMPTY_STRING, $rv);
  }

  // Files need special handling.. In principle we hard fail if it is anything other than
  // OK or NO FILE
  if ($rv && $varType == "_FILES") {
    if (!in_array($rv['error'], [UPLOAD_ERR_OK, UPLOAD_ERR_NO_FILE])) {
      gfError($ePrefix . 'Upload of ' . $aVarValue . ' failed with error code: ' . $rv['error']);
    }

    // No file is handled as merely being null
    if ($rv['error'] == UPLOAD_ERR_NO_FILE) {
      return null;
    }

    // Cursory check the actual mime-type and replace whatever the web client sent
    $rv['type'] = mime_content_type($rv['tmp_name']);
  }
  
  return $rv;
}

/**********************************************************************************************************************
* Sends HTTP Headers to client using a short name
*
* @param $aHeader    Short name of header
**********************************************************************************************************************/
function gfHeader($aHeader) { 
  if (!headers_sent() && array_key_exists($aHeader, HTTP_HEADERS)) {   
    if (DEBUG_MODE) {
      gfError(HTTP_HEADERS[$aHeader]);
    }
    else {
      header(HTTP_HEADERS[$aHeader]);

      if (in_array($aHeader, [404, 501])) {
        exit();
      }
    }
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
* Explodes a string to an array without empty elements if it starts or ends with the seperator
*
* @param $aSeparator   Separator used to split the string
* @param $aString      String to be exploded
* @returns             Array of string parts
***********************************************************************************************************************/
function gfExplodeString($aSeparator, $aString) {
  $ePrefix = __FUNCTION__ . SPACE . DASH . SPACE;

  if (!is_string($aString)) {
    gfError($ePrefix . 'Specified string is not a string type');
  }

  if (!str_contains($aString, $aSeparator)) {
    gfError($ePrefix . 'String does not contain the seperator');
  }

  $explodedString = array_values(array_filter(explode($aSeparator, $aString), 'strlen'));

  return $explodedString;
}

/**********************************************************************************************************************
* Splits a path into an indexed array of parts
*
* @param $aPath   URI Path
* @returns        array of uri parts in order
***********************************************************************************************************************/
function gfExplodePath($aPath) {
  if ($aPath == SLASH) {
    return ['root'];
  }

  return gfExplodeString(SLASH, $aPath);
}

/**********************************************************************************************************************
* Builds a path from a list of arguments
*
* @param        ...$aPathParts  Path Parts
* @returns                      Path string
***********************************************************************************************************************/
function gfBuildPath(...$aPathParts) {
  $path = implode(SLASH, $aPathParts);
  $filesystem = str_starts_with($path, ROOT_PATH);
  
  // Add a prepending slash if this is not a filesystem path
  if (!$filesystem) {
    $path = SLASH . $path;
  }

  // Add a trailing slash if the last part does not contain a dot
  // If it is a filesystem path then we will also add a trailing slash if the last part starts with a dot
  if (!str_contains(basename($path), DOT) || ($filesystem && str_starts_with(basename($path), DOT))) {
    $path .= SLASH;
  }

  return $path;
}

/**********************************************************************************************************************
* Strips the constant ROOT_PATH from a string
*
* @param $aPath   Path to be stripped
* @returns        Stripped path
***********************************************************************************************************************/
function gfStripRootPath($aPath) {
  return str_replace(ROOT_PATH, EMPTY_STRING, $aPath);
}

/**********************************************************************************************************************
* Read file (decode json if the file has that extension or parse install.rdf if that is the target file)
*
* @param $aFile     File to read
* @returns          file contents or array if json
                    null if error, empty string, or empty array
**********************************************************************************************************************/
function gfReadFile($aFile) {
  $file = @file_get_contents($aFile);

  // Automagically decode json
  if (str_ends_with($aFile, JSON_EXTENSION)) {
    $file = json_decode($file, true);
  }

  // If it is a mozilla install manifest and the module has been included then parse it
  if (str_ends_with($aFile, 'install.rdf') && array_key_exists('gmMozillaRDF', $GLOBALS)) {
    global $gmMozillaRDF;
    $file = $gmMozillaRDF->parseInstallManifest($file);

    if (is_string($file)) {
      gfError('RDF Parsing Error: ' . $file);
    }
  }

  return gfSuperVar('var', $file);
}

// ====================================================================================================================

// == | Main | ========================================================================================================

// Define an array that will hold the current application state
$gaRuntime = array(
  'currentScheme'       => gfSuperVar('server', 'SCHEME') ?? (gfSuperVar('server', 'HTTPS') ? 'https' : 'http'),
  'phpServerName'       => gfSuperVar('server', 'SERVER_NAME'),
  'phpRequestURI'       => gfSuperVar('server', 'REQUEST_URI'),
  'remoteAddr'          => gfSuperVar('server', 'HTTP_X_FORWARDED_FOR') ?? gfSuperVar('server', 'REMOTE_ADDR'),
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
elseif (str_starts_with($gaRuntime['phpRequestURI'], '/mdn/')) {
  $gaRuntime['requestComponent'] = 'mdn';
}

// --------------------------------------------------------------------------------------------------------------------

// Load component based on requestComponent
if ($gaRuntime['requestComponent'] && array_key_exists($gaRuntime['requestComponent'], COMPONENTS)) {
  require_once(COMPONENTS[$gaRuntime['requestComponent']]);
}
else {
  if (!DEBUG_MODE) {
    gfHeader(404);
  }
  gfError('Invalid component');
}

// ====================================================================================================================

?>