=== Plugin Name ===
Contributors: shames0
Tags: Private, custom login, lockdown
Requires at least: 3.3
Tested up to: 3.8
Stable tag: 2.1

Simple Easy Private site/blog plugin.

== Description ==

This plugin is very simple. All it does is keep your RSS feeds and main blog content private. If someone tries to visit one of your site pages without logging in as a valid user first then they are redirected to the login page that you specify. All steps required to get the plugin setup are shown in your Wordpress Dashboard under the settings --> sprivate menu after you have the plugin installed and activated. 

== Installation ==
Simply copy the extracted plugin files into your wp-content/plugins folder and you can activate it from there. 
After you have the plugin installed you can configure it from your wp-admin
--> settings --> sprivate page.

== Frequently Asked Questions ==

<b>The 'login' page I created is showing in my navigation bar of my site! I don't want it there.</b>

To prevent this you simply need to create a menu from appearance --> menus on your wp dasboard. Be sure you add only the pages you want shown in the navigation to your new menu, and be sure to save it as the primary navigation menu for your site to use.


<b>Why is the plugin named 'Sprivate'?</b>

I'm a plugin developer not a poem writer! I know the name isn't exactly descriptive or nice to look at. The 'S' I guess stands for 'Simple' though many might not agree that it is 'simple' to use. So you can think of the 'S' as standing for 'Slick', or 'Stupid' depending on your own opinion of the plugin.

<b>What is the [posts] shortcode?</b>

This was created to allow you to display any number of post titles that you
want on any of your pages. However, the shortcode is intended to be used on
the same page aso your [Sprivate-login-form] shortcode so that a 'preview' of
the posts is listed on that page. This way your users know that you have
posted new content just by looking at the login page. The content of these
posts is still protected from the view of unauthenticated users. The use of
this shortcode is optional.


<b>I activated sprivate, but it is not redirecting users to the login
page!</b>

You should be able to login to the wp-admin area of your site still. After you
are logged in go to the settings --> sprivate area and be sure you read and
follow the instructions listed just above the 'Redirect to URL' setting.
== Changelog ==
2.1
*Fixes bugs that were introduced in 2.0. Including the strpos 'empty
delimeter' error that was crashing some sites, and "call_user_func_array()
expects parameter 1 to be a valid callback, function" showing up in some
error_log files.

2.0
*Added security enhancements to RSS feeds.<br />
*Now includes the option to allow unauthenticated users to visit specified
pages other than the login page.<br />
*Option to show titles of posts on RSS feeds so 'feed readers' can tell when
you've updated your site.<br />

1.5.1
*Changed plugin site link.

1.5
*Added [posts] shortcode that can be used anywhere on the site, but it's
mainly designed as an option to be added to the login page to display the 'n'
latest post titles as a preview to what is on the site.<br />
*Added Requested URL redirect. This makes it so that when someone tries to
access a specific URL such as: http://domain.com/somepage/anotherpage/ then
they will be redirected to that location after login is successful.
*Improved F.A.Q and usage documentation.<br />

1.0
*Initial Release.

== Upgrade Notice ==
= 1.5.1 =
Added features to improve the usefulness of the plugin.

== Screenshots ==
No screenshots yet
