
## Changelog ##


### 0.18.9 ###

Make test form compatible with code minifiers

* Thanks to @khani26 for reporting


### 0.18.8 ###

Fix WordPress search behaviour

* Pages was missing in search results
* Thanks to @berylune for reporting


### 0.18.7 ###

Welcome WordPress 4.5

* Upgrade autotests


### 0.18.6 ###

Add result's page extension point and PHP7 support

* Add `wp_testing_passer_before_render` extension point
* Add PHP 7.0 support and leave only edge cases in tests


### 0.18.5 ###

Fix donate link and update WP tests to 4.4.2

* Thanks to new PayPal.Me service

There is nothing new in this hotfix.


### 0.18.4 ###

Improve results saving under high concurrent load

* Avoid MySQL errors like `Deadlock found when trying to get lock; try restarting transaction`
* Issue solved by catching such errors and restarting save as recommended at [MySQL :: How to Cope with Deadlocks](http://dev.mysql.com/doc/refman/5.7/en/innodb-deadlocks.html)

You should upgrade only if your site is popular and you've heard of some strange "white screens of death" when respondents are posting their results on last step before redirecting to result pages.

Just opening result page many times will not result in any errors.
Especially if you have some cache plugin, that already transformed result page into static page and DB is not touched at all.

For reporting and sponsoring this issue thanks to [Johan](http://www.personalityperfect.com/)


### 0.18.3 ###

Improve modern themes compat, upgrade WP compat and addons extension points

* Switch to more independent unique identifiers generating lib to avoid theme compatibility issues
* Check WordPress 4.4.1 compatibility
* Add more extension points for addons in settings and results managing (related to pdf addon)


### 0.18.2 ###

Welcome WordPress 4.4

* Upgrade autotests
* Update screenshots


### 0.18.1 ###

Add "equals to" button into formula comparisions

* Improve external addons intergation


### 0.18 ###

Really-multisite, menu for non-admins and migration fault tolerance

Plugin now really multisite-compatible

* When "Network Activate" is used — database updated for all sites
* When new site created, it's database also updated

Correctly add menu page under non-admin

* Respect user role when adding menu
* Rename page title to "My Test Results"

Improve migration fault tolerance when WordPress tables has different table types

* Normally you should have only single table type (name if format, engine): MyISAM or InnoDB
* But there are cases when you have "damaged" or "optimized" database and table types differs
* Now database migration takes this into account. It's critical only for new users when they can't activate plugin
* Thanks to paid support — this issue was catched during it


### 0.17.2 ###

Internal improvements and intro-video

Add intro-video with plugin description, howto concepts and explanations: http://www.youtube.com/watch?v=tT3d8Jdm7kY

Internal improvements:

* Improve code quality and avoid duplicates
* Improve test rendering


### 0.17.1 ###

Fix incorrect content processing

Symptoms:

* The questions on the first page of the test dissapear
* Shortcodes don't work on result page

Upgrade notice:

* If you use version 0.17 upgrade to this version!
* Please check first pages of all tests, that use shortcodes in them
* Also please check  result pages, which could have shortcodes inside.
It's ok just  to open at least one existing page. Shortcodes could be in results or scales descriptions.

I apologize for the inconvenience.


### 0.17 ###

Shortcodes for tests embedding

* Add `wpt_test_read_more` and `wpt_test_first_page` shortcodes that allows to embed test in short or full modes.
* Enrich parameters of `wpt_tests` shortcode (ex-`wptlist`).
* Hide "Publish on homepage" checkbox when it's impossible to publish on homepage (custom page on home instead of latests posts).

For details about shortcodes params please see our good old FAQ.


### 0.16.5 ###

Add Thai lang and fix tests' results under respondent account


### 0.16.4 ###

Fix issue with individual answers was not added if results was attached


### 0.16.3 ###

Fix negative substracting in formulas and improve passing results table

* Fix formulas with negative scale values substracting
* Improve passing results table internals and addons integration


### 0.16.2 ###

Upgrade WP to 4.3 and edit test author

* Upgrade WordPress compatibility to 4.3
* Allow to switch test's author (same as for posts)


### 0.16.1 ###

Enable working in multisite mode

* Use unique DB names to allow many installs in same DB
* Test multisite install as two activations and programming auto-tests on 1st install


### 0.16 ###

Decimalize scores

Before score value could be from -128 to 127, which is not too usable for tests which have decimal scores. Now it's changed and possible values are from -999.999 to 999.999. So now you can use scores like 0.005 or else. These changes are applied to scales's labels too.
Decimal-style values are shown only when it's needed. So if you have scale which values are 15 out of 15, it will be shown as "15 out of 15". But when values will be decimal — it will  be shown as decimal: "12.034 out of 24.3".

* WordPress compatibility updated to 4.2.4 and prepared to 4.3.
* Use [semantic headers](https://make.wordpress.org/core/2015/07/31/headings-in-admin-screens-change-in-wordpress-4-3/) in respondents results as of WP 4.3.

Fixes:

* Respect results orders. Helpful when you have many results and their output order is important at results page.
* Fix support tags generation.

Internal improvements:

* Step strategy know if answered questions are possible now.
* Step strategy can show step's description as a short description before questions.


### 0.15.2 ###

Maintenance: Document "Plugin update checker" library role

Wp-testing plugin uses external library named "Plugin Update Checker" for the purposes of updating paid addons only. These addons are hosted at http://apsiholog.ru/addons/. Updates happens only in admin area and only when addon registered. So it's not touched you if you dont' have any paid addon installed. This external library is not send anything to update server other than the current version of paid addon, that needs to be updated when it's time will comes.

Upgrade notice:

* You don't need to update on this version as it's here only for legal purposes.


### 0.15.1 ###

Maintenance: FAQ, screenshots, latest WP compatibility

* Add FAQ with links to support forum tags
* Improve screenshots style
* Test latest WP version compatibility (4.2.3)
* Minor locale update
* Speed up testing auto-builds for 30 minutes


### 0.15 ###

Add question-answer variables in formulas

* Link results without scales and scores — only questions and answers needed
* Add NOT comparisions in formulas
* Warn about required answers in more understandable way

In formulas now there is button titled "Question [..] answer [..]" that on click adds variable like `question_1_answer_2`. This variable will be true only when respondent will choose in 1st question 2nd answer.

Before when you have two opposite results and you was enforced to negate their formulas manually. For example: `scale-1 > scale-2 AND scale-1 < scale-3` for 1st result and opposite `scale-1 <= scale-2 OR scale-1 >= scale-3`.
Now you can do it without moving your mind on 2nd: `NOT(scale-1 > scale-2 AND scale-1 < scale-3)`.

Internal improvements:

* Cleanup external modules: remove bad and excessive files (184KB)
* Replace "quizes" to "quizzes" in plugin descriptions ))


### 0.14.3 ###

Improve compatibility with other custom categories (for example WooCommerce)

* Check for taxonomy object type on archieve pages


### 0.14.2 ###

Prepare plugin to styling addon

* Allow dependencies in plugin's style
* Fix links to tests in respondents results


### 0.14.1 ###

Make compatible with polylang plugin

* Add post_type in result's rewrite to integrate with polylang


### 0.14 ###

Update addons centrally via standard plugins updates

* Add addon updater and setup it
* Add Czech and Slovak translations

Internal improvements:

* Improve activation and update reliability (fix migrations)
* Avoid section's questions false-duplicates (for sections addon)


### 0.13.2 ###

Update database after plugin update

* Now you don't have to deactivate and activate plugin after every update!
* Tested on standalone mock-plugin


### 0.13.1 ###

Fix result permalinks with front prefixes

* Make permalinks like /archives/%post_id% work for both tests and results


### 0.13 ###

Respondent passings for user and admin with improved usability

* Respondent can view own passings in wordpress admin area
* Significantly improved respondents passings: search/sort/toggle by columns and setup items per page
* Add Persian (Iran) locale
* Fix test page in Internet Explorers before 9.0

Internal improvements:

* Speed-up build, which will allow to test new features faster
* Rename passing columns in WP style, which is just perfectionism, but who knows


### 0.12.1 ###

Improve questions fill usability by highlighting non-answered

* Remove disabled button state and highlight to respondent non-answered questions
* Make required attributes compatible with screen-readers
* Make sure new validation is compatible with old browsers

Internal improvements:

* Upgrade jQuery for old WordPress at form fill
* Fix tests under old WP by using only extraversion scale (1st always)
* Add more extension points for fields addon


### 0.12 ###

Speed-up tests saving, improve respondents results' and add new locale

* Radically speed-up saving of tests with many scores (many questions, answers and scales)
* Improve respondents results' table look by making it non-fixed
* Add Spanish locale
* Update tests to the latest WP version
* Add extension points for fields addon


### 0.11.4 ###

Fix content comparing in duplicate protection

You should upgrade to this version only if:

* There are no questions on test page
* Test's content text is small (less than 255 chars)
* You have plugin that also adds something to content


### 0.11.3 ###

Fix strict settings catchable error in steps

Upgrade notice:

* You should upgrade to this version only if nothing works on test page and your PHP error settings are strict


### 0.11.2 ###

Fix many answers on many steps

* Was stuck on 2nd step


### 0.11.1 ###

Improve steps generating (for sections addon)

Upgrade notice:

* You should upgrade to this version only if you have sections addon


### 0.11 ###

Add one-per-page questions and fix annoing database collation issue

* Add option when respondent could read only one question at a time on page, to avoid to see through pattern
* Fix annoing database latin1 collation problem for non-latin languages when question marks shown instead of text


### 0.10.1 ###

Fix incompatibility with themes/plugins that changes post form attributes

* Use more robust way to pack metadata in edit form fields
* All these dances are to minify fields numbers

Upgrade notice:

* You shouldn't upgrade to this version if all is working
* Only if it was working, then you install something (another plugin/theme) and all was broken


### 0.10 ###

Save respondent in results and add addons base

* Save respondent from logged in user
* Add external addons base
* Test minimum score error


### 0.9.5 ###

Update locales, minor fixes and cleanup download file

* Add Chinese and Brazilian locales
* Fix activation under windows
* Fix taxonomy sortable containers look
* Fix plugin uninstallation
* Remove excessive and old files from download zip


### 0.9.4 ###

Add more math operators, native FR lang and improve scales' chart #2

* Avoid rotating text labels when we have too many scales
* Show mini-annotations like abbrevirations always near data values
* Make annotations as popups instead of tags
* Improve one-scale case
* Translate update reviewed locale for FR lang
* Add more math operators: "+*/"
* Enable advanced options for default test


### 0.9.3 ###

Improve scales' chart

* Use ratio (percents) when scales lengths differs
* Rotate text labels to 45 degrees when we have too many scales


### 0.9.2 ###

Fix scale`s max calc when in question we have few answers with scores


### 0.9.1 ###

Allow respondent to select multiple answers per question on test page


### 0.9 ###

Scales chart, progress percentage, sorting and respondents' results

* Visualize scales values with chart (at the same time scales can be hidden)
* Show percentage of answered questions in browser title
* Allow to sort test's answers/scales/results manually and scales by scores sum (implies from more to less)
* Add simple "Respondents' tests results" table in admin area
* Improve scales/results descriptions: allow more HTML tags (headers, lists, images and hr) and add support for "read more"
* Translate new strings and update reviewed locales for IT, NL and BG langs

Fixes:

* Fix results getting in non-published yet test (for example in preview mode)
* Fix questions/scores editor width when test has too many scales

Internal improvements:

* Research and fix stable continious integration fails
* Move answers input inside labels (will improve rendering on some theme)
* Add semantic CSS classes to scales/results, for example: "result result-id-11 result-slug-result-phlegmatic result-index-0 title"
* Add placeholders to questions form
* Pass data to javascript in more stable way


### 0.8.1 ###

Fix external library and update translations

* Fix external library to allow uppercased table names
* Update translations (Dutch now native)


### 0.8 ###

Add test page settings

* Allow to reset answers and customize button caption
* Save user agent in passing
* Update translations

Internal improvements:

* Fix attachment URLs
* Add CSS class to body on passing pages
* Exit after passing redirect


### 0.7.1 ###

Fix bad external library version


### 0.7 ###

Give each passing own URL to allow sharing and add two result page options

* Save passings in DB and redirect to them by URLs (with client's ip and device uuid)
* Add result page options for scales and test description
* Add Turkish translation
* Speed-up plugin by not updating rewrite rules everytime
* Fix conflict with scroll-triggered-box plugin
* Update translations

### 0.6.4 ###

Italian translation added


### 0.6.3 ###

Fix featured image incompatibilities with Jetpack shortcode module


### 0.6.2 ###

Inherit post's CSS classes onto test


### 0.6.1 ###

Fix apostrophe problem (slashes) and update tests to be compatible to WP 4.1


### 0.6 ###

Individual answers

* Add individual answers feature
* Update locales for main languages
* Migrate existing tests on new questions-answers model
* Allow to individualize answers: custom global answer title for question
* Use "Test Categories" in admin menu to differ with post categories


### 0.5.4 ###

Update German translation


### 0.5.3 ###

Add standard category to tests

* Update README about Bulgarian translation


### 0.5.2 ###

Fix fatal error incompatibility with wordpress-seo plugin

* Adding Bulgarian translation


### 0.5.1 ###

Add tags, improve formulas editor and docs

* Prefix screenhots by zeros
* Remove session stuff to avoid openbasedir bug
* Update compatible up to WP 4.0
* Swap formulas and results columns in editor
* Add tags to test


### 0.5 ###

Localization and quick fill

* Localize to six languages with help of [Transifiex](https://www.transifex.com/projects/p/wp-testing/)
* Add understandable labels for no-questions/no-answers and other no-something cases in test editor
* Quick fill questions from text
* Quick fill scores from questions separated by commas
* Update docs and add screenshots


### 0.4.2 ###

Fix pages disapperance and form formatting

* Fix pages disapperance
* Fix fill form (public) formatting conflict with wpautop (for example under Monaco theme)


### 0.4.1 ###

Fix tests preview mode

* Not changing main WP query when in preview


### 0.4 ###

Display tests in blog everywhere same as posts

* Display tests on homepage, in categories and other places just like posts
* Allow to hide individual tests from homepage by "Publish on the home page" setting at publish box
* Fix quickedit for posts
* Fix quickedit for tests by saving test part only in full edit mode
* Minimize possibility of "Max post vars" warning by minizing the number of hidden inputs

### 0.3 ###

Test plugin functionality in 37 combinations of WP and PHP and fix found problems

* Test under WordPress from 3.2 to latest 4.0 and PHP from 5.2 to 5.5
* Fix plugin under non-latest WP versions
* Add test's buttons only in plugin's test editor, not in post's editor
* Use more styled headers at results page
* Use dashicons in admin only on WP that knows about it (>=3.8)
* Allow empty source in formula (with migration)
* Fix questions adding when scales, results and answers checked

Non-latest WP versions fixes (programmer's language):

* Avoid deprecated function in formula
* Add build status image into readme
* Remove another not-existing column from wp_posts under WP 3.6
* Check if we are at test screen for WP 3.2
* Move styles and scripts in editor and passer into head to fix under WP 3.2
* Fix test delete under WP 3.2 by clearing records cache
* Remove 3rd unused param from save_post subscription
* Avoid WP_Post in Test Editor
* Avoid direct usage of WP_Post class
* Avoid direct usage of WP_Screen class
* Fix minor notice under old WP version
* Add type=text to all inputs to fix ugly inputs under old WPs


### 0.2.5 ###

Fix test creation (empty scale sum broken)


### 0.2.4 ###

Correctly uninstall plugin


### 0.2.3 ###

Fix activation on PHP below 5.4


### 0.2.2 ###

Update description to correct English version


### 0.2.1 ###

Fix PHP 5.2 parse error (not affects latest PHP versions)


### 0.2 ###

Connecting scales scores with results through formulas.

* Formulas parser undertands variables and comparision operators like "less", "more", "same", "not same", "and", "or"
* Formulas editor added with buttons of scales and comparision operators
* Show scale scores totals at the top of questions editor and on the formulas editor buttons
* Add shortcut buttons to the top of content editor: Add New Questions, Edit Questions, Edit Formulas
* Show test results calculated through formulas above above scales bars on the results page


### 0.1.4 ###

Test passing error fixed

* Manual relashionships naming
* Misspell in one of tables names


### 0.1.3 ###

Another plugin activation problems fixed

* Permissions on migrations directory
* Working under MySQL engine named MyISAM and in mixed InnoDB/MyISAM cases


### 0.1.2 ###

Bump stable tag to apply previous hotfix on wordpress plugins


### 0.1.1 ###

Plugin activation hotfix

* In initial release migrations dir taken from wordpress dir rather than be hardcoded.
Locally all was ok as always, but not on your wordpresses :(
* Upgrade notice added


### 0.1 ###

Initial release

* Add shortcode for tests lists: wptlist
* Edit tests, answers (global), scales, results and categories through admin
* Edit tests questions and scores (question -> answer -> scale -> score value)
* Show test page, allowing to redefine it's template if needed
* Allow to send test form only when all questions selected
* Show test results by scales totals
* Add eysenck personality inventory example


### 0.0 ###
* Init repo and files


== Upgrade Notice ==

### 0.17.1 ###
Fix incorrect content processing. You must upgrade if you are using 0.17 currently

### 0.13.2 ###
Now you don't have to deactivate and activate plugin after every update

### 0.7.1 ###
Fix bad external library version

### 0.5.2 ###
Fix fatal error incompatibility with wordpress-seo plugin

### 0.4.2 ###
Fix page disppearance and fill form broken formatting

### 0.4.1 ###
Fix tests preview mode

### 0.3 ###
Plugin now more stable under WP 3.2 to latest and PHP from 5.2 to 5.5

### 0.2.5 ###
Test creation from scratch now should work

### 0.2.3 ###
Plugin activation on PHP below 5.4 fixed

### 0.2.1 ###
Results and formulas added (parse error fixed)

### 0.1.4 ###
Test passing fatal error found and fixed

### 0.1.3 ###
Plugin activation fatal error found and fixed

