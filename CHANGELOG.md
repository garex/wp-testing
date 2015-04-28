
## Changelog ##


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

