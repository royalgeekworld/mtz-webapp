<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.

class classGenerateContent {
  public $extraSimpleTags;
  public $extraRegexTags;
  private $contentURL;

  public function display($aURL = null, $aContent = null) {
    $ePrefix = __CLASS__ . '::' . __FUNCTION__ . DASH_SEPARATOR;

    if (!$this->contentURL) {
      if (!$aURL) {
        gfError($ePrefix . 'You must pass a url');
      }

      $this->contentURL = $aURL;
    }

    $contentDir     = ROOT_PATH . BASE_RELPATH . 'content' . SLASH;
    $skinDir        = ROOT_PATH . SKIN_RELPATH . SKIN . SLASH;

    $template       = gfReadFile($skinDir . 'site-template.xhtml');
    $stylesheet     = gfReadFile($skinDir . 'site-stylesheet.css');
    $stylesheetHLJS = gfReadFile(dirname(JSMODULES['highlight']) . '/styles/github.css');

    if ($aContent) {
      if (is_array($aContent)) {
        $title = $aContent['title'];
        $content = $aContent['content'];
        if (array_key_exists('html', $aContent)) {
          $content = "[html-override]" . NEW_LINE . $content;
        }
      }
      else {
        $title = 'Content Test';
        $content = $aContent;
      }
    }
    else {
      $content = gfReadFile($contentDir . CONTENT[$this->contentURL][0] . '.content');
      $title = CONTENT[$this->contentURL][1];
    }

    if (!$template || !$stylesheet || !$content) {
      gfError($ePrefix . 'Failed to generate content.');
    }

    $finalContent = $template;

    $pageSubsts = array(
      '{%SITE_STYLESHEET}'      => $stylesheet,
      '{%HIGHLIGHT_STYLESHEET}' => $stylesheetHLJS,
      '{%PAGE_CONTENT}'         => '<h1>' . ($this->contentURL == SLASH ? 'Pale Moon Developer Site' : $title) . '</h1>' .
                                   NEW_LINE . $this->parseSeleneCode($content),
      '{%BASE_PATH}'            => gfStripRootPath($skinDir),
      '{%CONTENT_PATH}'         => gfStripRootPath($contentDir),
      '{%SITE_NAME}'            => SITE_NAME,
      '{%PAGE_TITLE}'           => $this->contentURL == SLASH ? 'Front Page' : $title,
      '{%COPYRIGHT_YEAR}'       => date("Y"),
      '{%SOFTWARE_NAME}'        => SOFTWARE_NAME,
      '{%SOFTWARE_VERSION}'     => SOFTWARE_VERSION,
      '{%SOFTWARE_REPO}'        => SOFTWARE_REPO,
    );

    if (is_string($aContent)) {
      $pageSubsts['{%PAGE_CONTENT}'] = '<p><a href="#" onclick="window.history.back();"><-- Back</a></p>' . $pageSubsts['{%PAGE_CONTENT}'];
    }

    foreach ($pageSubsts as $_key => $_value) {
      $finalContent = str_replace($_key, $_value, $finalContent);
    }

    // Clear contentURL for reasons
    $this->contentURL = null;

    // Set header and print the content and fuck off...
    gfHeader('html');
    print($finalContent);
    exit();
  }

  // --------------------------------------------------------------------------------------------------------------------

  private function parseSeleneCode($aContent) {     
    if (str_starts_with($aContent, '[html-override]')) {
      return str_replace('[html-override]', '', $aContent);
    }

    $aContent = preg_replace('/\<\!\-\- (.*) \-\-\>\n/iU', '', $aContent);
    $aContent = htmlentities($aContent, ENT_XHTML);

    $seleneCodeSimpleTags = array(
      '[title]'                                             => '<h1>',
      '[/title]'                                            => '</h1>',
      '[header]'                                            => '<h2>',
      '[/header]'                                           => '</h2>',
      '[section]'                                           => '<h3>',
      '[/section]'                                          => '</h3>',
      '[small]'                                             => '<small>',
      '[/small]'                                            => '</small>',
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

    $seleneCodeSuperRegex = 'span|p|ul|ol|li|hr|table|th|tr|td|hr|caption|col|colgroup|thead|tbody|tfoot';
    $basePreStyle = 'background-color: #ecf3f7 !important; border: 1px solid #cedfea !important; border-radius: 6px !important;';
    $blockPreStyle = 'padding: 4px !important;';
    $inlinePreStyle = 'display: inline !important; line-height: 20pt !important;';
    $codePreStyle = 'background-color: transparent !important;';

    $seleneCodeRegexTags = array(
      "\[title=\"(.*)\"\]"                                  => '<h1>$1</h1>',
      "\[header=\"(.*)\"\]"                                 => '<h2>$1</h2>',
      "\[section=\"(.*)\"\]"                                => '<h3>$1</h3>',
      "\[script=(.*)\]"                                     => '<script src="$1" type="text/javascript"></script>',
      "\[link=(.*)\](.*)\[\/link\]"                         => '<a href="$1">$2</a>',
      "\[url=(.*)\](.*)\[\/url\]"                           => '<a href="$1" target="_blank">$2</a>',
      "\[url\](.*)\[\/url\]"                                => '<a href="$1" target="_blank">$1</a>',
      "\[img(.*)\](.*)\[\/img\]"                            => '<img src="$2"$1 />',
      "\[code=(.*)\]"                                       => '<pre style="' . $basePreStyle . $blockPreStyle . '">' .
                                                               '<code class="$1" style="' . $codePreStyle . '">',
      "\[codeline=(.*)\]"                                   => '<pre style="' . $basePreStyle . $inlinePreStyle .'">' .
                                                               '<code class="$1" style="display: inline; ' . $codePreStyle . '">',
      "\[(" . $seleneCodeSuperRegex . ")(.*)\]"             => '<$1$2>',
    );

    if ($this->extraSimpleTags) {
      $seleneCodeSimpleTags = array_merge($seleneCodeSimpleTags, $this->extraSimpleTags);
    }

    if ($this->extraRegexTags) {
      $seleneCodeRegexTags = array_merge($seleneCodeRegexTags, $this->extraRegexTags);
    }

    // Process the substs
    $aContent = gfSubst('simple', $seleneCodeSimpleTags, $aContent);
    $aContent = gfSubst('regex', $seleneCodeRegexTags, $aContent);

    // Clear the extra tag class vars
    $this->extraSimpleTags = null;
    $this->extraRegexTags = null;

    return $aContent;
  }
}

?>