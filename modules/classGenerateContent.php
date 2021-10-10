<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.

class classGenerateContent {
  public $extraSimpleTags;
  public $extraRegexTags;
  private $skinPath;
  private $contentURL;
  private $contentBase;

  // --------------------------------------------------------------------------------------------------------------------

  public function loadAndDisplay() {
    global $gaRuntime;
    $ePrefix = __CLASS__ . '::' . __FUNCTION__ . DASH_SEPARATOR;

    if (str_contains($gaRuntime['qPath'], DOTDOT)) {
      gfError($ePrefix . 'CODE: ID-10-T');
    }

    $this->contentURL = $gaRuntime['qPath'];
    $this->contentBase = dirname(COMPONENTS[$gaRuntime['qComponent']]) . SLASH . 'content';

    $filename = 'selene' . CONTENT_EXTENSION;

    $content = gfReadFile($this->contentBase . $this->contentURL . $filename);

    if (!$content) {
      gfError($ePrefix . 'Unable to load content');
    }

    // Parse the YAML header
    $contentData = gfSuperVar('var', @yaml_parse($content));

    if (!$contentData) {
      gfError($ePrefix . 'Unable to parse YAML for content');
    }

    $contentData['content'] = preg_replace(REGEX_YAML_FILTER, EMPTY_STRING, $content);

    $this->display($contentData);
  }

  // --------------------------------------------------------------------------------------------------------------------

  public function display($aContent, $aURL = null) {
    global $gaRuntime;
    $ePrefix = __CLASS__ . '::' . __FUNCTION__ . DASH_SEPARATOR;

    if (!$this->contentURL) {
      $this->contentURL = $aURL ?? $gaRuntime['qPath'];
    }

    if (!$this->contentBase) {
      $this->contentDir = ROOT_PATH . BASE_RELPATH . 'content';
    }

    if (!$this->skinPath) {
      $this->skinPath = ROOT_PATH . SKIN_RELPATH . 'palemoon';
    }

    $template       = gfReadFile($this->skinPath . SLASH . 'site-template.xhtml');
    $stylesheet     = gfReadFile($this->skinPath . SLASH . 'site-stylesheet.css');
    $stylesheetHLJS = gfReadFile(dirname(JSMODULES['highlight']) . '/styles/github.min.css');

    $isHTML = false;

    if (is_array($aContent)) {
      $title = $aContent['title'];
      $content = $aContent['content'];
      $isHTML = array_key_exists('html', $aContent);
    }
    else {
      $title = 'Content Test';
      $content = $aContent;
      // Content Test supports the legacy html override since it doesn't support YAML
      $isHTML = str_starts_with($content, '[html-override]');
      // Discard any YAML for the content test
      $content = preg_replace(REGEX_YAML_FILTER, EMPTY_STRING, $content);
    }

    if (!$template || !$stylesheet || !$content) {
      gfError($ePrefix . 'Failed to generate content.');
    }

    $page = $template;

    $content = $isHTML ? str_replace('[html-override]', EMPTY_STRING, $content) : $this->parseCodeTags($content, true, true);
    $content = '<h1>' . ($this->contentURL == SLASH ? str_replace(DASH_SEPARATOR, SPACE, SITE_NAME) : $title) .
               '</h1>' . NEW_LINE . $content;

    $pageSubsts = array(
      '{%SITE_STYLESHEET}'      => $stylesheet,
      '{%HIGHLIGHT_STYLESHEET}' => $stylesheetHLJS,
      '{%PAGE_CONTENT}'         => $content,
      '{%BASE_PATH}'            => gfStripRootPath($this->skinPath . SLASH),
      '{%SKIN_PATH}'            => gfStripRootPath($this->skinPath),
      '{%CONTENT_PATH}'         => gfStripRootPath($this->contentBase . $this->contentURL),
      '{%SITE_NAME}'            => SITE_NAME,
      '{%PAGE_TITLE}'           => $this->contentURL == SLASH ? 'Front Page' : $title,
      '{%COPYRIGHT_YEAR}'       => date("Y"),
      '{%SOFTWARE_NAME}'        => SOFTWARE_NAME,
      '{%SOFTWARE_VERSION}'     => SOFTWARE_VERSION,
      '{%SOFTWARE_REPO}'        => SOFTWARE_REPO,
    );

    if (is_string($aContent)) {
      $backLink = '<p><a href="#" onclick="window.history.back();"><-- Back</a></p>';
      $pageSubsts['{%PAGE_CONTENT}'] = $backLink . $pageSubsts['{%PAGE_CONTENT}'];
    }

    $page = gfSubst('simple', $pageSubsts, $page);

    // Clear contentURL for reasons
    $this->contentURL = null;

    // Set header and print the content and fuck off...
    gfHeader('html');
    print($page);
    exit();
  }

  // --------------------------------------------------------------------------------------------------------------------

  private function parseCodeTags($aContent, $allowHTMLAttrs = false, $allowScripts = false) {
    $aContent = preg_replace('/\<\!\-\- (.*) \-\-\>\n/iU', EMPTY_STRING, $aContent);
    $aContent = htmlentities($aContent, ENT_XHTML);

    // XXXTobin: Move HLJS override styling to stylesheet
    $basePreStyle                   = 'background-color: #ecf3f7 !important; border: 1px solid #cedfea !important;' . 
                                      'border-radius: 6px !important;';
    $blockPreStyle                  = 'padding: 4px !important;';
    $inlinePreStyle                 = 'display: inline !important; line-height: 20pt !important;';
    $codePreStyle                   = 'background-color: transparent !important;';

    $htmlTags       = ['p', 'span', 'small', 'br', 'hr', 'ul', 'ol', 'li', 'table', 'th', 'tr', 'td',
                       'caption', 'col', 'colgroup', 'thead', 'tbody', 'tfoot'];
    $simpleTags     = implode(PIPE, array_merge($htmlTags, ['title', 'header', 'section', 'b', 'i', 'u']));
    $regexTags      = array(
      "\[\/(" . $simpleTags . ")\]"       => '</$1>',
      "\[(" . $simpleTags . ")\]"         => '<$1>',
      "\[break\]"                         => '<br />',
      "\[dblbreak\]"                      => '<br /><br/>',
      "\[separator\]"                     => '<hr style="display: block; width: 66%; margin: 2em auto;" />',
      "\[title=\"(.*)\"\]"                => '<h1>$1</h1>',
      "\[header=\"(.*)\"\]"               => '<h2>$1</h2>',
      "\[section=\"(.*)\"\]"              => '<h3>$1</h3>',
      "\[anchor=(.*)\]"                   => '<a name="$1"></a>',
      "\[link=(.*)\](.*)\[\/link\]"       => '<a href="$1">$2</a>',
      "\[url=(.*)\](.*)\[\/url\]"         => '<a href="$1" target="_blank">$2</a>',
      "\[url\](.*)\[\/url\]"              => '<a href="$1" target="_blank">$1</a>',
      "\[code=(.*)\]"                     => '<pre style="' . $basePreStyle . $blockPreStyle . '">' .
                                             '<code class="$1" style="' . $codePreStyle . '">',
      "\[codeline=(.*)\]"                 => '<pre style="' . $basePreStyle . $inlinePreStyle .'">' .
                                             '<code class="$1" style="display: inline; ' . $codePreStyle . '">',

    );

    // Should we allow script tags?
    if ($allowScripts) {
      $regexTags["\[script=(.*)\]"] = '<script src="$1" type="text/javascript"></script>';
    }

    // Should we allow attributes on HTML tags?
    // Additionally, this adds support for the img tag
    if ($allowHTMLAttrs) {
      $regexTags["\[img(.*)\](.*)\[\/img\]"] = '<img src="$2"$1 />';
      $regexTags["\[(" . $simpleTags . ")(.*)\]"] = '<$1$2>';
    }

    // Maintain support for custom simple tags in the previous implementation but do the subst at once
    if ($this->extraSimpleTags) {
      $aContent = gfSubst('simple', $this->extraSimpleTags, $aContent);
    }

    // Support extra regex tags
    if ($this->extraRegexTags) {
      $regexTags = array_merge($regexTags, $this->extraRegexTags);
    }

    // Finally process the regex substs
    $aContent = gfSubst('regex', $regexTags, $aContent);

    // Clear the extra tag class vars
    $this->extraSimpleTags = null;
    $this->extraRegexTags = null;

    // And return
    return $aContent;
  }
}

?>