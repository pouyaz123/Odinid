<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Profile\Info */
/* @var $dg \Base\DataGrid */
?>
<? $this->beginContent('Site.views.profile.editinfo.layout') ?>
<?= $dg ?>
<? require 'locations_addedit.php'; ?>
<? $this->endContent(); ?>
