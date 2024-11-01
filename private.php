<?php
/*
/**
 * @package sprivate_site
 * @version 2.1 
 */
/*
Plugin Name: Sprivate
Plugin URI: http://www.intogeek.com/sprivate/
Description: Very simple plugin that sends users to a login page if they are not already logged in. All content on the site is kept private, but the login page is displayed within your active theme. Comment feeds are completely blocked unless authenticated, but you have the option show only titles on post feeds.
Author: Shames
Version: 2.1 
Author URI: http://intogeek.com
*/

//Protect RSS
add_action('pre_get_posts','blockage_rss');

//Protect all other pages
add_action('get_header', 'verify_authenticatej');

//Non authenticated RSS -- redirect to login page IF titles aren't enabled. If titles are enabled then RSS feeds will show post titles only.
function blockage_rss($query){
		$options = get_option('sprivate_options');
		if ($query->is_feed) {
			//show only titles on Feed pages
			if($options['rss_titles'] == 'yes'){
				add_filter('the_content', create_function('','return\'\';'));
			//don't show feeds at all if titles are not enabled.
			}else{
				verify_authenticatej();
			}
		}
		//hide ALL comment feeds
		if ($query->is_comment_feed()){
			verify_authenticatej();
		}	
}
//Redirect if not already logged in
function no_authenticatej(){
	$options = get_option('sprivate_options');
	wp_redirect($options['redirect_url'] . "?referred=" . $_SERVER['REQUEST_URI'], 302);
    	exit();	
}

//Check to see if the user is already logged in
function verify_authenticatej(){
	$options = get_option('sprivate_options');
	if(!($options['active'] != 'yes')){
		if(!is_user_logged_in()){
			//Check to see if the visitor is accessing one of the 'allowed' pages
			$allowed_URL_arr = explode(",",$options['allowed_url']);
			$goodToGo = false;
			foreach($allowed_URL_arr as $aurls_sp){
				if($_SERVER['REQUEST_URI'] == $options['redirect_url'] || $_SERVER['REQUEST_URI'] == $options['redirect_url'] . "?referred=" . $_GET['referred'] || @strpos($_SERVER['REQUEST_URI'],$aurls_sp)){
					$goodToGo = true;
				}
				else{
				}
			}
			if($goodToGo){
		
			}else{	
				//If this is not an 'allowed' page, redirect them to the authentication page.
				no_authenticatej();
			}
		}else{
			//If they are already logged in, and they're trying to access the login page, redirect them to the homepage
			if($_SERVER['REQUEST_URI'] == $options['redirect_url']){
				wp_redirect('/', 302);
			}
			//If a requested URL is found, redirect the user to that URL.
			if(isset($_GET['referred'])){
				wp_redirect($_GET['referred'], 302);				
			}
			
		}
	}
}
function sprivate_refurl(){
	wp_redirect($_GET['referred']);
}

//Shortcodes creation
//Shortcode for the login form
add_shortcode( 'Sprivate-login-form', 'Sprivate_login_form_shortcode' );
add_shortcode('posts','posts_list');

function Sprivate_login_form_shortcode() {
	if ( is_user_logged_in() )
		return  '<span style="color:#FFFFFF;background-color:#00CC00;padding:2px 2px 2px 2px"><b>You are already logged in.</b></span>';

	return wp_login_form( array( 'echo' => false ) );
}

//Shortcode for the list of posts
function posts_list () {
	$options = get_option('sprivate_options');
        $output='<ul>';
	if(isset($options['num_posts'])){
		$numo_posts = $options['num_posts'];
	}
	else{
		$numo_posts = '5';
	}
	$args = array( 'numberposts' => $numo_posts);
        $posts = get_posts($args);
        foreach($posts as $post){
                $permalink = get_permalink( $post->ID );
                $output.= '<li>' . '<a href="' . $permalink . '">' . $post->post_title . '</a></li>';
        }
        $output.='</ul>';
        return $output;
}

//Start Administration Menu contents
add_action('admin_menu', 'Sprivate_admin');

function Sprivate_admin(){
add_options_page('Sprivate Options', 'Sprivate', 'manage_options', 'sprivate-plugin', 'sprivate_options');
}

function sprivate_options() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	echo '<div class="wrap">';
	echo '<h2>Sprivate plugin options</h2>';
	;?>
	<p><b>Description:</b>This plugin is designed to allow you to send unauthenticated users to a simple login page on your site. Both your site pages, and RSS feeds are not accessible to anyone that has not logged into the site using a valid username and password.</p>
	<br />
	<p><b>Usage: </b>Before setting the "Redirect to URL" setting below I would recommend creating the page you want people to be redirected to first.</p>
	<p>I typically just create a page with the title "login", but you can use whatever title you want. <b>Just make sure you add the shortcode: [Sprivate-login-form] </b>to that page so the login form is displayed properly. </p>
	<p>After you have the page created, with the shortcode inserted; go ahead and visit the page you created and take note of the URL showing in the address bar. You will enter a portion of this into the field below to make the plugin work properly.</p><hr /><br />
	<p>Remember you only want to enter the part of the URL that shows <b>after the main part of your domain</b>. For example my own page URL was: http://myDomain.com/login/ so, in the "Redirect to URL" field below I simply entered: /login/  </p>
<form action="options.php" method="post">
<?php settings_fields('sprivate_options'); ?>
<?php do_settings_sections('sprivate'); ?>

<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
</form>
<?php do_settings_sections('sprivate2'); ?>
</div>
<?}
add_action('admin_init', 'sprivate_admin_init');
function sprivate_admin_init(){
register_setting( 'sprivate_options', 'sprivate_options', 'sprivate_options_validate' );
add_settings_section('sprivate_main', '', 'sprivate_section_text', 'sprivate');
add_settings_field('sprivate_redirect_url', 'Redirect to URL', 'sprivate_setting_string', 'sprivate', 'sprivate_main');
add_settings_field('sprivate_allowed_url', 'Allowed URLs', 'sprivate_allowed_url','sprivate','sprivate_main');
add_settings_field('sprivate_num_posts', 'Number of post titles to display through [posts] shortcode.', 'sprivate_num_posts', 'sprivate', 'sprivate_main');
add_settings_field('sprivate_active', 'Activate Sprivate?', 'sprivate_active_radio', 'sprivate', 'sprivate_main');
add_settings_field('rss_titles', 'Enable RSS Titles?', 'rss_show_titles', 'sprivate', 'sprivate_main');
add_settings_section('sprivate_faq', '<br /><hr>', 'sprivate_faq_text', 'sprivate2');
}
function sprivate_section_text() {
echo '';
}
function sprivate_faq_text(){
	echo "<h2>F.A.Q.</h2>
        <p><b>The 'login' page I created is showing in my navigation bar of my site! I don't want it there.</b></p>
        <p>To prevent this you simply need to create a menu from appearance --> menus on your wp dasboard. Be sure you add only the pages you want shown in the navigation to your new menu, and be sure to save it as the primary navigation menu for your site to use.</p><br />
        <p><b>Why is the plugin named 'Sprivate'?</b></p>
        <p>I'm a plugin developer not a poem writer! I know the name isn't exactly descriptive or nice to look at. The 'S' I guess stands for 'Simple' though many might not agree that it is 'simple' to use. So you can think of the 'S' as standing for 'Slick', or 'Stupid' depending on your own opinion of the plugin.<p>
<br />
<p><b>What is the [posts] shortcode?</b></p>
<p>This was created to allow you to display any number of post titles that you want on any of your pages. However, the shortcode is intended to be used on the same page aso your [Sprivate-login-form] shortcode so that a 'preview' of the posts is listed on that page. This way your users know that you have posted new content just by looking at the login page. The content of these posts is still protected from the view of unauthenticated users. The use of this shortcode is optional.</p>
<br />
<p><b>I activated sprivate, but it is not redirecting users to the login page!</b></p>
<p>You should be able to login to the wp-admin area of  your site still. After you are logged in go to the settings --> sprivate area and be sure you read and follow the instructions listed just above the 'Redirect to URL' setting.</p><br />
<hr>
<p><b>Need help, or have questions about this plugin? Feel free to <a href='http://www.intogeek.com/contact-me/' target='_blank'>Contact Me</a>. I will reply to you at my earliest convenience.</b></p>
";
} 

//Input box for redirect URL
function sprivate_setting_string() {
$options = get_option('sprivate_options');
echo "<input id='sprivate_redirect_url' name='sprivate_options[redirect_url]' size='40' type='text' value='{$options['redirect_url']}' />";
}
//Active or Inactive radio button
function sprivate_active_radio() {
echo "<p><b>WARNING!</b> do not activate until you have your login page created, and the Redirect to URL setting st properly as described above.</p>";
echo "<p>The site will NOT be private until you do activate it below and save the settings.</p>";
$options = get_option('sprivate_options'); ?> 
<input type='radio' name='sprivate_options[active]' value='yes'<?php if($options['active'] == 'yes') echo "checked"; ?> /> Yes<br />
<input type='radio' name='sprivate_options[active]' value='no' <?php if($options['active'] != 'yes') echo "checked"; ?> /> No
<?php
}
//Number of posts titles to show on the login page
function sprivate_num_posts(){
$options = get_option('sprivate_options'); 
echo "The optional [posts] shortcode is intended to be used on the same page as the [Sprivate-login-form] shortcode so the titles of the latest posts are displayed on the login page.";
?>
<input type="text" name="sprivate_options[num_posts]" id="posts_num" value="<?php echo $options['num_posts']?>" />
<?php echo "Default is 5";
}
//allowed_url
function sprivate_allowed_url() {
$options = get_option('sprivate_options');
echo "List of allowed urls excluding the login page. Do not unclude leading slash, separate each by a comma.Leave blank if you don't want to allow any URLs for non-authenticated users. <br>Example: url_one/,url_two/,url_three/<br>";?>
<input type="text" size="40" name="sprivate_options[allowed_url]" id="url_allowed" value="<? echo $options['allowed_url']?>" />
<?php
}
//RSS feed titles enable/disable
function rss_show_titles(){
$options = get_option('sprivate_options');
echo "You can set this to 'yes' if you want your RSS feeds to show titles of your posts. If you don't want peole to see your RSS feeds (even titles) unless logged in, set this to 'no'<br>"; ?>
<input type='radio' name='sprivate_options[rss_titles]' value='yes'<?php if($options['rss_titles'] == 'yes') echo "checked"; ?> /> Yes<br />
<input type='radio' name='sprivate_options[rss_titles]' value='no' <?php if($options['rss_titles'] != 'yes') echo "checked"; ?> /> No
<?php
}

// validate our options
function sprivate_options_validate($input){
$num_posts = trim($input['num_posts']);
if(preg_match("/^[0-9]{1,2}$/",$num_posts)){
	$newinput['num_posts'] =  $num_posts;
}else{
	$newinput['num_posts'] = '5';
}
$newinput['active'] = trim($input['active']);
$newinput['redirect_url'] = trim($input['redirect_url']);
$newinput['allowed_url'] = trim($input['allowed_url']);
$newinput['rss_titles']  = trim($input['rss_titles']);
return $newinput;
}
//End Administration menu contents. 
?>
