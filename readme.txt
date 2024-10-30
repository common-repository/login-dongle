=== Plugin Name ===
Contributors: aercolino
Donate link: http://andowebsit.es/blog/noteslog.com/contact/
Tags: security question, answer, challenge, response, DDoS, IP spoofing, access, account, admin, attack, authentication, block, brute force, control, credentials, dongle, hacker, key, limit, lock, login attempts, login, prevent, private, protect, reject, restrict, security, stop 
Requires at least: 1.0
Tested up to: 4.0
Stable tag: 1.5.2
License: GPLv2 or later

The bookmark to login nobody but you. Simple and secure.


== Description ==

Login Dongle protects your login by means of a [security question](http://en.wikipedia.org/wiki/Security_question) 
(AKA challenge/response) as an extra security layer. A bookmark is your login dongle.


= How it works =

1. Go to your standard login page, fill in the form as usual, and click the bookmark.
1. A prompt asks the challenge. Fill in the response, and accept.<br><br>

https://www.youtube.com/watch?v=9zejJewYVi4
Thanks to [Synx3Solutions](https://www.youtube.com/channel/UCpsROxxI02ovi7TKxXAs_yg) for this video.
<br><br>

= Why it works =

* Under the hood, the bookmark submits login data, together with the challenge and response.
* If both challenge and response validate on the server, the login process goes on as usual, otherwise it dies.
* The login page stays exactly the same as usual, so attackers won't know how to guess challenge and response.
* Only you know the response to the challenge, so nobody but you will be able to use the bookmarlet.
* People using your PC won't be able to login even if your browser fills in the login form with your password.

Login Dongle is compatible with any other login plugin.

For more info, please refer to the [FAQ](http://wordpress.org/extend/plugins/login-dongle/faq/)
and the user's instructions you'll find on the settings page after activating the plugin.


= User Guides =

* Dutch: http://www.wpsitemaken.nl/login-dongle
* English: http://www.itechdestiny.com/wordpress-security-with-login-dongle-plugin/
* English: http://www.shoutmeloud.com/secure-wordpress-login.html
* French: http://neosting.net/comment-securiser-acces-login-wordpress-2-etapes
* German: https://wordpress.org/support/topic/excellent-plugin-110
* Japanese: http://wp.8jimeyo.info/plugin/login-dongle/
* Turkish: http://hakanertr.wordpress.com/2012/07/24/login-dongle/


= Quick Reviews =

* http://forum.ait-pro.com/forums/topic/compatibility-question-for-custom-login/
* http://webscripts.softpedia.com/script/Modules/WordPress-Plugins/Login-Dongle-80067.html
* https://wordpress.org/support/topic/plugin-login-dongle-clever-plugin
* http://www.evohosting.co.uk/blog/web-development/wordpress-web-development/6-of-the-best-wordpress-security-plugins/
* http://www.labnol.org/internet/improve-wordpress-security/24639/
* https://www.linkedin.com/groups/Security-suggestions-1482937.S.199139568#commentID_110730071
* http://www.practicalwp.com/login-dongle-login-bookmarklet-for-wordpress/
* http://www.wtfdiary.com/2012/08/8-ways-to-secure-your-wordpress-blog.html


= Available translations =

* English (by Andrea Ercolino)
* Serbian (by Borisa Djuraskovic)
* Spanish (by Andrea Ercolino)
* Turkish (by Hakan Er)


NOTE: Here 'bookmark' and 'bookmarklet' are used interchangeably.

== Installation ==

1. Upload the *login-dongle* directory to your *wp-content/plugins* directory.
1. Click on *Activate* from the *Plugins* menu.
1. Configure.

= Configuration =

1. Edit your site settings (from the *Settings/Login Dongle* menu), choose a message for intruders, and save.
1. Edit your profile (from the *Users/Your Profile* menu), choose both challenge and response, check the flag *Send on next update*, and save.
1. Drag-and-drop your new bookmark into the bookmarks bar of your browser.<br><br>

https://www.youtube.com/watch?v=eY6O1zwddYE
Thanks to [Priya Madhavi](https://www.youtube.com/channel/UCwG-QfmoTEiKfqLJYiLKZTQ) for this video.
<br><br>

= Upgrading from any version prior to 1.3.0 =

Before version 1.3.0, Login Dongle was site-wide available, and all users essentially shared the 
same bookmark as their login dongle. Starting from version 1.3.0, any user has her own login
dongle. Upgrading from previous versions to 1.3.0 is done by automatically associating to each user
a login dongle with the same challenge &gt;&gt; response as the one that was already site-wide set.

This makes it possible to upgrade hassle free, even if your blog had other users. Anyway, if that
is not what you want, you can configure the login dongle of each user from the *Users/All Users* 
menu.


== Frequently Asked Questions ==

= It's not working. What's the problem? =

I'm going to fix any new bugs you find, but please try the last stable version, maybe it's already fixed.

* Prior to version 1.4.0
 * the Theme My Login plugin and the Login Dongle plugin didn't work together. See below.
* Prior to version 1.2.2 
 * the jQuery library was not made available on the login page.
 * the dongle didn't work with Get New Password button.
* Prior to version 1.2.1
 * a valid login could be rejected if the challenge or response contained quotes.
* Prior to version 1.2.0
 * the dongle encoding was not compatible with a SmartPhone browser. 
* Prior to version 1.1.0
 * the challenge could interfere with other login fields.
 * the dongle bypassed possible plugins associated to the submit button.
* Prior to version 1.0.4 
 * the dongle and the activation procedure didn't work due to last minute bugs.
* Prior to version 1.0.3 
 * it was impossible to install the plugin due to its file structure.


= I've lost my login dongle. How can I access my blog now? =
If you lost your login dongle, you can disable this plugin very easily.

1. Access your blog by means of your usual remote file manager, like an FTP client. 
1. Edit the *login-dongle.php* file in the *login-dongle* plugin directory. 
1. Comment the line `$loginDonglePlugin = new LoginDonglePlugin();` by adding // at the
   beginning. 
1. Save the file back to your site.

This emergency procedure will make the default *Log In* button work again. After logging in, 
undo what you did above, otherwise this plugin will be marked as Active while being inactive. Then 
you can deactivate it with the WordPress button or leave it working.


= Is Login Dongle compatible with other login plugins? =

Login Dongle does not touch any element of the standard login functionality (page, fields, buttons, 
processing ...) of WordPress, so you should be able to run this plugin alongside any other login 
plugin, like the wonderful Limit Login Attempts plugin. If you find issues, feel free to contact me 
and I'll have a look.

[Theme My Login](http://wordpress.org/extend/plugins/theme-my-login/) (at least up to v6.2.2) is 
not comptible with Login Dongle out of the box. In Login Dongle v1.4.0 I added support for Theme My 
Login. Unfortunately, you need to add a missing line into the code of that plugin. In fact WP 3.2 
introduced the *login_init* hook (that I use) but Theme My Login lacks it. To fix Theme My Login 
you'll need to 
<ol>
  <li>edit the file *theme-my-login/includes/class-theme-my-login.php*.</li>
  <li>search for the text 'login_form'. That line and the one before should read:
<code>// allow plugins to override the default actions, and to add extra actions if they want
do_action( 'login_form_' . $action );</code></li>
  <li>add a line in between, reading:
<code>do_action( 'login_init' );</code></li>
  <li>save the file</li>
</ol>


= Can I use Login Dongle instead of Limit Login Attempts (or the likes)? =

I would not. Login Dongle is designed to work in conjunction with brute force attacks repellers 
like Limit Login Attempts and the likes.

What those plugins do is to block access to internet users trying to log in but not being allowed
many times in a row. When that occurs, the recorded intruder's IP is used to reject their following
login requests during some time, even before matching their credentials against the database.

What Login Dongle does is to cut off the processing of the login form if it does not have a special 
field (question) or if that field does not contain the special value (answer) stored in the 
database of your blog, even before running the repeller or any special authentication plugins.

To save your precious resources (CPU time and web availability) when under attack, Login Dongle 
simply exits with a configurable message, instead of incurring into another page generation cycle.


= Can I use a simple answer for my question? =

Yes, because if someone stole your dongle, they are 
[supposed](http://www.goodsecurityquestions.com/designing.htm) to not know the correct answer, 
which is only stored in the database. If they guess it, they only gain the right to process the 
login form on the server, but they still need to guess your unknown (and 
[strong](http://supergenpass.com/)) password. That means that soon they will be locked out by your 
brute forse attack repeller.

However, if you allow your browser to fill in your credentials automatically, and someone is going
to use your unattended PC, you easily realize that in this scenario all security relies on the 
unguessability of the response. If you think that such a scenario is going to happen some time,
you better setup a strong response.

= Limit Login Attempts (or the likes) notified me about some attacks. What can I do? =

Login Dongle makes a brute force attack impossible without knowing the correct challenge &gt;&gt; 
response. Anyway, if a brute force attack repeller notifies you of an attack, you only need to edit 
the Login Dongle section of your Profile. Change both the challenge and response, and you're done. 
As soon as you save your changes, the attack will immediately stop because Login Dongle will expect 
the new challenge &gt;&gt; response to be submitted along with the login form. 


= Limit Login Attempts (or the likes) notified me about some attacks. How can it be? =

The chance to get notified of an attack after installing Login Dongle is extremely little.
If it occurred it'd mean that **both**

1. they know the question because
    * either you told them 
    * or they got access to your login dongle at least once since you changed it the last time
        * either you sent your dongle to them 
        * or they got access to the PC where your dongle is
2. they know the answer because
    * either you told them
    * or it's easy to guess (knowing the question), like *Holmes* for *Sherlock*, 
    * or they brute force attacked your site to find it out (note that no protection exists against 
    a brute force attack perpetrated by evil hackers)
    * or they listened to your internet traffic (if it doesn't go through a secure connection), 
    * or they kept recording each and every keystroke of yours
        * this means that the integrity of your digital persona is badly compromised.


= Can I use HTML tags into the message field? =

You can use whatever you like, up to 1000 characters.


= What characters can I use into the challenge and response fields? =

You can use whatever you like, up to 20 characters. Even kanji.



== Screenshots ==

1. Configuration, step 1 - Site settings.
1. Configuration, step 2a - User settings. Before filling fields in.
1. Configuration, step 2b - User settings. After saving.
1. Configuration, step 3a - Bookmark. After drag-and-dropping it into the bookmarks bar.
1. Configuration, step 3b - Bookmark. After renaming it like the site.
1. Usage - For logging in go to the login page and fill in username and password as usual.
1. Usage, case 1 - Logging in without clicking the bookmark (standard login).
1. Usage, case 2 - Logging in after clicking the bookmark.

== Changelog ==

= 1.5.3 =
* Added support for interim login (i.e. on expired session)

= 1.5.2 =
* Added the Serbian translation (thanks to Borisa Djuraskovic).
* Changed 'bookmarklet' to 'bookmark', easier to understand for most users.
* Added links to installation videos, User Guides and Quick Reviews.

= 1.5.1 =
* Added the Turkish translation (thanks to Hakan Er).

= 1.5.0 =
* Added support for XML-RPC, such that the backdoor used by mobile apps and offline editing tools is now secured.
* Optimized code.
* Improved documentation.

= 1.4.3 =
* Added a banner to the plugin page in the wordpress site.
* Made explicit the license, now required into the readme.
* Improved documentation.

= 1.4.2 =
* Added the Turkish translation (thanks to Hakan Er).

= 1.4.1 =
* Changed the way context is addded to translations. Now using _x() instead of vertical bars.
* Fixed a bug causing the option for a deleted user to stay put.

= 1.4.0 =
* Improved compatibility with plugins that change the login URL.
* Added support for translations.
* Added the Spanish translation.
* Added automatic uninstall functionality.
* Redesigned the interface. 
* Introduced the concept of *security question* as a synonym of *challenge/response*.
* Improved documentation.

= 1.3.1 =
* Fixed a bug related to how WP treats magic quotes.

= 1.3.0 =
* Implemented a login dongle different for each user.
* Made it possible to register without needing a login dongle for the first access.
* Added flags to receive by mail the login dongle codes for backup. 
* Changed the maximum length of challenge and response to 20 characters.
* Updated the screenshot and added two more.
* Improved documentation.

= 1.2.2 =
* Fixed dongle functionality by loading jQuery also in the login page.
* Fixed dongle functionality by allowing its use with all login page actions.
* Changed the used hook from wp_loaded to login_init.
* Improved documentation.

= 1.2.1 =
* Fixed support for any character into the challenge and response fields.
* Improved documentation.

= 1.2.0 =
* Added support for SmartPhones.
* Reduced documentation redundancy between the settings page and the FAQ.
* Updated the screenshot.

= 1.1.0 =
* Improved compatibility of the method used to hook into WordPress.
* Improved compatibility of the bookmarklet with any Log In button click events.
* Improved documentation and added a screen shot of the settings page.

= 1.0.5 =
* Added a bit more help into the installation instructions.

= 1.0.4 =
* Fixed two serious bugs.

= 1.0.3 =
* Fixed the repository structure. Again 2.

= 1.0.2 =
* Fixed the repository structure. Again.

= 1.0.1 =
* Fixed the repository structure.

= 1.0 =
* First version.



== Upgrade Notice ==

= 1.4.0 =
Remember to refresh the bookmarked login dongle by grabbing it again from your profile page.

= 1.2.2 =
Remember to refresh the bookmarked login dongle by grabbing it again from the settings page.

= 1.1.0 =
Remember to refresh the bookmarked login dongle by grabbing it again from the settings page.


== Uninstall Instructions ==

1. Click the *Delete* button in the *Plugins/Installed Plugins* list.

If you manually removed the *login-dongle* plugin directory, or if your version was prior to 1.4.0, 
some garbage is left into your database. It's harmless, but if you still want to clean it up, access 
the *options* table and remove all the rows whose *option_name* column start with *login_dongle*. 

If your version was prior to 1.1.0, edit the *wp-login.php* file in the root 
directory of your blog and remove the line that begins with `do_action('login-start');`


== Banner ==

From [Rusty door](http://www.flickr.com/photos/fotologic/4891100379), taken by 
[fotologic](http://www.flickr.com/photos/fotologic/) on August 14, 2010 in Penbryn, Wales, GB.
