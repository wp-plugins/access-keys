<?php
/*
Plugin Name: Access Keys
Plugin URI: http://anthologyoi.com/wordpress/plugins/wordpress-access-keys.html
Description: This plugin allows you to add Access Keys to Category and Page navigation menus to make your website far more accessible.
Author: Aaron Harun
Version: 1.0
Author URI: http://anthologyoi.com/
*/

/*
Installation:
ONE: Upload to your plugin directory.
TWO: Activate.
THREE: Go to the Admin Menu under Manage named Access Keys.
FOUR: Read the instructions on the Admin Panel.

*/
 $access_keys = get_option('access_keys');

// *******************************
// Add Category Access Keys
// *******************************
add_filter('wp_list_categories', 'access_keys_cats');

function access_keys_cats($cats) {
		return preg_replace_callback('!(<li class="cat-item (cat)-item-([0-9]*)">[\s\S]*?<a([^>]*)>)!ims', 'access_keys_finish', $cats);
}

// *******************************
// Add Page Access Keys
// *******************************
add_filter('wp_list_pages', 'access_keys_pages');

function access_keys_pages($pages) {
	return preg_replace_callback('!(<li class="page_item (page)-item-([0-9]*)"><a([^>]*)>)!ims', 'access_keys_finish', $pages);
}

// *******************************
// Add Access Keys
// *******************************


function access_keys_finish($matches){
global $access_keys;
	$id = $matches[3];
	$link = $matches[0];
	if($access_keys[$matches[2]][$id]){
		$accesskey = $matches[4]. ' accesskey="'.$access_keys[$matches[2]][$id].'" ';
		$link = str_replace($matches[4], $accesskey, $link);
	}
return $link;
}


// *******************************
// Admin Panel
// *******************************
function access_keys_update_options($options){
global $access_keys;
	while (list($option, $value) = each($options)) {
			$access_keys[$option] =$value;
	}
return $access_keys;
}


add_action('admin_menu', 'access_keys_menu');

function access_keys_menu() {
	add_submenu_page('edit.php', 'Access Keys', 'Access Keys', 8, __FILE__,'access_keys_admin');
}

function access_keys_admin(){
global $access_keys;

if ($_POST["action"] == "saveconfiguration") {
			access_keys_update_options($_REQUEST['access_keys']);
			update_option('access_keys',$access_keys);
			$message .= 'Access Keys Updated.<br/>';

		//if we don't the panel will show old value...which may scare people.
		//$inapall doesn't need to be updated because it has the new values added to it immediately
	}
$pages = get_pages();
$cats = get_categories();
$pagesnokey = array();
$pagesyeskey = array();
$catsyeskey= array();
$catsnokey = array();

	foreach($pages as $page){
		if($access_keys['page'][$page->ID] != ''){
			$pagesyeskey[] = array($page->ID,$page->post_title,$access_keys['page'][$page->ID]);
		}else{
			$pagesnokey[] = array($page->ID,$page->post_title);
		}
	}

	foreach($cats as $cat){
		if($access_keys['cat'][$cat->cat_ID] != ''){
			$catsyeskey[] = array($cat->cat_ID,$cat->cat_name,$access_keys['cat'][$cat->cat_ID]);
		}else{
			$catsnokey[] = array($cat->cat_ID,$cat->cat_name);
		}
	}
?>
<div class="wrap">
<form method="post">
<h2>Access Keys Configuration</h2>

<div id="advancedstuff" class="dbx-group" >
	<div class="dbx-b-ox-wrapper">
	<fieldset id="instructions" class="dbx-box">
		<div class="dbx-h-andle-wrapper"><h3 class="dbx-handle">Instructions</h3></div>
			<div class="dbx-c-ontent-wrapper">
				<div class="dbx-content">
<p>Use of this plugin is simple. On the the first box lists pages and categories with a current access key, and the second box lists the pages and categories without access keys.</p>
<p>To add an access key to a page or category just type an access key in its text box and then click the save button. The categories or pages will now move to the top list.</p>
<p>To remove an access key from a page or category just remove the access key from its text box and click the save button. The category or page will now move to the second box.</p>
			</div>
		</div>
	</fieldset>
</div>
<div class="dbx-b-ox-wrapper">
	<fieldset id="accesskeys" class="dbx-box">
		<div class="dbx-h-andle-wrapper"><h3 class="dbx-handle">Current Access Keys</h3></div>
			<div class="dbx-c-ontent-wrapper">
				<div class="dbx-content">Pages:
				<ul>
<?php foreach($pagesyeskey as $x){
echo <<<block
    <li><p>
		<label>Access Key for $x[1] <input type="text" maxlength="1" value="$x[2]" name="access_keys[page][$x[0]]"></label>
    </p></li>
block;
}?>

</ul>

Categories:
				<ul>
<?php foreach($catsyeskey as $x){
echo <<<block
    <li><p>
		<label>Access Key for $x[1] <input type="text" maxlength="1" value="$x[2]" name="access_keys[cat][$x[0]]"></label>
    </p></li>
block;
}?>

</ul>
			</div>
		</div>
	</fieldset>
</div>

<div class="dbx-b-ox-wrapper">
	<fieldset id="noaccesskeys" class="dbx-box">
		<div class="dbx-h-andle-wrapper"><h3 class="dbx-handle">Current Access Keys</h3></div>
			<div class="dbx-c-ontent-wrapper">
				<div class="dbx-content">Pages:
				<ul>
<?php foreach($pagesnokey as $x){
echo <<<block
    <li><p>
					<label>Access Key for $x[1] <input type="text" maxlength="1" name="access_keys[page][$x[0]]"></label>
    </p></li>
block;
}?>

</ul>

Categories:
				<ul>
<?php foreach($catsnokey as $x){
echo <<<block
    <li><p>
					<label>Access Key for $x[1] <input type="text" maxlength="1" name="access_keys[cat][$x[0]]"></label>
    </p></li>
block;
}?>

</ul>
			</div>
		</div>
	</fieldset>
</div>



</div>


			<input type="hidden" name="action" value="saveconfiguration">
			<input type="submit" value="Save" style="width:100%;" >
		</form>


<br/><br/>
Have you found this Plugin useful?<br/>
If this Plugin has helped you, isn't it worth a little bit of time or money? <strong>If you are feeling monetarily generous make a donation</strong>.<br/> <strong>How much is entirely up to you</strong>, but numbers with lots of 0's, a 1 on the left and a decimal point on the right are the best kind. =D
<span style="text-align:center;">
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="admin@anthologyoi.com">
<input type="hidden" name="item_name" value="Donation For Access Keys">
<input type="hidden" name="no_shipping" value="2">
<input type="hidden" name="note" value="1">
<input type="hidden" name="currency_code" value="USD">
<input type="hidden" name="tax" value="0">
<input type="hidden" name="lc" value="US">
<input type="hidden" name="bn" value="PP-DonationsBF">
<input type="image" src="http://www.paypal.com/en_US/i/btn/x-click-but04.gif" border="0" name="submit" alt="Make a donation with PayPal - it's fast, free and secure!">
<img alt="" border="0" src="http://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form></span>
<br/>
Or if circumstances make a donation impossible, <em>links, refferals and comments are appreciated</em>.

<?php
}
?>