<?php defined('DATALIFEENGINE') or die('No direct script access.');
/**
 * Author: Gerasimov Ilya (hip)
 * Github: https://github.com/Omashu/dle-twping
 */

if(!$user_group[$member_id['user_group']]['admin_banners'])
{
	msg("error", $lang['index_denied'], $lang['index_denied']);
}

$page = max(1, (int)(isset($_GET["page"])?$_GET["page"]:1));
$per_page = max(1, (int)(isset($_GET["per_page"])?$_GET["per_page"]:100));

require ENGINE_DIR . "/modules/twping/load.php";
$twping = Twping_Twping::instance();

echoheader("dle-twping", "Auto ping in twitter");

echo <<<HTML
<div style="padding-top:5px;padding-bottom:2px;">
	<table width="100%">
	<tr>
		<td width="4"><img src="engine/skins/images/tl_lo.gif" width="4" height="4" border="0"></td>
		<td background="engine/skins/images/tl_oo.gif"><img src="engine/skins/images/tl_oo.gif" width="1" height="4" border="0"></td>
		<td width="6"><img src="engine/skins/images/tl_ro.gif" width="6" height="4" border="0"></td>
	</tr>
	<tr>
		<td background="engine/skins/images/tl_lb.gif"><img src="engine/skins/images/tl_lb.gif" width="4" height="1" border="0"></td>
		<td style="padding:5px;" bgcolor="#FFFFFF">
			<table width="100%">
				<tr>
					<td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">README</div></td>
				</tr>
			</table>
			<div class="unterline"></div>
			<table width="100%">
				<tr>
					<td style="padding:2px;">
						<b>Author:</b> Gerasimov Ilya<br/>
						<b>GitHub:</b> <a target="_blank" href="https://github.com/Omashu/dle-twping" title="https://github.com/Omashu/dle-twping">https://github.com/Omashu/dle-twping</a><br/>
						<b>Version:</b> 1.0
					</td>
				</tr>
			</table>
		</td>
		<td background="engine/skins/images/tl_rb.gif"><img src="engine/skins/images/tl_rb.gif" width="6" height="1" border="0"></td>
	</tr>
	<tr>
		<td><img src="engine/skins/images/tl_lu.gif" width="4" height="6" border="0"></td>
		<td background="engine/skins/images/tl_ub.gif"><img src="engine/skins/images/tl_ub.gif" width="1" height="6" border="0"></td>
		<td><img src="engine/skins/images/tl_ru.gif" width="6" height="6" border="0"></td>
	</tr>
</table>
HTML;

// запрашиваем записи
$count = 0;
$results = $twping->select($page, $per_page, $count);

echo <<<HTML
<div style="padding-top:5px;padding-bottom:2px;">
	<table width="100%">
	<tr>
		<td width="4"><img src="engine/skins/images/tl_lo.gif" width="4" height="4" border="0"></td>
		<td background="engine/skins/images/tl_oo.gif"><img src="engine/skins/images/tl_oo.gif" width="1" height="4" border="0"></td>
		<td width="6"><img src="engine/skins/images/tl_ro.gif" width="6" height="4" border="0"></td>
	</tr>
	<tr>
		<td background="engine/skins/images/tl_lb.gif"><img src="engine/skins/images/tl_lb.gif" width="4" height="1" border="0"></td>
		<td style="padding:5px;" bgcolor="#FFFFFF">
			<table width="100%">
				<tr>
					<td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">Count all: <b>{$count}</b> </div></td>
				</tr>
			</table>
			<div class="unterline"></div>
			<table width="100%">
				<tr>
					<td style="padding:2px;">

						<div class="dle-twping-logs">
HTML;
$i = 1;

foreach ($results as $row)
{
	$i++;
	$bg = $i%2===1?"background:#f1f1f1;":"";
echo <<<HTML
	<div style="overflow:hidden;{$bg}padding:5px;">
		<div style="float:left;">({$row["twping_target_type"]}/{$row["twping_target_id"]}) <b>{$row["target"]["title"]}</b>  / {$row["twping_service"]}:{$row["twping_account"]}</div>
		<div style="float:right;">{$row["twping_date_push"]}</div>
	</div>
HTML;
}

echo <<<HTML
						</div>
					</td>
				</tr>
			</table>
		</td>
		<td background="engine/skins/images/tl_rb.gif"><img src="engine/skins/images/tl_rb.gif" width="6" height="1" border="0"></td>
	</tr>
	<tr>
		<td><img src="engine/skins/images/tl_lu.gif" width="4" height="6" border="0"></td>
		<td background="engine/skins/images/tl_ub.gif"><img src="engine/skins/images/tl_ub.gif" width="1" height="6" border="0"></td>
		<td><img src="engine/skins/images/tl_ru.gif" width="6" height="6" border="0"></td>
	</tr>
</table>
HTML;

echofooter();