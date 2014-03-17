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
	Hi <?= $Name ?><br/>
	Your email address has been used for this username <b>"<?= $Name ?>"</b> on Odinid.com<br/>
	If it is right , to verify your email address on this user account :<br/>
	Please use this activation link : <?= \CHtml::link($CodeUrl, $CodeUrl) ?><br/>
	Or copy this activation code and use it in below activation page : <?= $Code ?><br/>
	<?= \CHtml::link($Url, $Url) ?><br/>
	<br/>
	Note : The activation link(code) has a short expiration time
</div>
<? $this->endContent(); ?>