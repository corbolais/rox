<?php
$User = APP_User::login();

$i18n = new MOD_i18n('apps/forums/board.php');
$boardText = $i18n->getText('boardText');
$words = new MOD_words();
?>

<div id="title">
  <h1><?php echo $words->getFormatted('ForumTitle'); ?></h1>
</div>
<div id="forums_introduction" class="note">
  <?php echo $words->getFormatted('ForumIntroduction'); ?>
</div>
