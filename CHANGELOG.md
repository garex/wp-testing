
## Changelog ##

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

