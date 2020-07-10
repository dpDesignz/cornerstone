# Cornerstone Change Log

## 0.3.05 - Sections + FAQ + Menu (2020-07-10)

- ➕ Added the ability to edit FAQ sections
- ➕ Added the ability to see menu and FAQ section item counts from the section list

## 0.3.04 - Labels + FAQ (2020-07-04)

- 🧹 Fixed bug if jQuery modal failed to load
- ➕ Added the "Infield Top Aligned" (`.csc-ifa`) input field styling classes
- ➕ Added the `.csc-hint` class for adding a hint icon to the end of an input field
- 🧹 Fixed bug when trying to remove an FAQ item from and FAQ section from the FAQ page
- ➕ Added the scroll to element from hash function in the core `cornerstone.js` file
- ➕ Added the `addid` option to the page editor

## 0.3.03 - Sections + FAQ + Menu (2020-06-26)

- ➕ Added initial debugging to the core `cornerstone.js` file
- 🧹 Fixed bug when saving menu items would clear them
- ➕ Added the ability to set a slug for an FAQ
- ➕ Added the ability to edit an FAQ
- ➕ Added the ability to output an FAQ section using the `$contentOP->outputFAQSection()` method
- ➕ Added the `toggleFAQCollapsible` function to the core `cornerstone.js` file
- ➕ Added the ability to check for FAQ Section and FAQ items in a page using the `$contentOP->checkStringFAQ()` method
- ➕ Added the ability to output an FAQ item using the `$contentOP->outputFAQ()` method
- ➕ Added the `menui_custom_title` column to the `cs_content_menu` table
- ➕ Added the ability to output a menu using the `$contentOP->outputMenu()` method
- 🧹 Fixed bug where the `addLabelActive()` and `removeLabelActive()` functions would fail if an input didn't have an ID
- 🧹 Fixed bug with `csc-btn--light` css class showing the text the same colour as the foreground

## 0.3.02 - Sections + FAQ (2020-06-22)

- ➕ Added the `content_sort_order` column to the `cs_content` table
- ➕ Added the `cs_content_faq_section` and `cs_content_menu` tables
- ➕ Added the ability to add a "menu" section type
- ➕ Added the ability to hide the section "location" if "type" isn't "page"
- ➕ Added the "menu" type to the sections index list
- ➕ Added the ability to edit menu items
- ➕ Added the to assign a page to a menu when creating the page
- ➕ Added the to assign/un-assign a page to a menu when editing the page
- ➕ Added the ability to add an FAQ
- ➕ Added the ability to view the FAQ index list

## 0.3.01 - Collapsible (2020-06-15)

- ➕ Added the collapsible feature
- Updated ezsql/ezsql (4.0.9 => 4.0.10)
- Updated phpmailer/phpmailer (v6.1.5 => v6.1.6)
- Updated html2text/html2text (4.2.1 => 4.3.1)
- Updated voku/portable-ascii (1.4.10 => 1.5.1)
- Updated symfony/translation-contracts (v2.0.1 => v2.1.2)
- Updated symfony/polyfill-mbstring (v1.15.0 => v1.17.0)
- Updated symfony/polyfill-php80 (v1.17.0)
- Updated symfony/translation (v5.0.7 => v5.1.1)
- Updated nesbot/carbon (2.32.2 => 2.35.0)
- Updated illuminate/contracts (v7.4.0 => v7.15.0)
- Updated doctrine/inflector (1.3.1 => 2.0.3)
- Updated illuminate/support (v7.4.0 => v7.15.0)
- Updated illuminate/cache (v7.4.0 => v7.15.0)
- Updated symfony/finder (v5.0.7 => v5.1.1)
- Updated illuminate/filesystem (v7.4.0 => v7.15.0)
- Updated nikic/php-parser (v4.3.0 => v4.5.0)
- Updated symfony/polyfill-util (v1.15.0 => v1.17.0)
- Updated symfony/polyfill-php56 (v1.15.0 => v1.17.0)

## 0.3.00 - Clean Install (2020-06-03)

- 🧹 Fixed bug with loading main style sheet
- 🖊️ Changed default cornerstone.sql files
- 🖊️ Changed the `top.php` theme file to `layout.php`
- 🧹 Fixed bug where the `loadpage` method didn't fire loading the `index` method when requested
- 🖊️ Changed the Toastify gradients for `-info` and `warning` for better contrast`

## 0.2.26 - Styling (2020-05-15)

- ➕ Added extra checks to the `userPageProtect()` function to make sure roles were set between sessions
- ➕ Added the ability to show page updated on page
- 🧹 Fixed bug where page wouldn't redirect properly if in wrong directory
- 🖊️ Changed the `section_location_name` column to `section_location_name` in the `cs_content_section` table
- 🖊️ Changed section "directory" to "location"

## 0.2.25 - Styling (2020-04-27)

- ➕ Added `toastify-xxx` style classes
- ➕ Added validation for "chosen" fields

## 0.2.24 - Settings + Role Permissions (2020-04-16)

- 🧹 Fixed bug when viewing the user list and the last login date wasn't populating
- 🧹 Fixed bug where user list has been changed. Added `listUsersBasic()` to fix this
- ➕ Added styling to input labels with a `data-tippy-content` attribute
- ➕ Added the ability to view and change core, mail, security, site, and add-on settings
- ➕ Added the ability to view a permissions list and search for keys

## 0.2.23 - User + Roles (2020-04-15)

- ➕ Added the `cs_roles`, `cs_roler_permissions`, and `cs_role_perms` tables
- 🖊️ Changed the `user_group_id` column to `user_role_id` in the `cs_users` table
- ➕ Added `role()` class for handling user role permissions
- ➕ Added the `canDo(x)` and `isMasterUser()` checks on the role class
- ➕ Added loading user permissions in the bootstrap
- ➕ Added the `permission` variable to the `outputAdminMenu` function to allow setting a permission to a menu item
- 🧹 Refactored the user model
- 🧹 Refactored the userauth model
- ➕ Added the settings section
- ➕ Added the ability to view, add, and edit user roles
- ➕ Added the ability for a master user to add a permission

## 0.2.22 - Styling (2020-04-10)

- ➕ Added mobile support for the admin section
- ➕ Added form switch element and moved the form components into a sub-folder for easier readability

## 0.2.21 - Dependencies (2020-04-04)

- ➕ Added the [Intervention Image](http://image.intervention.io) package
- ➕ Added the [Intervention Image Cache](https://github.com/Intervention/imagecache) package
- ❌ Removed the `nielse63/phpimagecache`, `gumlet/php-image-resize`, and `swiftmailer/swiftmailer` packages from production
- ➕ Added the `Images` controller from the ME project
- 🔄 Updated `whichbrowser/parser` (v2.0.41 => v2.0.42)
- 🔄 Updated `phpmailer/phpmailer` (v6.1.4 => v6.1.5)
- ➕ Added the [Roboto font](https://fonts.google.com/specimen/Roboto) from Google for the image placeholder text
- ➕ Added the placeholder text for the `config-sample.php` file
- 🔄 Updated the `cornerstone.sql` file
- ➕ Added the [Tippy.js](https://atomiks.github.io/tippyjs/) script as a vanilla JS alternative to Tooltipster
- 🖊️ Changed all instances of Tooltipster to Tippy and phased out Tooltipster

## 0.2.20 - Content (2020-03-30 to 2020-03-31)

- ➕ Added the ability to view the pages list
- 🧹 Refactored the `admin.js` file to be vanilla js
- 🧹 Refactored the admin menu code to use an array and output function
- ➕ Added the ability to view the sections list
- ➕ Added the ability to add a section
- ➕ Added the ability to edit a section
- ➕ Added the ability to add a page
- ➕ Added the ability to edit a page

## 0.2.19 - Content (2020-03-27)

- ➕ Added the vanilla JS ready state function
- ➕ Added the `cs_content`, `cs_content_meta`, `cs_content_section`, and `cs_seo_url` tables
- ➕ Added the `cornerstonecore.model.php` file to the system models for loading seo and content data
- 🖊️ Changed the `core.class.php` default `$currentMethod` from `error` to `loadpage`
- ➕ Added the [Html2Text](https://github.com/mtibben/html2text) package

## 0.2.18 - Options (2020-03-14)

- ➕ Added the options class
- ➕ Added the setting to set option data in the load view data
- 🧹 Fixed session and bootstrap EOF bugs
- 🧹 Fixed an error where `request->set_params()` fell over if a data item was an array or object
- ➕ Added files and documentation for personal usage

## 0.2.17 - Composer Updates (2020-02-03)

- 🔄 Updated ezSQL to v4.0.9
- 🔄 Updated whichbrowser to v6.1.4
- 🔄 Updated phpmailer to v6.1.4
- 🔄 Updated symfony polyfills (php72 v1.13.1, mbstring v1.13.1, intl-idn v1.13.1, iconv v 1.13.1)
- 🔄 Updated egulias email validator to v2.1.15
- ➕ Added loader, pagination, registry, and request classes
- ➕ Added the `outputBreadcrumbs()` function to the `output` helper file
- 🧹 Refactored the core controller
- ➕ Added the ability to debug load view data
- ➖ Removed the `outputPagination()` function from the output helper file

## 0.2.16 - Notification System (2020-01-19)

- ➕ Added the `cs_notification` table
- ➕ Added the `notification` and `notificationManager` classes
- ➕ Added the [Toastify](https://apvarun.github.io/toastify-js/) script

## 0.2.15 - UX Changes (2020-01-06)

- ➕ Added the `.csc-btn--inlineunder` modified button class
- ➕ Added to `getUrl()` function in the core loader to change dashes to underscores
- ➕ Added styling to allow the use of the [select2](https://select2.org/) script
- ➕ Added the ability to set an alert as not closable by setting `data-clsoable="false"`
- Fixed issue where error was being thrown if an input didn't have a label
- Changed the styling for the `.csc-helper-text` class to be positioned so it can "float" rather than shift the page

## 0.2.14 - Pagination (2019-12-28 to 2020-01-01)

- ➕ Added the script to reload a table when the page limit is changed
- ➕ Added the pagination output function (`outputPagination()`)
- ➕ Added the sort URL builder function (`get_sort_url()`)
- ➕ Added the check sort item function (`check_sort_item()`)
- ➕ Added `.csc-pagemenu` styling
- ➕ Added the `.csc-data-table__search` styling
- Updated the `get_sort_order()` function to output the sort if set

## 0.2.13 - UI Changes (2019-12-12 to 2019-12-16)

- ➕ Added component subdirectory for example components
- ➕ Added the [Inflector](https://github.com/ICanBoogie/Inflector) package
- ➕ Added the [Pickadate](https://amsul.ca/pickadate.js/) script
- ➕ Added custom styling for Pickadate script output
- Changed the Uppy button to follow the button outlined styling as it was most often be a secondary button
- ➕ Added function to remove AJAX modals from the DOM on close
- ➕ Added the [Inputmask](http://robinherbots.github.io/Inputmask) script

## 0.2.12 - Functions + UI Changes (2019-12-06 to 2019-12-07)

- Cleaned up the cornerstone.js file
- ➕ Added an animation to submit buttons
- Changed button styles to match alert styles and added proper class names (`cs-btn--color` prefixes)
- Changed the alert styles to allow for icons and a cleaner design
- ➕ Add the ability to close alerts on click
- Changed event listeners for active input labels to ES6 Javascript
- ➕ Added a `pageType` property to the controller class
- Fixed no results styling bug on chosen
- Changed `csc-bi` to `csc-bi--left`
- Lowered the `min-height` on the trumbowyg editor

## 0.2.11 - Functions (2019-11-30)

- ➕ Added a filters functions file
- ➕ Added a function to refactor the `sort` filter

## 0.2.10 - UI Changes (2019-11-25)

- ➕ Added styling for uppy script elements
- Fixed autoloading the Trumbowyg editor if not loaded

## 0.2.9 - Webpack + Scripts (2019-11-21)

- Started to integrate webpack
- ➕ Added the [Trumbowyg](https://alex-d.github.io/Trumbowyg/) script
- ➕ Added `$pageHasForm` boolean variable to header and footer tags to allow of loading form related scripts and stylesheets only where needed.
- Removed the default `text-align: center;` on `csc-data-table` elements
- ➕ Added the [Lazysizes](https://github.com/aFarkas/lazysizes) script for lazy image loading
- ➕ Added the [php-image-resize](https://github.com/gumlet/php-image-resize) package for image uploading and dynamic resizing
- ➕ Added the [Uppy](https://uppy.io/) script for image uploading

## 0.2.8 - UI Changes (2019-11-15 to 2019-11-16)

- Installed `include-media` into core scss Helpers
- Moved grid system into its own .scss file in the layout folder and moved relevant media queries here as well
- Updated Users index to reflect new grid styling
- ➕ Added auto resizing textarea thanks to [Stephan Wagner](https://stephanwagner.me/auto-resizing-textarea-with-vanilla-javascript)
- ➕ Added character counter based on [this character countdown script](https://www.jqueryscript.net/form/character-countdown-text-field.html)
- Changed input label active to trigger on change as well as focus
- Removed tinylimiter script
- ➕ Added `$pageHasForm` variable to admin footer to only load form specific scripts when required
- Updated all cdnjs scripts to have integrity checks
- Updated user forms to reflect new styling
- ➕ Added default colour border on `chosen-container-active` to show active chosen element when tabbed onto

## 0.2.7 - UI Changes (2019-11-10)

- Changed flat button styling to relate to good ux. Added underline and
  hover elements
- ➕ Added requirement for span in `csc-btn--flat` to allow for underlined text but not icon
- ➕ Added `csc-btn--outlined` back in to the stylesheet
- Fixed waves selector in `cornerstone.js` to load any `csc-btn*` class
- Removed css grid from `csc-col`
- ➕ Added `align-items:center` to `csc-card`
- ➕ Added `.inline` to `csc-badge` to allow displaying the badge next to text
- ➕ Added `width: 100%;` to `csc-data-table`
- ➕ Added `display: grid;` to `.csc-ga*` to allow positioning
- Fixed button link color in data table
- Fixed `csc-badge` extension styling

## 0.2.6 - Core Changes (2019-10-25)

- Changed how core methods get the url to prevent spaces being stripped and added code to strip tags to prevent malicious code being executed
- Changed how flashMsg class is assigned. No longer requires the full class name
- Add a tiny button size for sidebars
- Changed `.csc-col` from `align-items` to `align-content`
- ➕ Added more styling options for `csc-badge` to allow use of framed layout

## 0.2.5 - UI Changes (2019-10-11 to 2019-10-21)

- Removed `clearCustomAuth` function from logout script
- Cleaned up admin dashboard to allow more breathing room
- ➕ Added `--wide` button style
- Changed admin user form page style
- ➕ Added animation to admin forms
- Re-designed admin menu
- Fixed dead breadcrumb design
- Updated notification styling
- ➕ Added option for card with no max-width set

## 0.2.4 - Views, Models, and Selects (2019-09-22)

- ➕ Added requirement to use `.vw.php` suffix on view files for easier identification in IDE/SCE
- ➕ Added ability to set custom logo in admin header
- ➕ Added `ext.navbar.php` to theme files for extending the sidebar menu
- Moved models into folders and modified controller to handle retrieving the model name
- Removed `clearCustomAuth()` function from `ext.userauth.php` file as it's not longer user in the logout method
- Fixed link to `ext.userauth.php` file in the `authenticateUser()` method
- Initialised protected `$data` property as an array in core controller class
- Defined default param types in model methods
- Changed `listUsers()` method in 'user.model.php' to default to order by `user_first_name`
- Fixed error of infinite loop when loading 404 page

## 0.2.3 - Admin UI (2019-09-16 to 2019-09-21)

- Updated Admin UI sidebar navigation
- Updated password reset templates
- Fixed fallover if method doesn't exist in controller
- ➕ Added the [JavaScript Cookie](https://github.com/js-cookie/js-cookie) script
- ➕ Added cookie for saving collapsed menu state
- Changed Admin common file to load 'index' model by default.
- Add "All Users" page
- ➕ Added grid layout options for aligning
- Fixed early closing tag in head that wasn't loading jquery
- Removed async tag from jquery loading to resolve double loading fallover
- ➕ Added support for custom admin css file
- ➕ Added `get_public_path()` function
- Changed csc-data-table to default to 100% width
- ➕ Added link visited colour variable to sass
- Fixed time reset bug when checking if `$_SESSION['_cs']['CREATED']` is expired
- ➕ Added [Chosen selector](https://harvesthq.github.io/chosen/) script

## 0.2.2 - Security updates (2019-09-03)

- Moved mail helper into its own class and deleted the `fn.mail` helper file. This can now be called using `new SendMail()`.
- ➕ Added `userPageProtect()` function to the session helper
- Changed how cookie data is set on 'remember me'
- Changed how session data is handled and expired. Added default values for `session.use_strict_mode`, `session.use_trans_sid`, and `session.cookie_domain`.
- ➕ Added `htmlspecialchars` to `$_GET` variables to [restrict XSS](https://www.cloudways.com/blog/php-security/)
- Implemented further security recommendations based on [OWASP cheat sheets](https://github.com/OWASP/CheatSheetSeries)
- Invalidate users existing sessions and cookies if set when changing their password.
- ➕ Added manual session time out and session re-generating for users for extra security.
- ➕ Added `$hideThemeFooter` variable for hiding the theme footer if you want to define your own in the view.
- ➕ Added alpha dashboard UI design

## 0.2.1 - Initial Alpha Release (2019-09-02)

- Core MVC Structure
- Initial admin user functions (login, email based 2FA, remember me, login log, forgot password, password reset)
- Custom database based session handler
- Custom error handler
- PHPMailer and Swiftmailer support
  - HTML and Plain text Email templates for:
    - 2FA (auhorization)
    - Forgot Password - Account exists (password-reset)
    - Forgot Password - Account doesn't exist (password-reset-help)
    - New Password confirmation (new-password)
- Initial Helpers/Functions:
  - Master:
    - redirectTo()
    - get_option()
    - get_site_url()
    - get_sys_path()
    - get_lib_path()
    - get_theme_path()
  - Generate:
    - get_crypto_key()
    - get_crypto_token()
    - get_pin()
  - Mail:
    - get_mail_options()
    - send_phpmail()
    - send_swiftmail()
    - create_email_template()
  - Output:
    - friendlyDtmDiff()
    - showValidationErrors()
  - Session (_this also holds the session controller information_):
    - flashMsg()
    - checkFlashMsg()
    - isLoggedInUser()
- Custom material design based component specific css styling generated from sass file(s)
