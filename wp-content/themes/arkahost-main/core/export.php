<?php
/*
	(c) king-theme.com
*/

global $king, $wpdb;

$export = $king->export_options();

$file = THEME_PATH.DS.'core'.DS.'sample'.DS.'data'.DS.'widgets.export.txt';
$fp = $king->ext['fo']( $file, 'w');

$king->ext['fw']( $fp, $export );
$king->ext['fc']( $fp );

echo '<strong style="color:green">Export succesful</strong><br /> Data stored in the file <i>www/wp-contents/themes/'.strtolower(THEME_NAME).'/core/sample/data/widgets.export.txt</i>';
echo '<br /><br />';
echo '<strong>Ho To import</strong><br />  -step 1: copy your backup file under name <strong>"widgets.export.txt"</strong> into folder www/wp-contents/themes/'.strtolower(THEME_NAME).'/core/sample/ ';
echo '<br />';
echo '-step 2: Run a link <strong>'.SITE_URI.'/wp-admin?devn=import</strong> ';
echo '<br /><br />';
echo '<a href="'.SITE_URI.'/wp-admin">Go Back</a>';
exit;
