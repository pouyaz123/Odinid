<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Profile\Additionals */
/* @var $dg \Base\DataGrid */
?>
<? $this->beginContent('Site.views.profile.editinfo.layout') ?>
<?= $dg ?>
<? require 'additionals_addedit.php'; ?>
<? $this->endContent(); ?>
