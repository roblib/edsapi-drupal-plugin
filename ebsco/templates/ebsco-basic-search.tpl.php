<?php

/**
 * @file
 * Displays the basic search form block.
 *
 * @see template_preprocess_ebsco_basic_search()
 */
?>

<div class="container-inline">
  <?php if ($search_view == 'basic'): ?>
    <?php print $search_form; ?>
  <?php elseif ($search_view == 'advanced'): ?>
    <a href="<?php print url('ebsco/advanced', array('query' => array('edit' => 1))); ?>" class="small">
      <?php print t('Edit this Advanced Search'); ?>
    </a> |
    <a href="<?php print url('ebsco/advanced'); ?>" class="small">
      <?php print t('Start a new Advanced Search'); ?>
    </a> |
    <a href="<?php print url('ebsco/results'); ?>" class="small">
      <?php print t('Start a new Basic Search'); ?>
    </a>
    <br/>
    <?php print t('Your search terms'); ?> : <strong><?php print check_plain($lookfor); ?></strong>
  <?php endif; ?>
</div>
