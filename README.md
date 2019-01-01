# Psychological tests & quizzes #

**Contributors:** ustimenko, starlift, ufukluker, mimaes, memjavad, metavoor, natchalike, it2core, champ1on, rezaamaleki, cristipere, osfans, chrispeiffer, jacha, ilariarizzo, borrypsy, coach2talk, ikurtuldu

**Tags:** psychological, testing, test, quiz

**Requires at least:** 3.2

**Tested up to:** 5.0

**Stable tag:** 0.21.14

**License:** GPLv3

**License URI:** http://www.gnu.org/licenses/gpl-3.0.html


Create psychological tests/quizzes with scales connected to results through simple formulas like "extraversion > 50%"

## Description ##

Create typical or advanced psychological tests (quizzes, assessments) with **questions** and **answers**. A respondent answers and receives **results**.

What's inside? **Scales** associated with the particular answer by **scores**. **Simple formulas** like `extraversion > 50%` upon calculation give you **results**.

http://www.youtube.com/watch?v=VkbWn54neB0


### Respondent can ###

* View published test at homepage or at its own URL
* Pass the test by answering all questions' answers
* Run the test in single page or by one question per step
* See the passing progress in browser's title
* Get own individual results after running the test on standalone page, share them


### Logged-in respondent can ###

* View passing history in personal area


### Test author can ###

* Create and edit test in test editor like post editor
* Manage related and needed for test scales/results/global answers
* Edit results/scales descriptions (visual mode possible with the help of `visual-term-description-editor` plugin)
* View and filter tests passings
* Reorder scales/results/global answers
* Edit formulas with formulas editor
* Use total scores by scale as formula variable: extraversion > 50%
* Use  concrete question's answer as formula variable: question_5_1 OR question_9_7.
* Quick fill questions from text and scores from combination of scales/answers
* Customize test page options


### Long story ###

Edit scales, results and categories through **wordpress standard editors**, like categories editor. Associate them in sidebar. Change test questions, answers and formulas in standard wordpress metaboxes, below content. Reorder them if you like drag-n-drop.

At the top of content editor there are **buttons for quick access** to those metaboxes: Add New Questions, Edit Questions and Scores, Edit Formulas. If you want more control, you may add to them HTML tags like: headers, lists, images, hr and "read more" tag taken from post editor.

**Answers**  can be global to test — when all questions have the same answers. For example: "Yes", "No", "I'm not sure". Or you can use **individual answers** to add individual answer to each question. And the third way is to use global answers, but individualize their titles: not just "Yes", but "Yes, I do so and so", when you use it for the particular question. Some tests have this pattern.

Simple **formulas editor** has buttons for each scale (with sum of it's scores), question/answer button and allowed comparisions/operators: `<`, `>`, `<=`, `=>`, `<>`, `AND`, `OR`, `( .. )`, `NOT ( .. )`, `+`, `-`, `*`,  `/`.

Tests are like posts — they appear on home page and inside their categories pages if selected. But if you don't want your test to appear on home page, you can uncheck **"Publish on the home page"** in "Publish" metabox and this test will not appear on homepage.

**Quick fill** for questions and scores minimizes author's time. You can **quick fill questions from text** and they will fill appropriate fields. Same way you can **Quick fill scores** in many questions some answer+scale combination.

Respondent will get **results** on it's own individual passing page, which will allow share it. Logged-in respondent can see own results in admin area above the "Profile" page. There will be table with columns like: passing number, link, test, scales, results and date. It's possible to search/sort by test and date columns.

**Passings** are saved in DB with respondent's ip and device unique identifier. They are shown at "Respondents' results" table under "Tests" menu. It allow to see if someone will have many passings from same computer/smartphone/another device, which scales/results respondent have for concrete passing and ability to open it from there. If respondent was a logged in user — then you will see it in "Username" column with a link to profile. "Respondents' results" can be searched/sorted by most of it's columns. You can setup which columns you want to see there and how many passings per page you want to see.

**Test** page can be customized: reset answers on "Back" button, use your own caption for "Get Test Result" button, allow multiple answers per question, show percentage of answered questions and show one question per page.
**Results** page also can be customized: when you need to show/hide scales or test description on it; when you want to show scales chart or sort scales by scores sum.

Plugin localized into many languages: English, German, French, Dutch, Swedish, Bulgarian, Italian, Turkish, Chinese, Brazilian, Spanish, Persian, Czech, Slovak, Thai, Arabic, Romanian, Greek, Icelandic, Hebrew and Russian. English, French, Dutch, Bulgarian, Italian, Turkish, Chinese, Brazilian, Spanish, Persian, Czech, Slovak, Thai, Arabic, Romanian, Greek, Polish, Icelandic, Hebrew and Russian have good quality (native speakers) — others need review. You can easily add your language through excellent [Transifiex](https://www.transifex.com/projects/p/wp-testing/) service. **Translators** and *reviewers* are kindly welcome! See http://wp-translations.org/join/ for instructions.

**Quality** and **compatibility** are taken really seriously. Plugin tested on [more than 30 combinations](https://travis-ci.org/garex/wp-testing) of WordPress (from 3.2 to latest) and PHP (from 5.2 to 7.0) plus three custom combinations: for old MySQL storage engine (MyISAM), with few popular plugins and in multisite mode. So you can be sure, that it will just work, even if you don't have the latest WordPress or your hosting doesn't have the latest versions of PHP/MySQL. [Build status image](https://travis-ci.org/garex/wp-testing.svg?branch=develop) is available.

[![Build Status](https://travis-ci.org/garex/wp-testing.svg?branch=develop)](https://travis-ci.org/garex/wp-testing)

PS: **If something broken or doesn't work**, pls create new topic in ["Support" tab](https://wordpress.org/support/plugin/wp-testing)! Good support topic describes problem and have WP version and other plugins that you have in it.

### Thank you board ###

* For Bulgarian translation thanks to Borry Semerdzhieva <borry.semerdzhieva@gmail.com>
* For German translation thanks to Sascha <info@newwaystec.com>
* For Italian translation thanks to Ilaria Rizzo <dott.rizzo.ilaria@gmail.com>
* For Turkish translation thanks to Islam Kurtuldu and Ufuk Luker — http://ufukluker.com/
* For Dutch translation thanks to Jacha Heukels <info@orthomanueeldierenarts.nl> and Patrick van de Kerkhof <patrick@metavoor.nl>
* For French translation thanks to Christophe Peiffer <chris.peiffer@gmail.com>
* For Chinese translation thanks to Kyle Wang — https://github.com/osfans
* For Brazilian translation thanks to Cristiano Pereira da Conceição <cristiano@cristiano-coach.com.br>
* For Spanish translation thanks to Jon Ca — https://facebook.com/joncast
* For Persian (Iran) translation thanks to Reza Maleki <rezaa.maleki@gmail.com>
* For Czech translation thanks to Dalis Dobrota — clubseznamka.cz
* For Slovak translation thanks to Martin Oravec <oravec@it2core.sk>
* For Thai translation thanks to Natcha Wiratwattanakul <natchalike@gmail.com>
* For Arabic translation thanks to Mohammed Jawad <info@researchgate.asia>
* For Romanian translation thanks to Maria Estela Mihoc <maria_estela_mihoc@yahoo.com>
* For Greek translation thanks to Elektra Manousis <safiragon@yahoo.gr>
* For Polish translation thanks to Maciej Dzierżek — https://bezdechu.pl/
* For Icelandic translation thanks to Gunnar Geir Johannsson <gunnargj@gmail.com>
* For Hebrew translation thanks to Noam Lederman <noaml@noamlederman.co.il>

### Paid add-ons & support ###

There is no single "Pro"/"Premium" version with features, but each [paid add-on](https://docs.google.com/spreadsheets/d/1BrZv6gpIo0QV21p42oJ9KIO5jZzqugOUB1GqQOeQqEY/edit?usp=sharing) has it's own feature.

Implemented and ready to run:

* **Custom Fields**. Add custom form fields like name, email, sex and etc. Use their values in formulas and see in respondents' results. Denote required/optional fields, place fields before or after questions.
* **Questions Sections**. Group questions into sections and display each section on a different pages.
* **Styling**. Apply your style to questions, answers, scales and results. Choose your color/font/alignment and placement.
* **Export Results**. Export respondent's results into CSV tables. Choose which test, dates and columns do you want. Export just results or all data, including concrete answer on concrete question in concrete result.
* **Save Results in PDF**. Respondent will be able to get results in PDF form.
* **Redirect to Custom Results Page**. Respondent will be redirected to custom built URL(s) where you can pass as params scales, results and result key. URL could be single for whole test or individual for each result.
* **Computed Variables**. Create computed variables from existing variables and shortcode any variable at results page.
* **Email results to Author and Respondent**. Auto-notify author by email about respondent's result and also send it to respondent's email.

For complex test logic or plugin environment specific issues [paid support](https://docs.google.com/document/d/1eHQB69neQJ68xl3vT-x4cHERZTBskq2L0x47AjUPyKM/edit?usp=sharing) is available.


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

Another possible source of troubles is mis-configured [mod_security](https://www.modsecurity.org/). Ask your hoster about it. If it could be disabled or reconfigured to avoid false-positives, then your hoster is good. Otherwise it's better to switch to more competent hoster.

### I see some "Fatal error: bla-bla-bla" ###

Try to find there phrases like "Class ... not found" or "Call to undefined function ...". If it's the case — then your current hoster has disabled some PHP extension. Contact with your hosting company to enable them. Most popular missing extension is "mysqli".

### How to start? Where is documentation? ###

See the video, screenshots and example test. We will not plan to create any documentation. For complex test logic [paid support](https://docs.google.com/document/d/1eHQB69neQJ68xl3vT-x4cHERZTBskq2L0x47AjUPyKM/edit?usp=sharing) is available.

### Shortcodes? ###

**wpt_tests** — the list of tests. Attributes (allowed values): sort/reverse (id, title, created, modified, status, name, comments), max (number), id (numbers separated by commas), list (values for CSS `list-style-type`), class (any CSS class name).

**wpt_test_read_more** — the title of test, text before "more" and "Start Test" button. Attributes (allowed values): id/name (id or name or your test), start_title (any text), class (any CSS class name).

**wpt_test_first_page** — the first page of the test with title. Attributes (allowed values): id/name (id or name or your test), class (any CSS class name).

### Wonderful, but I want to have this, that and those feature in plugin ###

Create new support topic if same feature topic not yet created and describe there what do you want. You can check already created topics from tags list.
Some features already implemented or planned to as a paid addons — see sticked topic at forum.

### I want some feature here and now, I can pay you ###

WordPress as platform good as it's opensource and popular — you can find someone who knows it and it's technologies for your tasks.
But if you think it should be me — then contact me.


## Screenshots ##

01. Test editing section with menu in admin
02. There are fast access buttons like "Edit Questions and Answers" at the top of the page. Test page and results page can be customized from sidebar
03. Under "Edit Scores" every scale has a sum of scores. At "Edit Questions and Answers" box we can add to each question individual answers. The choise of answers and scales is available in the sidebar. They can be reordered by drag-n-drop
04. The "Quick Fill Scores" box allows us quickly enter scores from the questions separated by commas
05. Fast adding questions from text. Some boxes could be maximized, which helps in case of huge lists or tables
06. Editing formulas
07. The example of the test with scores. Some answers are individual and some are individualized
08. Respondents’ test results in admin area. Test link will open test in edit mode and view link allow to see test result
09. User see own tests results in admin area
10. Ready test on the home page
11. The page with the description of the test, questions and answers
12. Unanswered questions are highlighted to respondent
13. Get test results after all questions are answered
14. The result page on it`s own URL contains both the result of the test and the scales that create a result
15. Scale description with "more..." text closed
16. Scale description with "more..." text opened (after clicking on "more" link)
17. A test without scores is shown like a "Test is under construction"
18. Answers titles are those that was entered
19. Test results with scales chart. Hovered scale shows it`s value and title in dynamic tag
20. In case when scales has different length (possible max total) they are shown as percents
21. Multiple answers per question are also possible
22. One question per page also allowed. On first page we see test description, "Next" button and pages counter
23. On second page description not shown
24. On last page counter not shown and button changes back to "Get Test Results"
