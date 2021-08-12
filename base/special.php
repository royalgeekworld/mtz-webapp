<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.

// == | Functions | ===================================================================================================

/**********************************************************************************************************************
* Strips path to obtain the slug
*
* @param $aPath     $gaRuntime['qPath']
* @param $aPrefix   Prefix to strip 
* @returns          slug
***********************************************************************************************************************/
function funcStripPath($aPath, $aPrefix) {
  return str_replace('/', '', str_replace($aPrefix, '', $aPath));
}

// == | Main | ========================================================================================================

$strComponentPath = dirname(COMPONENTS[$gaRuntime['qComponent']]) . '/';
$strStripPath = funcStripPath($gaRuntime['qPath'], '/special/');

// --------------------------------------------------------------------------------------------------------------------

switch ($strStripPath) {
  case 'phpinfo':
    phpinfo(INFO_GENERAL | INFO_CONFIGURATION | INFO_ENVIRONMENT | INFO_VARIABLES);
    break;
  case 'software-state':
    gfGenContent('Software State', $gaRuntime);
    break;
  case 'test':
    $gaRuntime['requestTestCase'] = gfSuperVar('get', 'case');
    $arrayTestsGlob = glob($strComponentPath . 'tests/*.php');
    $arrayFinalTests = [];

    foreach ($arrayTestsGlob as $_value) {
      $arrayFinalTests[] = str_replace('.php',
                                       '',
                                       str_replace($strComponentPath . 'tests/', '', $_value));
    }

    unset($arrayTestsGlob);

    if ($gaRuntime['requestTestCase'] &&
        in_array($gaRuntime['requestTestCase'], $arrayFinalTests)) {
      require_once($strComponentPath . 'tests/' . $gaRuntime['requestTestCase'] . '.php');
    }

    $testsHTML = '';

    foreach ($arrayFinalTests as $_value) {
      $testsHTML .= '<li><a href="/special/test/?case=' . $_value . '">' . $_value . '</a></li>';
    }

    $testsHTML = '<ul>' . $testsHTML . '</ul>';

    gfGenContent('Test Cases - Special Component', $testsHTML);
    break;
  default:
    $rootHTML = '<a href="/special/test/">Test Cases</a></li><li>' .
                '<a href="/special/phpinfo/">PHP Info</a></li><li>' .
                '<a href="/special/software-state/">Software State</a>';
    gfGenContent('Special Component', $rootHTML, null, true);
}

exit();

// ====================================================================================================================

?>