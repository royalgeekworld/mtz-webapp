<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.

// == | Main | ========================================================================================================

$htmlOut = gfSuperVar('get', 'htmlOut');

function processContent($aContent) {     
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
    '[code]'                                             => '<pre><code class="plaintext">',
    '[/code]'                                            => '</code></pre>',
  );

  foreach ($seleneCodeSubsts as $_key => $_value) {
    $aContent = str_replace($_key, $_value, $aContent);
  }

  $seleneCodeSuperRegex = 'span|p|ul|ol|li|hr|table|th|hr|caption|col|colgroup|thead|tbody|tfoot';
  $seleneCodeRegex = array(
    '\[title=\"(.*)\"\]'                                  => '<h1>$1</h1>',
    '\[header=\"(.*)\"\]'                                 => '<h2>$1</h2>',
    '\[section=\"(.*)\"\]'                                => '<h3>$1</h3>',
    '\[link=(.*)\](.*)\[\/link\]'                         => '<a href="$1">$2</a>',
    '\[url=(.*)\](.*)\[\/url\]'                           => '<a href="$1" target="_blank">$2</a>',
    '\[url\](.*)\[\/url\]'                                => '<a href="$1" target="_blank">$1</a>',
    '\[img(.*)\](.*)\[\/img\]'                            => '<img src="$2"$1 />',
    '\[code=(.*)\]'                                       => '<pre><code class="$1">',
    '\[(' . $seleneCodeSuperRegex . ')(.*)\]'             => '<$1$2>',
  );

  foreach ($seleneCodeRegex as $_key => $_value) {
    $aContent = preg_replace('/' . $_key . '/iU', $_value, $aContent);
  }

  return $aContent;
}

// ------

$content = <<<EOT
[title]Title
Syntax[/title]
[title="Title Syntax Alt"]
[header="Header"]
[section="Section"]
[p]Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph Paragraph.[/p]
[p]
  Paragraph with line breaks also formatting[br]
  [b]Bold text[/b][break]
  [i]Italic Text[/i][break]
  [u]Underlined Text[/u][break]
  [url=http://www.palemoon.org/]Pale Moon[/url][break]
  [url]http://www.palemoon.org/[/url][break]
  [img]about:logo[/img][break]
[/p]
[ul]
  [li]Unordered list 1[/li]
  [li]Unordered list 2[/li]
[/ul]
[ol][li]Ordered list 1[/li][li]Ordered list 2[/li][/ol]
[code]test code
here
[/code]
EOT;

if (!$htmlOut) {
  gfGenContent('seleneCode Test', [$content, processContent($content)]);
}

print processContent($content);
exit();

// ====================================================================================================================

?>