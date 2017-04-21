<?php

/**
 * @file
 * Default theme implementation for displaying an EBSCO result.
 *
 * @see template_preprocess_ebsco_result()
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
?>

<?php
    if ($record) {

        $id          = check_plain($record->record_id());
        $fulltextUrl = url('ebsco/fulltext', array('query' => array('id' => $id)));
        $pdfUrl      = url('ebsco/pdf', array('query' => array('id' => $id)));
?>

        <div class="ebsco-record">
            <h1><?php print $record->title; ?></h1>

            <div class="record-toolbar">
                <?php
                    if ($last_search){
                    ?>
                      <div class="floatright">
                        <?php
                            if ($last_search['previous']){
                                echo '<a href="' . url('ebsco/result', array('query' => array('id' => $last_search['previous'], 'op' => 'Previous'))) . '" class="_record_link">&laquo; ' . t('Previous') . '</a>';
                            }
                            echo "#";
                            print $last_search['current_index']; ?> of <?php print $last_search['count'];

                            if ($last_search['next']){
                                echo '<a href="' . url('ebsco/result', array('query' => array('id' => $last_search['next'], 'op' => 'Next'))) . '" class="_record_link">' . t('Next') . ' &raquo;</a>';

                            }
                        ?>
                      </div>
                      <div class="floatleft">
                        <a href="<?php print "?{$last_search['query']}"; ?>">
                          &laquo; <?php print t('Back to Results list'); ?>
                        </a>
                      </div>
                      <div class="clear"></div>
                <?php
                    }
                    ?>
            </div>

            <div class="span-5">
              <ul class="external-links">
                <?php
                    if($record->p_link){
                        echo "<li> <a href='" . $record->p_link . "'>" . t('View in EDS') . "</a></li>";
                    }

                    if ($record->pdf_link){
                        echo '
						  <li>
							<a href="' . $pdfUrl . '" class="icon pdf fulltext">' . t('PDF full text') . '</a>
						  </li>';
                    }

                    if ($record->full_text_availability){
                        echo '
						  <li>
							<a href="' . ((!user_is_logged_in()) ? $fulltextUrl : "") . '#html" class="icon html fulltext">' . t('HTML full text') . '</a>
						  </li>';
                    }

                    if (!empty($record->custom_links)){
                        foreach ($record->custom_links as $link){
                            echo '
								<li>
									<a href="' . $link['Url'] . '" target="_blank" title="' . $link['MouseOverText'] . '" class="external-link">' . ($link['Icon']) ? '<img src="' . $link['Icon'] . '" />' : '' . $link['Name'] . '</a>
								</li>';
                        }
                    }
                ?>
              </ul>
            </div>

            <div class="span-13">
              <table cellpadding="2" cellspacing="0" border="0" class="citation" summary="<?php print t('Bibliographic Details'); ?>">
                <?php
                    foreach ($record->items as $item){
                        if (!empty($item['Data'])){
                            echo '
							  <tr valign="top">
								<th width="150">' . t($item['Label']) . ':</th>
								<td>' . auto_link($item['Data']) . '</td>
							  </tr>';
                        }
                    }

                    if ($record->db_label){
                        echo '
						  <tr valign="top">
							<th width="150">' . t('Database') . ':</th><td>' . check_plain($record->db_label) . '</td>
						  </tr>';
                    }

                    if ($record->full_text){
                        echo '
						  <tr id="html" valign="top">
							<td colspan="2" class="html">' . $record->full_text . '</td>
						  </tr>';
                    }
                    elseif ($record->access_level && !user_is_logged_in()){
                        echo '
						  <tr id="html" valign="top">
							<td colspan="2" class="html">
							  <p>' . t('The full text cannot be displayed to guests.') . '<br />.';
                                    $link = '<a href="' . url('user') . '">' . t('Login') . '</a>';
                                    echo '<strong>' . sprintf(t('%s for full access.'), $link) . '</strong>
							  </p>
							</td>
						  </tr>
						  ';
                    }
                    ?>
              </table>
            </div>

            <div class="span-4">
              <?php
                if ($record->medium_thumb_link){
                    echo '<img src="' . check_url($record->medium_thumb_link) . '" class="book-jacket" alt="' . t('Book jacket') . '"/>';
                }

                if ($record->publication_type){
                    echo '<p>' . check_plain($record->publication_type) . '</p>';
                }
                ?>
            </div>

            <div class="clear"></div>

        </div>
<?php
    }
?>

<div id="spinner" class="spinner"></div>
