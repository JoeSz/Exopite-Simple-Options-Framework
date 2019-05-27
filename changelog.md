### CHANGELOG

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
