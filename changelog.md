### CHANGELOG

= 20261117.4 - 2026-11-17 =
* Can be installed and activated as a standalone plugin (exopite-simple-options.php), still works bundled
* Admin notice if another bundled copy was loaded before the plugin, action exopite_sof_loaded, constant Exopite_Simple_Options_Framework::VERSION
* Fix: include the framework files with __DIR__ instead of relative paths (depended on include_path)

= 20261117 - 2026-11-17 =
* Security: upload/delete AJAX requests require a nonce and capability checks (upload_files, edit_post, delete_post)
* Security: import/export/reset require the options capability and a per-options-page nonce, only the own option can be changed
* Security: escape field values in the admin output (esc_attr, esc_textarea, esc_url)
* Security: metabox save requires a nonce and edit_post capability for the saved post
* Fix: "simple" metabox options saved the whole $_POST as post meta, now only the defined fields are saved
* Fix: fatal error on missing config keys (eac_attr__ typo)
* Fix: fatal error in plugin settings link if settings_link is an URL, wrong default settings link
* Fix: checkbox array sanitization, hard coded "en" language in the hidden _language field
* Fix: several PHP 8.1+ warnings and deprecations
* Allow nested accordions (accordion field inside an accordion field)
* Unify text domain to exopite-sof
* Requires PHP 8.2+
* Security: escape attributes, labels and titles in all field templates (esc_attr, esc_url, esc_html, wp_kses_post)
* Fix: typography font family was not preselected (selected() echoed instead of returned)
* Fix: button bar buttons have type="button", so they never submit the form
* Fix: typography preview loads only Google fonts from Google, not websafe fonts (blocked MIME type error)
* Coding standards: PHP, JS and CSS reformatted to the WordPress standards, dead debug code removed
* Build: npm run build creates the minified JS and CSS from assets/dev
* Fix alignment in WordPress 7 admin (40px controls): header search icon and title, button texts, field titles, number/range units, prepend/append, upload and image buttons

= 20261009 - 2026-10-09 =
* Fix PHP 8.0+ deprecation: optional parameters declared before required ones (get_menu_item, field constructors)
* Fix PHP 8.2+ deprecation: declare dynamic properties (fields base class, accordion, group)

= 20191203 - 2019-12-03 =
* Fix PHP warnings

= 20191130 - 2019-11-30 =
* Fix some CSS issues

= 20191116 - 2019-11-16 =
* Fix: some typos and change some styles

= 20190527 - 2019-05-27 =
* Fix: load textdomain

= 20190520 - 2019-05-20 =
* Fix: wp.media is not a function
* Fix: image field not included if it is included in group field

= 20190503 - 2019-05-03 =
* Fix: checkbox ans select with attribute multiple not sent in metabox
* Fix some style in metabox under Gutenberg
* Fix unserialize if value serialized

= 20190411 - 2019-04-11 =
* Add gallery field

= 20190409 - 2019-04-09 =
* Set minicolor az default colorpicker

= 20190408 - 2019-04-08 =
* Fix color picker not working in groups after clone:<br>
Add new colorpicker. Minicolor https://github.com/claviska/jquery-minicolors

= 20190407 - 2019-04-07 =
* Add recursive santization for tab, fieldset and group field.
* Fix switcher, select and (single) checkbox not saved in nested groups if default is yes and user select no.
* Fix group title (should) apply only current group
* Fix font preview not working in groups, if it is newly created (with the add button)
* Fix WP Editor not working in groups after clone
* Fix Chosen not working in groups after clone

= 20190331 - 2019-03-31 =
* Move sanitization functions to a separate class.

= 20190325 - 2019-03-25 =
* Fix Trumbowyg, colorpicker and datepicker not working on dynamically added group elements.

= 20190324 - 2019-03-24 =
* Add accordion field
* Add fieldset field
* Sortabe based on HTML5Sortable, better handling dynamically created nested sortable
https://github.com/lukasoppermann/html5sortable
* Tab and group field now are nestable

= 20190316 - 2019-03-16 =
* New design
* Add typography field
* Add tab field
* Add submenu (section)
* Add search
* Various bugfiexes

= 20190218 - 2019-02-18 =
* Include Trumbowyg localy
* Some bugfixes (New PHP, WordPress version and Gutenberg)


= 20181122 - 2018-11-22 =
* Fix name index update on drag drop and delete in gorup field.

= 20181026 - 2018-10-26 =
* Filter to override save methode. "exopite_sof_field_value"
* Various bugfiexes.

= 20181015 - 2018-10-15 =
* Fix TinyMCE is undefinied error in save, if not enqueued.

= 20181002 - 2018-10-02 =
* Fix import and delete options didn't work because minification error.

= 20180930 - 2018-09-30 =
* Load "non multilang" options in multilang if multilang not exist and other way arround for compatibility.

= 20180924 - 2018-09-24 =
* Fixed TinyMCE does not save.
* Fixed ACE Editor addig slashes.

= 20180916 - 2018-09-16 =
* Code clean up
* Fix image_select and multiselect doen't save
* Import, export using JSON encoded array

= 20180911 - 2018-09-11 =
* Multilang support for WPML, Polylang, WP Multilang and qTranslate-X
* Major refactoring to meet WordPress standard
* Option to save post meta as simple instad of array

= 20180904 - 2018-09-04 =
* Dashes in Filter and Action names to meet WordPress standars (thanks to raoabid GitHub)

= 20180903 - 2018-09-03 =
* Refactoring main class to include some helper functions (thanks to raoabid GitHub)

= 20180608 - 2018-06-08 =
* Add open section with url (...?page=[plulin-slug]&section=[the-id-of-the-section])

= 20180528 - 2018-05-28 =
* Fix footer displayed twice
* Add save form on CTRL+S

= 20180511 - 2018-05-11 =
* Add loading class and hooks

= 20180429 - 2018-04-29 =
* add Trumbowyg editor to editor field
* allow TinyMCE in group field
* improve JavaScripts
* group can be sortable

= 20180219 - 2018-02-19 =
* Add SweetAlert (https://sweetalert.js.org/docs/)

= 20180114 - 2018-01-14 =
* Add backup and group/repeater field.

= 20180113 - 2018-01-13 =
* Add meta field.

= 20180107 - 2018-01-07 =
* Add button field.

= 20180102 - 2018-01-02 =
* Initial release.
