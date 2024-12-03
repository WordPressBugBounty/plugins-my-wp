=== My WP Customize Admin/Frontend ===
Contributors: gqevu6bsiz
Tags: admin, frontend, debug, sidebar, toolbar
Requires at least: 4.7
Tested up to: 6.7
Requires PHP: 5.6
Stable tag: 1.24.1
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Simply and easy-to-use the customize for Admin and Frontend. A lot of custom filters and actions, and included the developer tools.

== Description ==

[My WP Customize](https://mywpcustomize.com) is customize for WordPress.

Simply and easy-to-use the customize for Admin and Frontend. A lot of custom filters and actions, and included the developer tools.

There are lots of custom actions and filters.

You will speed up to your site creation. Debug on current post, Debug on current using theme, Debug on server info, ...etc more helpful info.

The demo site is here: [https://playground.wordpress.net/?plugin=my-wp&url=/wp-admin/admin.php?page=mywp](https://playground.wordpress.net/?plugin=my-wp&url=/wp-admin/admin.php?page=mywp)

= Customize the admin =
- Admin General(hide update notifications, hide screen options, custom footer text).
- Admin Dashboard(hide meta boxes, change meta box title, restrict meta box order).
- Admin Sidebar(hide menus, order menus, change icon and title, add custom link menu).
- Admin Toolbar(hide menus, order menus, change icon and title, add custom link menu).
- Admin Post list(hide columns, order columns, change title).
- Admin Post edit(support block editor and classic editor, hide meta boxes, change title placeholder, restrict order meta box).
- Admin Terms(hide columns, order columns, change title).
- Admin Media uploads(hide columns, order columns, change title).
- Admin Comments(hide columns, order columns, change title).
- Admin Users(hide columns, order columns, change title).
- Admin User edit(hide Visual Editor checkbox, hide Syntax Highlighting checkbox, hide Admin Color Scheme).
- Admin Site editor(Change top left button).
- Admin Nav menus(hide meta boxes, hide Link target, hide Title Attribute, hide CSS classes).

= Customize the frontend =
- Frontend General(show and hide toolbar, hide Rest link, hide Shortlink, set X-Frame-Options, add custom header meta).
- Frontend Author archive(hide archive page, add Disallow to robots.txt).
- Frontend Date archive(hide archive page).
- Frontend Taxonomy archive(hide archive page).
- Frontend Toolbar(hide menus, order menus, change icon and title, add custom link menu).

= Customize the login =
- Login General(Change logo link and image, hide select language, add custom footer text).
- Login User(redirect after login and logout).

= Customize the website =
- Site General(Disable file edit, hide PHP X-Mailer version).
- Site Post type(Change create_posts capability).
- Site Sitemap(hide core sitemap.xml).

= For Debug =
- Debug General(Display a debug screen that is useful for development).
- Debug Blogs(Show all blogs on network/multisite).
- Debug Crons(Show all crons).
- Debug Date time(Show all date and time values).
- Debug Defines(Show all defines).
- Debug Post statuses(Show all post statuses).
- Debug Post structure(Show a post structure).
- Debug Post types(Show all post types).
- Debug Rest API(Show all rest api).
- Debug Site options(Show all site options on network/multisite).
- Debug Taxonomies(Show all taxonomies).
- Debug Terms(Show all terms).
- Debug transients(Show all transients).
- Debug translations(Show all translations).
- Debug Capabilities(Show all user roles capabilities).


== Installation ==

1. Upload the entire mywp folder to the /wp-content/plugins/ directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. You will find 'My WP' menu in your WordPress admin panel.

== Screenshots ==

1. Admin general.
2. Admin dashboard.
3. Admin sidebar.
4. Admin Toolbar.
5. Admin all posts.
6. Admin post edit.
7. Frontend general.
8. Frontend toolbar.
9. Debug screen.
10. Developer panel on footer.

== Changelog ==

= 1.24.1 2024-12-03 =
* Updated: For WordPress 6.7.
* Changed: How to get the wp version and offset seconds.
* Changed: Late include the JS and CSS on admin and frontend and login.
* Fixed: Escape and sanitize the some values.

= 1.24.0 2024-08-06 =
* Added: Deprecated list columns on Posts, Comments, Users, Uploads, Terms.
* Added: Custom search filter on Posts, Comments, Users, Uploads.

= 1.23.1 2024-07-20 =
* Fixed: Update serialized value on bulk post meta.
* Fixed: Sitemap error on lower WP.
* Fixed: Escape value for orderby statement on admin uploads and admin posts.

= 1.23.0 2023-12-25 =
* Added: Debug for rest API.
* Added: Hide setting for WordPress CORE sitemap.
* Added: Send X-Frame-Options header on frontend.
* Fixed: Some small fix.

= 1.22.2 2023-11-27 =
* Fixed: Autovivification fixed for PHP 8.1.

= 1.22.1 2023-11-25 =
* Updated: Compatibility for PHP 8.1 and WP 6.4.
* Fixed: Hide search box on uploads.
* Fixed: Print template file name when empty template.
* Fixed: Change label on post edit.
* Fixed: Print translation content when empty translation object.

= 1.22.0 2023-08-14 =
* Added: Add disallow to author archive for robots.txt.
* Added: Hide X-Pingback for response header.
* Fixed: Text line break adjustment on debug panel, sidebar setting, toolbar setting.
* Fixed: Get all user roles.
* Fixed: Sort order column customize on Posts.

= 1.21.1 2023-05-09 =
* Fixed: Misalignment of admin sidebar menu on mobile.
* Fixed: Hidden avatar icon on Site Editor.
* Fixed: Some security update(XSS vulnerability).

= 1.21.0 2023-03-31 =
* Added: Change icon for Site Editor.
* Updated: Update register meta boxes.
* Updated: Some javascript escapes.
* Updated: Get current_screen_id for developer tool.
* Updated: Get default list columns and meta box setting for some controllers.

= 1.20.1 2022-11-14 =
* Added: Hide rest api link and shortlink on HTML meta of Frontend.
* Updated: Customize post edit when using the block editor.
* Fixed: Customize the admin toolbar when the user does not have blogs.
* Fixed: Sortable request when multiple orderby requests on Posts.

= 1.20.0 2022-05-30 =
* Added: Customize columns on terms.
* Updated: Small improvement in usability.
* Fixed: Working when no setting on comments, posts, uploads, users.

= 1.19.1 2022-02-12 =
* Added: Hide language selectable on login.
* Added: Custom UI Admin sidebar menu admin body class.
* Fixed: Custom UI Admin sidebar menu.
* Fixed: Small fix.

= 1.19.0 2022-01-11 =
* Added: Debug the post structure, post type structure, capabilities.
* Added: Hide bulk actions on Posts.
* Added: Force list show on Media library.

= 1.18.3 2021-12-09 =
* Changed: To setting debug datetime from developer datetime.
* Added: Debug the post statuses.
* Updated: Small fix.
* Fixed: Admin sidebar optimize for mobile/tablet device.
* Fixed: Admin posts list show expand for mobile/tablet device.

= 1.18.2 2021-09-27 =
* Updated: Show admin toolbar for frontend.
* Fixed: Admin toolbar menu item for mobile device.
* Fixed: Show admin sidemenu for mobile device.

= 1.18.1 2021-09-02 =
* Updated: Javascript small update.
* Fixed: Get the PHP Mailer version.
* Fixed: PHP static property.
* Fixed: Hidden default toolbar some icon.
* Fixed: Return default columns when regist posts columns.
* Fixed: Hidden column header of posts list.

= 1.18.0 2021-07-24 =
* Added: MyWP admin sidebar menu item is dynamic.
* Added: Hidden PHP X-Mailer version.
* Fixed: 0sec data update for cache timeout setting on sidebar&toolbar customize.
* Fixed: Empty data update for bulk post meta.
* Updated: PHP 8 support.

= 1.17.1 2021-06-15 =
* Changed: To cast int from intval function.
* Developping: PHP 8 add support.
* Fixed: Change order disable for meta boxes.
* Fixed: Screen scroll when admin custom footer text setted.
* Added: Current screen info on Developer admin screen.

= 1.17.0 2021-05-14 =
* Updated: Change redirect from wp_redirect() to wp_safe_redirect().
* Added: View current post on admin toolbar.
* Added: Custom logger post.

= 1.16 2021-03-05 =
* Fixed: Metabox restrict sortable.
* Fixed: Contact fields on user edit.
* Added: Change top left icon on Block Editor.
* Added: Hide revisions on publish metabox.
* Added: Hide application password on user edit.
* Added: Edit current post on frontend admin toolbar.

= 1.15.0 2020-11-10 =
* Removed: Thirdparty for WooCommerce.
* Added: Shortcode for Term.
* Added: Update bulk post meta on Posts.
* Updated: Added collapse menu text when customized admin sidebar.
* Updated: Debug for upload file.

= 1.14 2020-08-28 =
* Removed: Thirdparty for Advanced Custom Fields.
* Updated: Customize Document panel on post edit for Block Editor.

= 1.13.2 2020-07-18 =
* Updated: jQuery compatible to 3.x.
* Updated: How to get all custom fields keys.
* Updated: Add capability for toolbar item.
* Fixed: Some small bugs.

= 1.13.1 2020-04-27 =
* Updated: Debug list.

= 1.13 2020-03-03 =
* Added: Frontend toolbar customize.
* Added: toolbar item and post shortcode.
* Updated: Cache delete post meta when MywpPostType::get_post.
* Updated: Column title can be shortcode for Posts.

= 1.12.2 2019-11-13 =
* Fixed: Remove menu item on toolbar customize.
* Updated: Some small usability.

= 1.12.1 2019-09-19 =
* Added: Inlucde css&js for frontend.
* Fixed: Remove tags for input css fields.
* Fixed: Post per page setting on Posts customize.

= 1.12 2019-06-13 =
* Added: Redirect after login.
* Added: [mywp_url] to post permalink on shortcode.
* Updated: Remove cache for Sidebar and Toolbar.
* Fixed: Spaces on sidebar customized when screen zoom.
* Fixed: Login title filter for WP 5.2.
* Fixed: Change post type and taxonomy when slow network.

= 1.11 2019-03-20 =
* Added: Login form customize.
* Updated: Some Frontend debug.
* Updated: Some network admin debug.

= 1.10.2 2019-03-06 =
* Added: Manager Ajax and Network Manager Ajax actions.
* Added: Remove transients timing to Sidebar and Toolbar.
* Added: Automatic output of column body for Posts.
* Added: Added some features for Frontend.
* Fixed: Toolbar custom URL to attributes.
* Fixed: Some bugs.

= 1.10.1 2019-01-14 =
* Fixed: Sidebar custom UI.
* Fixed: Global $post on Media Libraries.
* Fixed: abstract setting module function name.
* Updated: Remove setting with registered list columns.

= 1.10 2019-01-08 =
* Added: Customize Comments columns.
* Added: Customize Uploads columns.
* Added: Customize Users columns.
* Updated: To improve the convenience of plugin.
* Fixed: Some bugs.

= 1.9 2018-12-29 =
* Added: Customize posts columns.
* Updated: Thirdparty.

= 1.8 2018-12-08 =
* Fixed: Some bugs.
* Added: Change the editor for post edit(block/classic).
* Added: Hide the meta boxes on post edit screen for Block Editor(WP ver5).

= 1.7.2 2018-11-09 =
* Fixed: Empty check for function to value(for PHP5.4).
* Updated: Check to image file URL.
* Added: Debug for $_COOKIE.

= 1.7.1 2018-09-25 =
* Fixed: Some bugs/translations.

= 1.7 2018-09-05 =
* Fixed: Some bugs/translations.
* Updated: Blog ID/Site ID can use to shortcodes.
* Updated: Organizing hook names.
* Added: Object for transient.
* Added: Network manager.

= 1.6.2 2018-08-09 =
* Fixed: Translations.
* Fixed: Reorder menu items on Sidebar/Toolbar customize.
* Added: User first name/last name/display name on shortcode.

= 1.6.1 2018-07-20 =
* Fixed: Definition of variable.
* Updated: Translation file(pot).

= 1.6 2018-07-10 (Slightly larger update) =
* Enhancement: Some filters added to Model, Controller, Setting.
* Enhancement: Cache to Sidebar&Toolbar customize.
* Added: Support taxonomy to Sidebar&Toolbar customize.
* Added: Dynamic menu items on Toolbar with multisite.
* Added: Roughly get max allowed packet size(mysql).
* Added: Some debug lists on Site of Developer.
* Added: parse_url() on request of Developer.
* Added: Example debug panel of Developer.
* Added: Network admin url to Shortcode.
* Changed: Early get Toolbar items hook.
* Changed: Model class.
* Changed: Restlict customize sub items to Sidebar&Toolbar.
* Changed: Some properties on Thirdparty.
* Fixed: Show defines to individual site when multisite.
* Removed: get_plugin_data_by_name on MywpThirdparty.

= 1.5.8 2018-05-05 =
* Enhancement: Show the current queries on Debug tool.

= 1.5.7 2018-04-18 =
* Fixed: Customized sidebar collapse menu.
* Updated: Custom menu UI on sidebar customize.

= 1.5.6 2018-04-03 =
* Added: Some filters/actions.
* Fixed: Debug value options, site_options.

= 1.5.5 2018-03-23 =
* Fixed: Only main_query on Archive disable.
* Fixed: Debug object to options, site_options, find option.

= 1.5.4 2018-02-27 =
* Fixed: Echo array on find option.

= 1.5.3 2018-02-27 =
* Fixed: Code miss on Developer.
* Fixed: Do shortcode on admin footer text.
* Fixed: Strict comparison.
* Added: Debug for options, site_options, blogs, find option.

= 1.5.2 2018-01-22 =
* Fixed: Do shortcode for custom html of admin bar customize.
* Fixed: Apply some filters for custom footer text of admin general.
* Fixed: Show icon for using custom icon of sidebar customize.

= 1.5.1 2017-12-25 =
* Added: Debug to user meta on User Edit.
* Enhancement: Network menu on admin toolbar customize.
* Fixed: Initialized data fields of controller.

= 1.5 2017-12-08 =
* Added: Shortcode to [mywp_site].
* Added: Syntax Highlight setting on User Profile.
* Fixed: Get meta serialize of post type.

= 1.4 2017-10-30 =
* Added: Customize the users page.
* Added: Disable the user dashboard.
* Changed: Priority change the main and network on mywp_setting_menus.
* Enhancement: Show Terms and Taxonomies on single page of Developer.
* Removed: Function "deep_esc_html" on Helper.

= 1.3.3 2017-09-08 =
* Enhancement: Customize sidebar.

= 1.3.2 2017-09-03 =
* Added: Change the capability for create_posts.
* Enhancement: Developer.
* Updated: Model data parse.

= 1.3.1 2017-08-28 =
* Fixed: Setting screen for Network.
* Added: Update messages posts and edit post.
* Added: Debug for Network.

= 1.3 2017-08-23 =
* Enhancement: All transients, All Translations, All Crons of debug.
* Enhancement: Dates of Developer.
* Updated: Require WordPress version to 4.7.

= 1.2.3 2017-08-19 =
* Enhancement: Edit Post / Add Post.
* Enhancement: Developer.
* Fixed: Block not use admin for ajax.

= 1.2.2 2017-08-16 =
* Fixed: Hide add new of post edit.
* Updated: Some feature of Developer.

= 1.2.1 2017-08-07 =
* Fixed: Show the structure template on developer.
* Enhancement: Developer.

= 1.2.0 2017-07-25 =
* Updated: Show the structure template on developer.

= 1.1.6 2017-06-15 =
* Updated: Some bugs.
* Added: Some small feature.

= 1.1.5 2017-04-22 =
* Updated: Small changes.

= 1.1.4 2017-03-09 =
* Fixed: Debug time to timestart().
* Fixed: Debug information on Frontend.
* Fixed: User edit advanced settings.

= 1.1.3 2017-03-08 =
* Added: Debug time to timestart().
* Added: Site customize.
* Added: Compatible network settings.
* Fixed: Contollers cache.

= 1.1.2 2017-03-04 =
* Added: Customize UI of admin sidebar.
* Added: Using cache controllers/setting menus.
* Added: Not use admin panel on Admin General.
* Fixed: Debug footer html.
* Fixed: Miss name to default from initial on Admin Posts.

= 1.1.1 2017-02-24 =
* Fixed: sidebar of admin customize.

= 1.1 2017-02-24 =
* Added: sidebar of admin customize.
* Added: custom third party activate filter.
* Added: include js file of admin general.

= 1.0 2017-02-17 =
* Initial release.
