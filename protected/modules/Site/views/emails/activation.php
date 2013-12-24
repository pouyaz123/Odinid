<?php
/* @var $this \Site\Components\BaseController */
/* @var $Code string */
/* @var $Url string */
?>
<?= Lng::Site('tr_user', 'Please use this activation link') . ' : ' . \CHtml::link($Url, $Url) ?>
<br/>
<br/>
<?= Lng::Site('tr_user', 'Or copy this activation code and post it through below activation page') . " : $Code" ?>
<br/>
<?= \CHtml::link(\Site\Consts\Routes::UserActivation, \Site\Consts\Routes::UserActivation) ?>
?>