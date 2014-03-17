<?php
/* @var $this \Site\Components\BaseController */
/* @var $Code string */
/* @var $CodeUrl string */
/* @var $Url string */
/* @var $Name string */
/* @var $Email string */
?>
<? $this->beginContent('Site.views.layouts.emails'); ?>
<div>
	Your email has been used to recover the credentials of your Odinid user account , if you haven't tried to recover your account please forget this email.<br/>
	Your Username : "<?= $Name ?>"<br/>
	Set your new password through this code-link : <?= \CHtml::link($CodeUrl, $CodeUrl) ?><br/>
	Or copy this recovery code and use it in below recovery page : <?= $Code ?><br/>
	<?= \CHtml::link($Url, $Url) ?><br/>
	<br/>
	Note : The recovery link(code) has a short expiration time
</div>
<? $this->endContent(); ?>