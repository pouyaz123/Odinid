<?php
/* @var $this Site\controllers\UserController */
?>
<?= $content; ?>
<?= CHtml::link(t2::site_site('Register'), Site\Consts\Routes::UserRegister) ?> | 
<?= CHtml::link(t2::site_site('Resend Activation Link'), Site\Consts\Routes::UserResendActivation) ?> | 
<?= CHtml::link(t2::site_site('Login'), Site\Consts\Routes::UserLogin) ?> | 
<?= CHtml::link(t2::site_site('Recovery'), Site\Consts\Routes::UserRecovery) ?>