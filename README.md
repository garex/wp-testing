# Psychological tests & quizes #

**Contributors:** ustimenko, borrypsy, coach2talk, ilariarizzo, ikurtuldu  
**Donate link:** http://apsiholog.ru/psychological-tests/  
**Tags:** psychological, testing, test, quiz  
**Requires at least:** 3.2  
**Tested up to:** 4.1  
**Stable tag:** 0.8  
**License:** GPLv3  
**License URI:** http://www.gnu.org/licenses/gpl-3.0.html  

Create psychological tests/quizes with scales connected with results through simple formulas like "extraversion > 50%"

## Description ##

With the help of this plugin now you can create typical and advanced psychological tests (quizes, assessments).

Typical psychological test consists of **questions** and **answers**. A respondent answers all of the questions and gets **results**. This is how the box outside looks like.

Inside the box we also have **scales** each connected with the particular answer with **scores** and **results**, that are calculated with the help of **simple formulas** like: "extraversion > 50%". Where "extraversion" is a sum of respondent's scores from extraversion scale. We didn't invent anything new — all this has been already invented in 19th century.

Test answers, scales, results and categories are edited through **wordpress standard editors**, similar to categories editor. You can associate them in sidebar. Whether test questions, answers and formulas are edited with standard wordpress metaboxes, behind content. You can even reorder them if you like. At the top of content editor you have **button for quick access** to those metaboxes: Add New Questions, Edit Questions and Scores, Edit Formulas.

**Answers**  can be global to test — when all questions have the same answers. For example: "Yes", "No", "I'm not sure". Or you can use **individual answers** to add individual answer to each question. And the third option is to use global answers, but individualize their titles: not just "Yes", but "Yes, I do so and so", when you use it for the particular question. Some tests have this pattern.

As to **formulas** you can be sure — we have simple formula editor, that has buttons for each scale (with sum of it's scores) and allowed comparisions.

Tests are treated for WordPress like posts — they appear on home page and inside their categories pages if selected. But if you don't want your test to appear on home page you can uncheck **"Publish on the home page"** in "Publish" metabox and this particular test will not appear on homepage.

To minimize author's time we have **Quick Fill** for questions and scores. You can **quick fill questions from text** and they will fill appropriate fields. Same way you can **Quick Fill Scores** in many questions some answer+scale combination.

Respondent will get **results** on it's own individual passing page, which will allow share it. **Passings** are saved in DB with respondent's ip and device unique identifier. It will allow later to see if someone will have many passings from same computer/smartphone/another device.

**Results** page also can be customized: when you need to show/hide scales or test description on it.

Wp-testing localized into nine languages: English, Russian (native speaker), German, French, Dutch, Swedish, Bulgarian (native speaker), Italian (native speaker) and Turkish (native speaker). English, Russian, Bulgarian, Italian and Turkish have good quality — others need review. You can easily add your language through excellent [Transifiex](https://www.transifex.com/projects/p/wp-testing/) service. **Translators** and *reviewers* are kindly welcome! See http://wp-translations.org/join/ for instructions.

**Quality** and **compatibility** are taken really seriously. Plugin tested on [37 combinations](https://travis-ci.org/garex/wp-testing) of WordPress (from 3.2 to 4.0) and PHP (from 5.2 to 5.5) plus two custom combinations for old MySQL storage engine (MyISAM) and with few popular plugins. So you can be sure, that it will just work, even if you don't have  the latest WordPress or your hosting doesn't have the latest versions of PHP/MySQL. [Build status image](https://travis-ci.org/garex/wp-testing.svg?branch=develop) is available.

[![Build Status](https://travis-ci.org/garex/wp-testing.svg?branch=develop)](https://travis-ci.org/garex/wp-testing)

PS: **If something broken or doesn't work**, pls create new topic in ["Support" tab](https://wordpress.org/support/plugin/wp-testing)! Good support topic describes problem and have WP version and other plugins that you have in it. If you want some feature — also create topic. Donations as money or links to our site are welcome.

### Thank You Board ###

* For Bulgarian translation thanks to Borry Semerdzhieva (borry.semerdzhieva@gmail.com)
* For German translation thanks to Sascha (info@newwaystec.com)
* For Italian translation thanks to Ilaria Rizzo (dott.rizzo.ilaria@gmail.com)
* For Turkish translation thanks to Islam Kurtuldu


## Installation ##

1. Download plugin archive.
1. Unzip it.
1. Upload it to your plugins directory. It will create a 'wp-content/plugins/wp-testing/' directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Start with reviewing example Eysenck's Personality Inventory (EPI) test.
1. Or directly create your own test through Tests menu in admin area.

## Requirements ##

* WordPress version **3.2** or higher.
* PHP version **5.2.4** or higher.
* MySQL version **5.0** or higher.

## Screenshots ##

01. Test editing section with menu in admin
02. Test editing section. There are fast access buttons like "add new questions" at the top of the page. The choise of answers and scales is available in the sidebar
03. Here we can see "Edit Questions and Scores" box where every scale has a sum of scores. Also we can add to each question individual answers
04. The "Quick Fill Scores" box is opened that allows us quickly enter scores from the questions separated by commas. "Add Individual Answers" box also opened but it tells us to use "Test Answers" in case when answers are same
05. Fast adding questions from text
06. Editing formulas
07. The example of the test without  scores. Some answers are individual and some are individualized
08. Ready test on the home page
09. The page with the description of the test, questions and answers
10. The button is disabled until all questions are not answered
11. Get test results after all questions are answered
12. The result page on it`s own URL contains both the result of the test and the scales that create a result
13. A test without scores is shown like a "Test is under construction". Answers titles are those that was entered
