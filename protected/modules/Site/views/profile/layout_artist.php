<?php
/* @var $this Site\controllers\ProfileController */
/* @var $Model \Site\models\Profile\Info */
?>
<? if (\Site\models\User\Login::IsLoggedIn()): ?>
	<div><?= \CHtml::link(t2::Site_User('Edit basic info'), \Site\Consts\Routes::UserEditInfo()) ?></div>
<? endif; ?>
<div class="LeftCol">
	<div>
		<table>
			<tr>
				<td rowspan="2"><img/></td>
				<td>
					<h1><?= ucwords($Model->drUser->Username) ?></h1>
				</td>
				<td>
					<span><?= $Model->drUser->UserType ?></span>
					<span><?= $Model->drInfo->ArtistTitle ?></span>
				</td>
			</tr>
			<tr>
				<td>
					<?=
					$drCurrentLocation = $Model->drCurrentLocation ? \t2::Site_User('User location', array(
						'{City}' => $drCurrentLocation['City'],
						'{Division}' => !is_numeric($drCurrentLocation['DivisionCode']) ?
								$drCurrentLocation['DivisionCode'] : $drCurrentLocation['Division'],
						'{Country}' => $drCurrentLocation['GeoCountryISO2']? : $drCurrentLocation['Country'],
					)) : ""
					?>
				</td>
				<td>
					<? foreach ($Model->dtWebAddr as $drWebAddr): ?>
						<? if ($drWebAddr['WebAddress']): ?>
							<div><?= \CHtml::link($drWebAddr['WebAddress'], $drWebAddr['WebAddress']) ?></div>
							<? break; ?>
						<? endif; ?>
					<? endforeach; ?>
				</td>
			</tr>
		</table>
		<div>
			social items
		</div>
	</div>
	<div>
		Profile navigator
	</div>
	<div>
		content below
		<div style="clear: both"><?= $content; ?></div>
	</div>
</div>
<div class="RightCol">
	<div>
		<div>
			<? \t2::Site_User('Following') ?>
			<div>
				following count here
			</div>
		</div>
		<div>
			<? \t2::Site_User('Followers') ?>
			<div>
				Followers count here
			</div>
		</div>
		<div>
			<? \t2::Site_User('Favorites') ?>
			<div>
				Favorites count here
			</div>
		</div>
	</div>
	<div>
		♥ Likes 5k
	</div>
	<div>
		Profile views 2.1k
	</div>
	<div>
		Share
	</div>
	<div>
		Add to favorites
	</div>
	<div>
		<div>About</div>
		<?= $Model->drInfo->SmallDesc ?>
	</div>
	<div>
		<div>Skills</div>
		<div>item1</div>
		<div>item2</div>
		<div>item3</div>
	</div>
	<div>
		<div>Experiences</div>
		<div>item1</div>
		<div>item2</div>
		<div>item3</div>
	</div>
	<div>
		<div>Educations</div>
		<div>item1</div>
		<div>item2</div>
		<div>item3</div>
	</div>
</div>