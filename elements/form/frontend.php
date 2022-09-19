<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<div class="form-group">
    <?php if ($view->supportsLabel()) { ?>
        <label class="control-label"><?=$view->getLabel()?></label>
    <?php } ?>

    <?php if ($view->isRequired()) { ?>
        <span class="label label-info"><?=t('Required')?></span>
    <?php } ?>

    <?php $view->renderControl()?>
</div>
