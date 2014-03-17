<?php
/* @var $this Site\controllers\UserController */
?>
<?= $content; ?>
<?= CHtml::link(t2::Site_User('Register'), Site\Consts\Routes::UserRegister) ?> | 
<?= CHtml::link(t2::Site_User('Resend Activation Link'), Site\Consts\Routes::UserResendActivation) ?> | 
<?= CHtml::link(t2::Site_User('Login'), Site\Consts\Routes::UserLogin) ?> | 
<?= CHtml::link(t2::Site_User('Recovery'), Site\Consts\Routes::UserRecovery) ?>