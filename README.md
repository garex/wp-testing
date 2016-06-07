# Psychological tests & quizzes #

**Contributors:** ustimenko, natchalike, it2core, champ1on, rezaamaleki, cristipere, osfans, chrispeiffer, jacha, ilariarizzo, borrypsy, coach2talk, ikurtuldu
**Donate link:** https://www.paypal.me/Ustimenko/19.83eur
**Tags:** psychological, testing, test, quiz
**Requires at least:** 3.2
**Tested up to:** 4.5
**Stable tag:** 0.18.9
**License:** GPLv3
**License URI:** http://www.gnu.org/licenses/gpl-3.0.html

Create psychological tests/quizzes with scales connected with results through simple formulas like "extraversion > 50%"

## Description ##

With the help of this plugin now you can create typical and advanced psychological tests (quizzes, assessments).

Typical psychological test consists of **questions** and **answers**. A respondent answers all of the questions and gets **results**. This is how the box outside looks like.

Inside the box we also have **scales** each connected with the particular answer with **scores** and **results**, that are calculated with the help of **simple formulas** like: `extraversion > 50%`. Where "extraversion" is a sum of respondent's scores from extraversion scale. Concrete respondent's question's answer in formulas also possible: `question_5_1 OR question_9_7`.

We didn't invent anything new — all this has been already invented in 19th century.

> If you like the plugin, feel free to [donate via PayPal](https://www.paypal.me/Ustimenko/19.83eur) or rate it (on the right side of this page). Thanks a lot! :)

http://www.youtube.com/watch?v=tT3d8Jdm7kY

Test answers, scales, results and categories are edited through **wordpress standard editors**, similar to categories editor. You can associate them in sidebar. Whether test questions, answers and formulas are edited with standard wordpress metaboxes, behind content. You can even reorder them if you like by drag-n-drop. At the top of content editor you have **button for quick access** to those metaboxes: Add New Questions, Edit Questions and Scores, Edit Formulas. If you want more control then you are allowed to add to them not allowed out-of-the box HTML tags like: headers, lists, images, hr and "read more" tag taken from post editor.

**Answers**  can be global to test — when all questions have the same answers. For example: "Yes", "No", "I'm not sure". Or you can use **individual answers** to add individual answer to each question. And the third option is to use global answers, but individualize their titles: not just "Yes", but "Yes, I do so and so", when you use it for the particular question. Some tests have this pattern.

As to **formulas** you can be sure — we have simple formula editor, that has buttons for each scale (with sum of it's scores), question/answer button and allowed comparisions/operators: `<`, `>`, `<=`, `=>`, `<>`, `AND`, `OR`, `( .. )`, `NOT ( .. )`, `+`, `-`, `*`,  `/`.

Tests are treated for WordPress like posts — they appear on home page and inside their categories pages if selected. But if you don't want your test to appear on home page you can uncheck **"Publish on the home page"** in "Publish" metabox and this particular test will not appear on homepage.

To minimize author's time we have **Quick Fill** for questions and scores. You can **quick fill questions from text** and they will fill appropriate fields. Same way you can **Quick Fill Scores** in many questions some answer+scale combination.

Respondent will get **results** on it's own individual passing page, which will allow share it. Logged in respondent can see own results in admin area above the "Profile" page. There will be table with columns like: passing number, link, test, scales, results and date. It's possible to search/sort by test and date columns.

**Passings** are saved in DB with respondent's ip and device unique identifier. They are shown at "Respondents' results" table under "Tests" menu. It allow to see if someone will have many passings from same computer/smartphone/another device, which scales/results respondent have for concrete passing and ability to open it from there. If respondent was a logged in user — then you will see it in "Username" column with a link to profile. "Respondents' results" can be searched/sorted by most of it's columns. You can setup which columns you want to see there and how many passings per page you want to see.

**Test** page can be customized: reset answers on "Back" button, use your own caption for "Get Test Result" button, allow multiple answers per question, show percentage of answered questions and show one question per page.
**Results** page also can be customized: when you need to show/hide scales or test description on it; when you want to show scales chart or sort scales by scores sum.

Wp-testing localized into sixteen languages: English, German, French, Dutch, Swedish, Bulgarian, Italian, Turkish, Chinese, Brazilian, Spanish, Persian, Czech, Slovak, Thai and Russian. English, French, Dutch, Bulgarian, Italian, Turkish, Chinese, Brazilian, Spanish, Persian, Czech, Slovak, Thai and Russian have good quality (native speakers) — others need review. You can easily add your language through excellent [Transifiex](https://www.transifex.com/projects/p/wp-testing/) service. **Translators** and *reviewers* are kindly welcome! See http://wp-translations.org/join/ for instructions.

**Quality** and **compatibility** are taken really seriously. Plugin tested on [more than 30 combinations](https://travis-ci.org/garex/wp-testing) of WordPress (from 3.2 to 4.4) and PHP (from 5.2 to 7.0) plus three custom combinations: for old MySQL storage engine (MyISAM), with few popular plugins and in [multisite mode](https://circleci.com/gh/garex/wp-testing/tree/develop). So you can be sure, that it will just work, even if you don't have the latest WordPress or your hosting doesn't have the latest versions of PHP/MySQL. [Build status image](https://travis-ci.org/garex/wp-testing.svg?branch=develop) is available.

[![Build Status](https://travis-ci.org/garex/wp-testing.svg?branch=develop)](https://travis-ci.org/garex/wp-testing)

PS: **If something broken or doesn't work**, pls create new topic in ["Support" tab](https://wordpress.org/support/plugin/wp-testing)! Good support topic describes problem and have WP version and other plugins that you have in it. If you want some feature — also create topic. Donations as money or links to our site are welcome.

### Thank You Board ###

* For Bulgarian translation thanks to Borry Semerdzhieva <borry.semerdzhieva@gmail.com>
* For German translation thanks to Sascha <info@newwaystec.com>
* For Italian translation thanks to Ilaria Rizzo <dott.rizzo.ilaria@gmail.com>
* For Turkish translation thanks to Islam Kurtuldu
* For Dutch translation thanks to Jacha Heukels <info@orthomanueeldierenarts.nl>
* For French translation thanks to Christophe Peiffer <chris.peiffer@gmail.com>
* For Chinese translation thanks to Kyle Wang — https://github.com/osfans
* For Brazilian translation thanks to Cristiano Pereira da Conceição <cristiano@cristiano-coach.com.br>
* For Spanish translation thanks to Jon Ca — https://facebook.com/joncast
* For Persian (Iran) translation thanks to Reza Maleki <rezaa.maleki@gmail.com>
* For Czech translation thanks to Dalis Dobrota — clubseznamka.cz
* For Slovak translation thanks to Martin Oravec <oravec@it2core.sk>
* For Thai translation thanks to Natcha Wiratwattanakul <natchalike@gmail.com>

## Installation ##

1. Download plugin archive.
1. Unzip it.
1. Upload it to your plugins directory. It will create a 'wp-content/plugins/wp-testing/' directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Start with reviewing example Eysenck's Personality Inventory (EPI) test.
1. Or directly create your own test through Tests menu in admin area.

## Requirements ##

* WordPress version **3.2** or higher.
* PHP version **5.2.4** or higher (including **7.0** !).
* MySQL version **5.0** or higher.

## Frequently Asked Questions ##

### Plugin is not works or works improperly ###

Most possibly it's a conflict with your current theme or some of other plugin.
To check it — try to switch theme to default WP theme and see if it helps.
In case of plugins conflict try to disable other plugins one by one and check. Or disable all of them and check — it will help to understand if problem is on other plugins side.

### I see some "Fatal error: bla-bla-bla" ###

Try to find there phrases like "Class ... not found" or "Call to undefined function ...". If it's the case — then your current hoster has disabled some PHP extension. Contact with your hosting company to enable them. Most popular missing extension is "mysqli".

### How to start? Where is documentation? ###

Sorry we dont' have it yet. Start from screenshots section and try to understand what where should be. Also we have an example test under the hood — it should helps.

### Shortcodes? ###

**wpt_tests** — the list of tests. Attributes (allowed values): sort/reverse (id, title, created, modified, status, name, comments), max (number), id (numbers separated by commas), list (values for CSS `list-style-type`), class (any CSS class name).

**wpt_test_read_more** — the title of test, text before "more" and "Start Test" button. Attributes (allowed values): id/name (id or name or your test), start_title (any text), class (any CSS class name).

**wpt_test_first_page** — the first page of the test with title. Attributes (allowed values): id/name (id or name or your test), class (any CSS class name).

### Wonderful, but I want to have this, that and those feature in plugin ###

Create new support topic if same feature topic not yet created and describe there what do you want. You can check already created topics from tags list.
Some features already implemented or planned to as a paid addons — see sticked topic at forum.

### I want some feature here and now, I can pay you for this over many-many money ###

Sorry, I dont' have too much time for custom paid development. WordPress as platform good as it's opensource and popular — you can find someone else for your tasks.

### Which tags do you have at support forum? ###

Most giant are **[feature](https://wordpress.org/tags/wp-testing-feature)**, **[bug](https://wordpress.org/tags/wp-testing-bug)** and **[support](https://wordpress.org/tags/wp-testing-support)**. Other are groupped under them.

**Feature**: [alert required questions](https://wordpress.org/tags/wp-testing-alert-required-questions), **[answer rate](https://wordpress.org/tags/wp-testing-answer-rate)**, [answer type number](https://wordpress.org/tags/wp-testing-answer-type-number), [author](https://wordpress.org/tags/wp-testing-author), [chained steps strategy](https://wordpress.org/tags/wp-testing-chained-steps-strategy), [conflict plugin](https://wordpress.org/tags/wp-testing-conflict-plugin), [custom template](https://wordpress.org/tags/wp-testing-custom-template), [custom text](https://wordpress.org/tags/wp-testing-custom-text), [diagram settings](https://wordpress.org/tags/wp-testing-diagram-settings), **[diagram type](https://wordpress.org/tags/wp-testing-diagram-type)**, **[email results](https://wordpress.org/tags/wp-testing-email-results)**, [export import tests](https://wordpress.org/tags/wp-testing-export-import-tests), **[export results](https://wordpress.org/tags/wp-testing-export-results)**, [expose scores](https://wordpress.org/tags/wp-testing-expose-scores), [fields](https://wordpress.org/tags/wp-testing-fields), [for users](https://wordpress.org/tags/wp-testing-for-users), [formula absolute](https://wordpress.org/tags/wp-testing-formula-absolute), [formula division](https://wordpress.org/tags/wp-testing-formula-division), [formula not](https://wordpress.org/tags/wp-testing-formula-not), [hide scales](https://wordpress.org/tags/wp-testing-hide-scales), [hide test description](https://wordpress.org/tags/wp-testing-hide-test-description), [individual answers](https://wordpress.org/tags/wp-testing-individual-answers), **[low memory](https://wordpress.org/tags/wp-testing-low-memory)**, [multiline questions](https://wordpress.org/tags/wp-testing-multiline-questions), [multiple answers](https://wordpress.org/tags/wp-testing-multiple-answers), [negative scales sum](https://wordpress.org/tags/wp-testing-negative-scales-sum), [page title](https://wordpress.org/tags/wp-testing-page-title), [paid results](https://wordpress.org/tags/wp-testing-paid-results), [paid test](https://wordpress.org/tags/wp-testing-paid-test), [passing counter](https://wordpress.org/tags/wp-testing-passing-counter), [pdf results](https://wordpress.org/tags/wp-testing-pdf-results), [postprocess results](https://wordpress.org/tags/wp-testing-postprocess-results), [public scale names](https://wordpress.org/tags/wp-testing-public-scale-names), [publish homepage](https://wordpress.org/tags/wp-testing-publish-homepage), [question per page](https://wordpress.org/tags/wp-testing-question-per-page), [random question answer order](https://wordpress.org/tags/wp-testing-random-question-answer-order), [redirect](https://wordpress.org/tags/wp-testing-redirect), [reorder questions](https://wordpress.org/tags/wp-testing-reorder-questions), [reorder scales results answers](https://wordpress.org/tags/wp-testing-reorder-scales-results-answers), **[respondents results](https://wordpress.org/tags/wp-testing-respondents-results)**, **[results page](https://wordpress.org/tags/wp-testing-results-page)**, **[rich scales results](https://wordpress.org/tags/wp-testing-rich-scales-results)**, [scale bar orientation](https://wordpress.org/tags/wp-testing-scale-bar-orientation), **[scores decimal](https://wordpress.org/tags/wp-testing-scores-decimal)**, [sections](https://wordpress.org/tags/wp-testing-sections), [share results](https://wordpress.org/tags/wp-testing-share-results), **[shortcode](https://wordpress.org/tags/wp-testing-shortcode)**, [sort results](https://wordpress.org/tags/wp-testing-sort-results), [sort scales](https://wordpress.org/tags/wp-testing-sort-scales), **[styling](https://wordpress.org/tags/wp-testing-styling)**, [test page answers](https://wordpress.org/tags/wp-testing-test-page-answers), [time limit](https://wordpress.org/tags/wp-testing-time-limit), [use post category](https://wordpress.org/tags/wp-testing-use-post-category), [user to results](https://wordpress.org/tags/wp-testing-user-to-results), [variable question answer](https://wordpress.org/tags/wp-testing-variable-question-answer), [workaround](https://wordpress.org/tags/wp-testing-workaround).

**Bug**: [answer order](https://wordpress.org/tags/wp-testing-answer-order), [answers disappears](https://wordpress.org/tags/wp-testing-answers-disappears), [apostrophe](https://wordpress.org/tags/wp-testing-apostrophe), [conflict javascript](https://wordpress.org/tags/wp-testing-conflict-javascript), **[conflict plugin](https://wordpress.org/tags/wp-testing-conflict-plugin)**, **[conflict theme](https://wordpress.org/tags/wp-testing-conflict-theme)**, [cpu limit](https://wordpress.org/tags/wp-testing-cpu-limit), **[database collation](https://wordpress.org/tags/wp-testing-database-collation)**, [database engine](https://wordpress.org/tags/wp-testing-database-engine), [database old password format](https://wordpress.org/tags/wp-testing-database-old-password-format), [database prefix case](https://wordpress.org/tags/wp-testing-database-prefix-case), [form multipart](https://wordpress.org/tags/wp-testing-form-multipart), [formulas](https://wordpress.org/tags/wp-testing-formulas), [individual answers](https://wordpress.org/tags/wp-testing-individual-answers), **[migration](https://wordpress.org/tags/wp-testing-migration)**, [minimal score](https://wordpress.org/tags/wp-testing-minimal-score), **[missing mysqli](https://wordpress.org/tags/wp-testing-missing-mysqli)**, [missing tokenizer](https://wordpress.org/tags/wp-testing-missing-tokenizer), [multiple answers](https://wordpress.org/tags/wp-testing-multiple-answers), [multisite](https://wordpress.org/tags/wp-testing-multisite), [page 404](https://wordpress.org/tags/wp-testing-page-404), [php strict](https://wordpress.org/tags/wp-testing-php-strict), [quick fill](https://wordpress.org/tags/wp-testing-quick-fill), [roles](https://wordpress.org/tags/wp-testing-roles), [shortcode](https://wordpress.org/tags/wp-testing-shortcode), [test description limited](https://wordpress.org/tags/wp-testing-test-description-limited), [uninstall](https://wordpress.org/tags/wp-testing-uninstall), [value names required](https://wordpress.org/tags/wp-testing-value-names-required), **[virus](https://wordpress.org/tags/wp-testing-virus)**.

**Support**: [access](https://wordpress.org/tags/wp-testing-access), [addon](https://wordpress.org/tags/wp-testing-addon), [conditional](https://wordpress.org/tags/wp-testing-conditional), **[conflict plugin](https://wordpress.org/tags/wp-testing-conflict-plugin)**, **[conflict theme](https://wordpress.org/tags/wp-testing-conflict-theme)**, [css](https://wordpress.org/tags/wp-testing-css), [custom template](https://wordpress.org/tags/wp-testing-custom-template), [database config](https://wordpress.org/tags/wp-testing-database-config), [database connection](https://wordpress.org/tags/wp-testing-database-connection), [demo](https://wordpress.org/tags/wp-testing-demo), [diagram type](https://wordpress.org/tags/wp-testing-diagram-type), [dispute](https://wordpress.org/tags/wp-testing-dispute), [export import tests](https://wordpress.org/tags/wp-testing-export-import-tests), [export results](https://wordpress.org/tags/wp-testing-export-results), [fields](https://wordpress.org/tags/wp-testing-fields), [formula misprint](https://wordpress.org/tags/wp-testing-formula-misprint), **[formulas](https://wordpress.org/tags/wp-testing-formulas)**, [hide results](https://wordpress.org/tags/wp-testing-hide-results), [individual answers](https://wordpress.org/tags/wp-testing-individual-answers), **[low memory](https://wordpress.org/tags/wp-testing-low-memory)**, **[migration](https://wordpress.org/tags/wp-testing-migration)**, [missing mysqli](https://wordpress.org/tags/wp-testing-missing-mysqli), [mobile](https://wordpress.org/tags/wp-testing-mobile), [more example tests for free](https://wordpress.org/tags/wp-testing-more-example-tests-for-free), [multisite](https://wordpress.org/tags/wp-testing-multisite), **[non actual](https://wordpress.org/tags/wp-testing-non-actual)**, [not reproduced](https://wordpress.org/tags/wp-testing-not-reproduced), [publish homepage](https://wordpress.org/tags/wp-testing-publish-homepage), [reorder scales results answers](https://wordpress.org/tags/wp-testing-reorder-scales-results-answers), [respondents results](https://wordpress.org/tags/wp-testing-respondents-results), **[results page](https://wordpress.org/tags/wp-testing-results-page)**, [scores](https://wordpress.org/tags/wp-testing-scores), [server config](https://wordpress.org/tags/wp-testing-server-config), [server error 503](https://wordpress.org/tags/wp-testing-server-error-503), [shortcode](https://wordpress.org/tags/wp-testing-shortcode), [skip question](https://wordpress.org/tags/wp-testing-skip-question), **[styling](https://wordpress.org/tags/wp-testing-styling)**, [theme conflict](https://wordpress.org/tags/wp-testing-theme-conflict), [theme customizing](https://wordpress.org/tags/wp-testing-theme-customizing), [translation](https://wordpress.org/tags/wp-testing-translation), [upgrade](https://wordpress.org/tags/wp-testing-upgrade).

**Other**: [addon](https://wordpress.org/tags/wp-testing-addon), [changelog](https://wordpress.org/tags/wp-testing-changelog), [conflict plugin](https://wordpress.org/tags/wp-testing-conflict-plugin), [fix me for free](https://wordpress.org/tags/wp-testing-fix-me-for-free), [low memory](https://wordpress.org/tags/wp-testing-low-memory), [manual](https://wordpress.org/tags/wp-testing-manual), [non actual](https://wordpress.org/tags/wp-testing-non-actual), [reward](https://wordpress.org/tags/wp-testing-reward), **[scores](https://wordpress.org/tags/wp-testing-scores)**, **[translation](https://wordpress.org/tags/wp-testing-translation)**, [upgrade](https://wordpress.org/tags/wp-testing-upgrade).


## Screenshots ##

01. Test editing section with menu in admin
02. There are fast access buttons like "Add New Questions" at the top of the page. Test page and results page can be customized from sidebar
03. Here we can see "Edit Questions and Scores" box where every scale has a sum of scores. Also we can add to each question individual answers. The choise of answers and scales is available in the sidebar. They can be reordered by drag-n-drop.
04. The "Quick Fill Scores" box is opened that allows us quickly enter scores from the questions separated by commas. "Add Individual Answers" box also opened but it tells us to use "Test Answers" in case when answers are same
05. Fast adding questions from text
06. Editing formulas
07. The example of the test without scores. Some answers are individual and some are individualized
08. Respondents’ test results in admin area. Test link will open test in edit mode and view link allow to see test result
09. User see own tests results in admin area
10. Ready test on the home page
11. The page with the description of the test, questions and answers
12. Unanswered questions are highlighted to respondent
13. Get test results after all questions are answered
14. The result page on it`s own URL contains both the result of the test and the scales that create a result
15. Scale description with "more..." text closed
16. Scale description with "more..." text opened (after clicking on "more" link)
17. A test without scores is shown like a "Test is under construction". Answers titles are those that was entered
18. Test results with scales chart. Hovered scale shows it`s value and title in dynamic tag
19. In case when scales has different length (possible max total) they are shown as percents
20. Multiple answers per question are also possible
21. One question per page also allowed. On first page we see test description, "Next" button and pages counter
22. On second page description not shown
23. On last page counter not shown and button changes back to "Get Test Results"
