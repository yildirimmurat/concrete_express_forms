<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>
<div class="field">
    <?php
    print $form->text(
        $this->field('value'),
        $value,
        [
            'placeholder' => $this->akTextPlaceholder
        ]
    );

    ?>
</div>
