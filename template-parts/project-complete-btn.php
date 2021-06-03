<?php

    if(!isset($_GET['enabler'])){
        return;
    }
    $done   = $args['done'];
    $text   = $done ?  __('Save Success Factor' ,'uni') : __('Please Fill Missed Data' ,'uni');
    $class  = $done ? 'button-primary button-large' : 'button-primary button-large';
    $id     = 'id="factor-complete-btn" ';
    $enable = $done ? '' : 'disabled';
?>
<style>
    .factor-complete-btn{
        margin-left: 10px;
    }
    .disabled{
        background-color: grey !important;
    }
</style>
<input <?= $id ?> type="submit" class="acf-button button  <?= $class ?> factor-complete-btn   <?= $enable ?> " value="<?= $text ?> "  <?= $enable ?> />

<script>

</script>