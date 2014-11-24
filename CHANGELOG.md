
## Changelog ##


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

