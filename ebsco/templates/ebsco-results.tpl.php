<?php

/**
 * @file
 * Default theme implementation for displaying EBSCO results.
 *
 * @see template_preprocess_ebsco_results()
 *
 *
 * Copyright [2017] [EBSCO Information Services]
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

  if (isset($autoSuggestTerms)) {
      if (count($autoSuggestTerms) > 0) {
        $suggestString = "";
        foreach ($autoSuggestTerms as $term) {
          $path = url('ebsco/results', array('query' => array('type' => "")));
          $suggestString .= "<a href=\"{$path}&lookfor=$term\">" . $term . "</a> ";
        }
        echo "<p><strong>Did you mean " . $suggestString . "?</strong></p>";
      }
  }

  if ($records){

    echo t('Showing') . " <strong>" . $record_start . "</strong> - <strong> " . $record_end . "</strong> " . t('of') . " <strong>" . $record_count . "</strong> ";

    if ($search_view == 'basic') {
      echo t('for search') . " <strong>'" . check_plain($lookfor) . "'</strong> ";
    }

    if ($search_time){
      echo "," . t('query time') . ":" . check_plain(round($search_time, 2)) . " s";
    }

    print $sort_form;
    print $pager;

    if (!user_is_logged_in()) {
      $link = '<a href="' . url('user') . '">' . t('Login') . '</a>';
      echo '<p class="top-login-message">';
      echo sprintf(t('Hello, Guest. %s for full access.'), $link);
      echo '</p>';
    }

    $tabs = "";
    $tabsContent = "";

    $counter = 0;
    $rsItem = NULL;

    if (isset($relatedContent)) {
      foreach ($relatedContent as $item) {
        if  (isset($item["RelatedRecord"])) {
            $tabs .= '<li data-target="#researchstarters" data-slide-to="' . $counter . '" ' . ($counter == 0 ? ' class="active" ' : ' ') . '></li>';
            $counter++;
            if (isset($item["RelatedRecord"]["Records"]["Record"][0])) {
              $rsItem = $item["RelatedRecord"]["Records"]["Record"][0];
            }
            else
            {
              if (isset($item["Records"]["Record"])) {
                $rsItem = $item["Records"]["Record"];
              }
            }
            if ($rsItem == NULL) {
              continue;
            }
            // var_dump($rsItem);
            $rsTitle = "";
            $rsSubjects = "";
            $rsAbstract = "";
            $rsSource = "";
            $rsImage = "";
            $rsAN = $rsItem["Header"]["An"];
            $rsDB = $rsItem["Header"]["DbId"];
            $rsUrl = url('ebsco/result', array('query' => array('id' => $rsAN . "|" . $rsDB)));

            if (isset($rsItem["ImageInfo"]["CoverArt"])) {
              $rsImage = $rsItem["ImageInfo"]["CoverArt"]["Target"];
            }
            foreach($rsItem["Items"]["Item"] as $it) {
              switch ($it["Group"]) {
                case "Ti":
                  $rsTitle = $it["Data"];
                  break;

                case "Src":
                  $rsSource = $it["Data"];
                  break;

                case "Su":
                  $rsSubjects = $it["Data"];

                  break;

                case "Ab":
                  $rsAbstract = $it["Data"];
                  break;
              }
            }

            $tabsContent .= '<div class="item" ><div class="carousel-caption">';
            if ($rsImage <> "") {
              $tabsContent .= '<img src="' . $rsImage . '" alt="' . $rsTitle . '" style="float:left">';
            }
            if ($rsTitle <> ""){
              $tabsContent .= '<h3 class="relatedTitle"><a href="' . $rsUrl . '" class="title _record_link">' . $rsTitle . '</a></h3>';
            }
            if ($rsSubjects <> ""){
              $path = url('ebsco/results', array('query' => array('type' => "Subject")));
              $link_xml = '/<searchLink fieldCode="([^\"]*)" term="%22([^\"]*)%22">/';
              $link_html = "<a href=\"{$path}&lookfor=$2\">";
              $rsSubjects = preg_replace($link_xml, $link_html, $rsSubjects);
              $rsSubjects = str_replace('</searchLink>', '</a>', $rsSubjects);
              $tabsContent .= '<p><strong>' . t('Subjects') . '</strong>:<span class="quotestart">' . str_replace(array("<br>", "<br />"), ', ', $rsSubjects) . '</span></p>';
            }
            if ($rsSource <> ""){
              $tabsContent .= '<p>' . $rsSource . '</p>';
            }
            if ($rsAbstract <> ""){
              $tabsContent .= '<p><cite>' . $rsAbstract . '</cite></p>';
            }

            $tabsContent .= '</div></div>';
        }

        // Replated publications.
        if  (isset($item["RelatedPublication"])) {
            // var_dump($item);
            $tabs .= '<li data-target="#ematchplacard" data-slide-to="' . $counter . '" ' . ($counter == 0 ? ' class="active" ' : ' ') . '></li>';
            $counter++;

            $tabsContent .= '<div class="item" ><div class="carousel-caption">';
            $tabsContent .= '
              <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
              <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>
              ';
            foreach ($item["RelatedPublication"]["PublicationRecords"] as $pub){
              $plink = $pub["PLink"];
              foreach($pub["Items"]["Item"] as $item) {
                if ($item["Group"] == "Ti")
                {
                  $tabsContent .= '<h3 class="relatedTitle"><a href="' . $plink . ' target="_blank" >' . $item["Data"] . '</a></h3>';
                }
                else
                {
                  $tabsContent .= "<strong>" . $item["Label"] . "</strong> : " . $item["Data"] . "<br/>";
                }
              }
              $tabsContent .= '<div id="publicationList"><ul>';
              foreach($pub["FullTextHoldings"]["FullTextHolding"] as $itemHoldings) {
                $hURL = isset($itemHoldings["URL"]) ? $itemHoldings["URL"] : "";
                $hName = isset($itemHoldings["Name"]) ? $itemHoldings["Name"] : "";
                $hCoverage = isset($itemHoldings["CoverageStatement"]) ? $itemHoldings["CoverageStatement"] : "";
                $hEmbargo = "";
                if (isset($itemHoldings["EmbargoDescription"])) {
                  // If no embargo, zero size array.
                  if (!is_array($itemHoldings["EmbargoDescription"])) {
                    $hEmbargo = $itemHoldings["EmbargoDescription"];
                  }
                }
                $tabsContent .= '<li>
                              <a href="' . $hURL . '" target="_blank">' . $hName . '</a>;
                              <ul><li>Coverage:' . $hCoverage . '; ';

                if ($hEmbargo <> '') {
                  $tabsContent .= '</li><li>Embargo:' . $hEmbargo;
                }
                $tabsContent .= '</li></ul></li>';
                // Notes.
              }
              $tabsContent .= '</ul></div>';
            }
            $tabsContent .= '
                <script>
                  jQuery("#publicationList").jstree().on("changed.jstree", function (e, data) {
                         var pubURL = data.instance.get_node(data.node, true).children("a").attr("href");
                         if (pubURL!="#") {
                          window.open(pubURL);
                         }
                  });
                </script>';
            $tabsContent .= '</div></div>';
            break;
        }
      }
    }

    if ($tabsContent <> "") {
      echo '<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>

            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

            <div id="relatedInformation" class="carousel slide" data-ride="carousel" style="display:block;">
              <!-- Indicators -->
              <ol class="carousel-indicators">' . $tabs . '</ol>';
      echo '<div class="carousel-inner" role="listbox" >' . $tabsContent . '</div>';
      echo '<!-- Left and right controls -->
            <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left" ></span>
            </a>
            <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right" ></span>
            </a>
          </div>
          <script>
            jQuery("#relatedInformation").carousel({
              pause: true,
              interval:4000
            })
          </script>';

    }

  ?>

  <ol class="search-results ebsco">
  <?php foreach ($records as $record):

    // Trim Title if needed.
    if ($trim_title && strlen($record->title) > $trim_title) {
      $record->title = truncate_utf8($record->title, $trim_title, TRUE, TRUE, 1);
    }

    // Trim Authors if needed.
    if ($trim_authors && strlen($record->authors) > $trim_authors) {
      $record->authors = truncate_utf8(implode(',',explode('<br />', $record->authors)), $trim_authors, TRUE, TRUE, 1);
    }

    $id = check_plain($record->record_id());
    $recordUrl = url('ebsco/result', array('query' => array('id' => $id)));
    $fulltextUrl = url('ebsco/fulltext', array('query' => array('id' => $id)));
    $pdfUrl = url('ebsco/pdf', array('query' => array('id' => $id)));

    ?>
    <li>
    <div class="record-number floatleft">
      <?php print $record->result_id; ?>
    </div>

    <div class="result floatleft">
      <div class="span-2">
      <?php
        if ($record->small_thumb_link){
          echo '
            <a href="' . $recordUrl . '" class="_record_link">
            <img src="' . $record->small_thumb_link . '" class="book-jacket" alt="' . t('Book jacket') . '"/>
            </a>';
        }
      ?>
      </div>

      <div class="span-9">
      <div class="result-line1">
        <?php

        if ($record->access_level == '1'){
          echo '<p>';
          $label = '<strong>' . check_plain($record->db_label) . '</strong>';
          $link = '<a href="' . url('user') . '">' . t('Login') . '</a>';
          echo sprintf(t('This result from %s cannot be displayed to guests.'), $label) . "<br /><strong>" . sprintf(t('%s for full access.'), $link) . "</strong>";
          echo "</p>";
        }
        elseif ($record->title){
          echo '<a href="' . $recordUrl . '" class="title _record_link">' . $record->title . '</a>';
        }
         ?>
      </div>

      <div class="result-line2">
        <?php
        if (!empty($record->authors)){
          echo "<span>" . t('by') . " " . str_replace(array("<br>", "<br />"), ', ', $record->authors) . " </span>";
        }

        if (!empty($record->source)){
          echo '<p>' . t('Published in') . " " . $record->source . '</p>';
        }

        ?>
      </div>

      <div class="result-line3">
        <?php

        if (!empty($record->summary)){
          echo '<cite>' . $record->summary . '</cite><br />';
        }

        if (!empty($record->subjects)){
          echo '<strong>' . t('Subjects') . '</strong>:<span class="quotestart">' . str_replace('<br />', ', ', $record->subjects) . '</span>';
        }

        ?>
      </div>

      <?php
        if (!empty($record->custom_links)){
      ?>
        <div class="result-line4">
        <ul class="custom-links">
          <?php
          foreach ($record->custom_links as $link){ ?>
          <li>
            <a href="<?php print $link['Url']; ?>" target="_blank" title="<?php print $link['MouseOverText']; ?>" class="external-link">
            <?php if ($link['Icon']): ?><img src="<?php print $link['Icon']?>" /><?php
            endif; ?><?php print $link['Name']; ?>
            </a>
          </li>
          <?php } ?>
        </ul>
        </div>
        <?php } ?>

      <div class="result-line5">
        <?php
        if ($record->full_text_availability){
          echo '<a href="' . $fulltextUrl . '#html" class="icon html fulltext _record_link">';
          echo t('HTML full text');
          echo "</a>&nbsp; &nbsp;";
        }

         if ($record->pdf_availability){
          echo ' <a href="' . $pdfUrl . '" class="icon pdf fulltext">';
          echo t('PDF full text');
          echo "</a>";
         }
         ?>

      </div>
      </div>
    </div>
    <div class="clear"></div>
    </li>
  <?php endforeach; ?>
  </ol>
  <?php print $pager; ?>

<?php
  }
  elseif (!empty($lookfor)) {
    echo "<h2>" . t('Your search did not match any resources.') . "</h2>";
    // Check for autocomplete.
/*
    if (isset ($autoSuggestTerms)  ) {
      if (count($autoSuggestTerms)>0) {
        echo "Did you mean "."?<br/>";
        var_dump($autoSuggestTerms);
      }
    }
*/
    echo search_help('search#noresults', drupal_help_arg());
  }
?>

<div id="spinner" class="spinner"></div>
