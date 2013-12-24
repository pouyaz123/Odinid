<?php
/* @var $this \Site\Components\BaseController */
/* @var $Code string */
/* @var $Url string */
?>
<?=
	Lng::Site('tr_user', 'Please use this activation link {link} <br/> Or this activation code {code}'
			, array(
		'{link}' => \CHtml::link($Url, $Url),
		'{code}' => $Code
			)
	);
	?>