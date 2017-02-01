<?php

/**
 * @file
 * Displays the basic search form block.
 *
 * @see template_preprocess_ebsco_basic_search()
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

<div class="container-inline">
  <?php 
	if ($search_view == 'basic'){ 
		echo  $search_form; 
	} 
	elseif ($search_view == 'advanced'){ 
  
		echo '<a href="'.url('ebsco/advanced', array('query' => array('edit' => 1))).'" class="small">'.t('Edit this Advanced Search')."</a> |";
		echo '<a href="'.url('ebsco/advanced').'" class="small">'.t('Start a new Advanced Search').'</a> |';
		echo '<a href="'.url('ebsco/results').'" class="small">'.t('Start a new Basic Search').'</a>';
		echo "<br/>";
		echo  t('Your search terms').": <strong>".check_plain($lookfor); "</strong>";
	} 
	?>
</div>
