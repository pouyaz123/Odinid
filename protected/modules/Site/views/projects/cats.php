<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Projects\Categories */
/* @var $dg \Base\DataGrid */
?>
<? $this->beginContent('Site.views.profile.editinfo.layout') ?>
<?= $dg ?>
<? require 'cats_addedit.php'; ?>
<? $this->endContent(); ?>
