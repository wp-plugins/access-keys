<?php
/*
Plugin Name: Access Keys
Plugin URI: http://anthologyoi.com/wordpress/plugins/wordpress-access-keys.html
Description: This plugin allows you to add Access Keys to Category and Page navigation menus to make your website far more accessible.
Author: Aaron Harun
Version: 0.6
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

if(is_home() || is_archive() || is_singular()){
	add_filter('wp_list_categories', 'access_keys_fix_links');
	
	if (!strpos($_SERVER['PHP_SELF'], 'wp-admin')){
		if (version_compare($wp_version, '2.3', '<')){
			add_filter('get_categories', 'access_keys_categories_add_key_wp21');
		}else{
			add_filter('get_terms', 'access_keys_categories_add_key',10,2);
		}
		
	}
}

function access_keys_categories_add_key_wp21($cats){
global $access_keys;
$cats_temp=array();
	foreach ($cats as $cat){
		if(strlen($access_keys['cat'][$cat->cat_ID])>0){
			$cat->cat_name .= 'accesskey=@'.$access_keys['cat'][$cat->cat_ID].'@';
		}
		$cats_temp[]=$page;
	}

$cats=$cats_temp; 
return $cats;

}

function access_keys_categories_add_key($cats,$is_cat){
global $access_keys;
	if($is_cat[0] == 'category'){
		$cats_temp=array();
		foreach ($cats as $cat){
			if(strlen($access_keys['cat'][$cat->term_id])>0){
				$cat->name .= 'accesskey=@'.$access_keys['cat'][$cat->term_id].'@';
				
			}
			$cats_temp[]=$cat;
		}
	
		$cats=$cats_temp; 
	}
return $cats;

}


// *******************************
// Add Page Access Keys
// *******************************
add_filter('wp_list_pages', 'access_keys_fix_links');
if (!strpos($_SERVER['PHP_SELF'], 'wp-admin')){
	if (version_compare($wp_version, '2.3', '<')){
		add_filter('get_pages', 'access_keys_pages_add_key21');
	}else{
		add_filter('get_pages', 'access_keys_pages_add_key');
	}
	
}
function access_keys_pages_add_keywp21($pages){
global $access_keys,$id;
$pages_temp=array();
if(!$id){
	foreach ($pages as $page){
		if(strlen($access_keys['page'][$page->ID])>0){
			$page->post_title .= 'accesskey=@'.$access_keys['page'][$page->ID].'@';
		}
		$pages_temp[]=$page;
	}
$pages=$pages_temp; 
}
	
return $pages;

}

function access_keys_pages_add_key($pages){
global $access_keys,$id;
$pages_temp=array();

	foreach ($pages as $page){
		if(strlen($access_keys['page'][$page->ID])>0){
			$page->post_title .= 'accesskey=@'.$access_keys['page'][$page->ID].'@';
		}
		$pages_temp[]=$page;
	}
$pages=$pages_temp; 

	
return $pages;

}


// *******************************
// Add Access Keys
// *******************************

function access_keys_fix_links($cats) {
	return preg_replace_callback('!<a[^>]*>[^>]*?(accesskey=@([^<]*)@)</a>!ims', 'access_keys_finish', $cats);
}

function access_keys_finish($matches){
	if($matches[1])
		$link = preg_replace('/accesskey=\@.*?\@/','',$matches[0]);
	
	if(!is_int($matches[2]) || $matches[2]>=0)
		$link = str_replace(' title=', ' accesskey="'.$matches[2].'" title=', $link);
	
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


<script type="text/javascript" src="../wp-includes/js/dbx.js"></script>
<script type="text/javascript" src="../wp-includes/js/tw-sack.js"></script>
<script type="text/javascript">
				//<![CDATA[
				addLoadEvent( function() {
					var manager = new dbxManager('inap');
					
					//create new docking boxes group
					var advanced = new dbxGroup(
						'advancedstuff', 		// container ID [/-_a-zA-Z0-9/]
						'vertical', 		// orientation ['vertical'|'horizontal']
						'10', 			// drag threshold ['n' pixels]
						'yes',			// restrict drag movement to container axis ['yes'|'no']
						'0', 			// animate re-ordering [frames per transition, or '0' for no effect]
						'yes', 			// include open/close toggle buttons ['yes'|'no']
						'open', 		// default state ['open'|'closed']
						'open', 		// word for "open", as in "open this box"
						'close', 		// word for "close", as in "close this box"
						'click-down and drag to move this box', // sentence for "move this box" by mouse
						'click to %toggle% this box', // pattern-match sentence for "(open|close) this box" by mouse
						'use the arrow keys to move this box', // sentence for "move this box" by keyboard
						', or press the enter key to %toggle% it',  // pattern-match sentence-fragment for "(open|close) this box" by keyboard
						'%mytitle%  [%dbxtitle%]' // pattern-match syntax for title-attribute conflicts
						);
				});
				//]]>
</script>
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