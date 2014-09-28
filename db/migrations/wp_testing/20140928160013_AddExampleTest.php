<?php

class AddExampleTest extends Ruckusing_Migration_Base
{
    public function up()
    {
        $pluginUrl          = WP_PLUGIN_URL;

        $posts              = WP_DB_PREFIX  . 'posts';
        $terms              = WP_DB_PREFIX  . 'terms';
        $termTaxonomy       = WP_DB_PREFIX  . 'term_taxonomy';
        $termRelashionships = WP_DB_PREFIX  . 'term_relationships';
        $questions          = WPT_DB_PREFIX . 'questions';
        $scores             = WPT_DB_PREFIX . 'scores';

        $this->execute("
            INSERT INTO $posts (
                post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt,
                post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged,
                post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order,
                post_type, post_mime_type, comment_count
            ) VALUES (
                1, NOW(), NOW(), 'The Eysenck Personality Inventory (EPI) measures two pervasive,  independent dimensions of personality,  Extraversion-Introversion and Neuroticism-Stability,  which account for most of the variance in the personality domain. Each form contains 57 \"Yes-No\" items with no repetition of items. The inclusion of a falsification scale provides for the detection of response distortion. The traits measured are Extraversion-Introversion and Neuroticism.<!--more--> When you fill out Eysenck’s Personality Inventory (EPI) you get three scores.\r\n<ul>\r\n	<li>The ‘lie score’ is out of 9. It measures how socially desirable you are trying to be in your answers.Those who score 5 or more on this scale are probably trying to make themselves look good and are not being totally honest in their responses.</li>\r\n	<li>The ‘E score’ is out of 24 and measures how much of an extrovert you are.</li>\r\n	<li>The ‘N score’ is out of 24 and measures how neurotic you are.</li>\r\n</ul>\r\nTo interpret the scores,  your E score and your N score are plotted on a graph from which you can read your personality characteristics. The nearer the outside of the circle you are,  the more marked are the personality traits. Please note that the EPI is a very simplistic type of personality measurement scale,  so if you have come out as a personality that does not match what you thought before you took the test,  you are probably right and the test is probably wrong!\r\n\r\n<img src=\"$pluginUrl/wp-testing/img/EPI-scales.png\" alt=\"EPI scales\" />\r\n<h2>Instructions</h2>\r\nHere are some questions regarding the way you behave,  feel and act. Each question has radio buttons to answer YES or NO. Try to decide whether YES or NO represents your usual way of acting or feeling. Then check those radio named YES or NO. Work quickly,  and don’t spend too much time over any question,  we want your first reaction,  not a long drawn-out thought process. The whole questionnaire shouldn’t take more than a few minutes. Be sure not to omit any questions. Start now,  work quickly and remember to answer every question. There are no right or wrong answers,  and this isn’t a test of intelligence or ability,  but simply a measure of the way you behave.', 'Eysenck’s Personality Inventory (EPI) (Extroversion/Introversion)', '', 'publish', 'closed', 'closed', '', 'eysencks-personality-inventory-epi-extroversionintroversion', '', '', NOW(), NOW(), '', 0, UUID(), 0, 'wpt_test', '', 0);

            INSERT INTO $terms (name, slug, term_group) VALUES
            ('Yes', 'answer-yes', 0),
            ('No', 'answer-no', 0),
            ('Extraversion/Introversion', 'scale-extraversion-introversion', 0),
            ('Neuroticism/Stability', 'scale-neuroticism-stability', 0),
            ('Lie', 'scale-lie', 0);
        ");

        $testId = $this->field("SELECT ID FROM $posts WHERE post_type = 'wpt_test' ORDER BY ID DESC LIMIT 1");
        $yesId  = $this->field("SELECT term_id FROM $terms WHERE slug = 'answer-yes' LIMIT 1");
        $noId   = $this->field("SELECT term_id FROM $terms WHERE slug = 'answer-no' LIMIT 1");
        $eiId   = $this->field("SELECT term_id FROM $terms WHERE slug = 'scale-extraversion-introversion' LIMIT 1");
        $nsId   = $this->field("SELECT term_id FROM $terms WHERE slug = 'scale-neuroticism-stability' LIMIT 1");
        $lieId  = $this->field("SELECT term_id FROM $terms WHERE slug = 'scale-lie' LIMIT 1");

        $this->execute("
            INSERT INTO $termTaxonomy (term_id, taxonomy, description, parent, count) VALUES
            ($yesId, 'wpt_answer', '', 0, 1),
            ($noId,  'wpt_answer', '', 0, 1),
            ($eiId,  'wpt_scale',  'Extraversion is characterized by being outgoing,  talkative,  high on positive affect (feeling good),  and in need of external stimulation. According to Eysenck\'s arousal theory of extraversion,  there is an optimal level of cortical arousal,  and performance deteriorates as one becomes more or less aroused than this optimal level. Arousal can be measured by skin conductance,  brain waves or sweating. At very low and very high levels of arousal,  performance is low,  but at a better mid-level of arousal,  performance is maximized.\r\n\r\nExtraverts,  according to Eysenck\'s theory,  are chronically under-aroused and bored and are therefore in need of external stimulation to bring them up to an optimal level of performance. About 16 percent of the population tend to fall in this range.\r\n\r\nIntroverts,  on the other hand,  (also about 16 percent of the population) are chronically over-aroused and jittery and are therefore in need of peace and quiet to bring them up to an optimal level of performance.\r\n\r\nMost people (about 68 percent of the population) fall in the midrange of the extraversion/introversion continuum,  an area referred to as ambiversion.', 0, 1),
            ($nsId,  'wpt_scale',  'Neuroticism or emotionality is characterized by high levels of negative affect such as depression and anxiety. Neuroticism,  according to Eysenck\'s theory,  is based on activation thresholds in the sympathetic nervous system or visceral brain. This is the part of the brain that is responsible for the fight-or-flight response in the face of danger. Activation can be measured by heart rate,  blood pressure,  cold hands,  sweating and muscular tension (especially in the forehead).\r\n\r\nNeurotic people — who have low activation thresholds,  and unable to inhibit or control their emotional reactions,  experience negative affect (fight-or-flight) in the face of very minor stressors — are easily nervous or upset.\r\n\r\nEmotionally stable people — who have high activation thresholds and good emotional control,  experience negative affect only in the face of very major stressors — are calm and collected under pressure.', 0, 1),
            ($lieId, 'wpt_scale',  'It measures how socially desirable you are trying to be in your answers. Those who score 5 or more on this scale are probably trying to make themselves look good and are not being totally honest in their responses.', 0, 1);
        ");
        $this->execute("
            INSERT INTO $termRelashionships (object_id, term_taxonomy_id, term_order) VALUES
            ($testId, (SELECT term_taxonomy_id FROM $termTaxonomy WHERE term_id = $yesId), 1),
            ($testId, (SELECT term_taxonomy_id FROM $termTaxonomy WHERE term_id = $noId),  2),
            ($testId, (SELECT term_taxonomy_id FROM $termTaxonomy WHERE term_id = $eiId),  1),
            ($testId, (SELECT term_taxonomy_id FROM $termTaxonomy WHERE term_id = $nsId),  2),
            ($testId, (SELECT term_taxonomy_id FROM $termTaxonomy WHERE term_id = $lieId), 3);

            INSERT INTO $questions (test_id, question_title) VALUES
            ($testId, 'Do you often long for excitement?'),
            ($testId, 'Do you often need understanding friends to cheer you up?'),
            ($testId, 'Are you usually carefree?'),
            ($testId, 'Do you find it very hard to take no for an answer?'),
            ($testId, 'Do you stop and think things over before doing anything?'),
            ($testId, 'If you say you will do something do you always keep your promise, no matter how inconvenient it might be to do so?'),
            ($testId, 'Do your moods go up and down?'),
            ($testId, 'Do you generally do and say things quickly without stopping to think?'),
            ($testId, 'Do you ever feel ‘just miserable’ for no good reason?'),
            ($testId, 'Would you do almost anything for a dare?'),
            ($testId, 'Do you suddenly feel shy when you want to talk to an attractive stranger?'),
            ($testId, 'Once in a while do you lose your temper and get angry?'),
            ($testId, 'Do you often do things on the spur of the moment?'),
            ($testId, 'Do you often worry about things you should have done or said?'),
            ($testId, 'Generally do you prefer reading to meeting people?'),
            ($testId, 'Are your feelings rather easily hurt?'),
            ($testId, 'Do you like going out a lot?'),
            ($testId, 'Do you occasionally have thoughts and ideas that you would not like otherpeople to know about?'),
            ($testId, 'Are you sometimes bubbling over with energy and sometimes very sluggish?'),
            ($testId, 'Do you prefer to have few but special friends?'),
            ($testId, 'Do you daydream a lot?'),
            ($testId, 'When people shout at you do you shout back?'),
            ($testId, 'Are you often troubled about feelings of guilt?'),
            ($testId, 'Are all your habits good and desirable ones?'),
            ($testId, 'Can you usually let yourself go and enjoy yourself a lot at a lively party?'),
            ($testId, 'Would you call yourself tense or ‘highly strung’?'),
            ($testId, 'Do other people think of you as being very lively?'),
            ($testId, 'After you have done something important, do you come away feelingyou could have done better?'),
            ($testId, 'Are you mostly quiet when you are with other people?'),
            ($testId, 'Do you sometimes gossip?'),
            ($testId, 'Do ideas run through your head so that you cannot sleep?'),
            ($testId, 'If there is something you want to know about, would you rather look it upin a book than talk to someone about it?'),
            ($testId, 'Do you get palpitations or thumping in your hear?'),
            ($testId, 'Do you like the kind of work that you need to pay close attention to?'),
            ($testId, 'Do you get attacks of shaking or trembling?'),
            ($testId, 'Would you always declare everything at customs, even if you knewyou could never be found out?'),
            ($testId, 'Do you hate being with a crowd who play jokes on one another?'),
            ($testId, 'Are you an irritable person?'),
            ($testId, 'Do you like doing things in which you have to act quickly?'),
            ($testId, 'Do you worry about awful things that might happen?'),
            ($testId, 'Are you slow and unhurried in the way you move?'),
            ($testId, 'Have you ever been late for an appointment or work?'),
            ($testId, 'Do you have many nightmares?'),
            ($testId, 'Do you like talking to people so much that you never miss a chance of talking toa stranger?'),
            ($testId, 'Are you troubled by aches and pains?'),
            ($testId, 'Would you be very unhappy if you could not see lots of people most of the time?'),
            ($testId, 'Would you call yourself a nervous person?'),
            ($testId, 'Of all the people you know, are there some whom you definitely do not like?'),
            ($testId, 'Would you say that you were fairly self-confident?'),
            ($testId, 'Are you easily hurt when people find fault with you or your work?'),
            ($testId, 'Do you find it hard to really enjoy yourself at a lively party?'),
            ($testId, 'Are you troubled by feelings of inferiority?'),
            ($testId, 'Can you easily get some life into a dull party?'),
            ($testId, 'Do you sometimes talk about things you know nothing about?'),
            ($testId, 'Do you worry about your health?'),
            ($testId, 'Do you like playing pranks on others?'),
            ($testId, 'Do you suffer from sleeplessness?');

            INSERT INTO $scores (answer_id, question_id, scale_id, score_value) VALUES
            ($yesId, 1, $eiId,  1),
            ($yesId, 2, $nsId,  1),
            ($yesId, 3, $eiId,  1),
            ($yesId, 4, $nsId,  1),
            ($noId,  5, $eiId,  1),
            ($yesId, 6, $lieId, 1),
            ($yesId, 7, $nsId,  1),
            ($yesId, 8, $eiId,  1),
            ($yesId, 9, $nsId,  1),
            ($yesId, 10, $eiId,  1),
            ($yesId, 11, $nsId,  1),
            ($noId,  12, $lieId, 1),
            ($yesId, 13, $eiId,  1),
            ($yesId, 14, $nsId,  1),
            ($noId,  15, $eiId,  1),
            ($yesId, 16, $nsId,  1),
            ($yesId, 17, $eiId,  1),
            ($noId,  18, $lieId, 1),
            ($yesId, 19, $nsId,  1),
            ($noId,  20, $eiId,  1),
            ($yesId, 21, $nsId,  1),
            ($yesId, 22, $eiId,  1),
            ($yesId, 23, $nsId,  1),
            ($yesId, 24, $lieId, 1),
            ($yesId, 25, $eiId,  1),
            ($yesId, 26, $nsId,  1),
            ($yesId, 27, $eiId,  1),
            ($yesId, 28, $nsId,  1),
            ($noId,  29, $eiId,  1),
            ($noId,  30, $lieId, 1),
            ($yesId, 31, $nsId,  1),
            ($noId,  32, $eiId,  1),
            ($yesId, 33, $nsId,  1),
            ($noId,  34, $eiId,  1),
            ($yesId, 35, $nsId,  1),
            ($yesId, 36, $lieId, 1),
            ($noId,  37, $eiId,  1),
            ($yesId, 38, $nsId,  1),
            ($yesId, 39, $eiId,  1),
            ($yesId, 40, $nsId,  1),
            ($noId,  41, $eiId,  1),
            ($noId,  42, $lieId, 1),
            ($yesId, 43, $nsId,  1),
            ($yesId, 44, $eiId,  1),
            ($yesId, 45, $nsId,  1),
            ($yesId, 46, $eiId,  1),
            ($yesId, 47, $nsId,  1),
            ($noId,  48, $lieId, 1),
            ($yesId, 49, $eiId,  1),
            ($yesId, 50, $nsId,  1),
            ($noId,  51, $eiId,  1),
            ($yesId, 52, $nsId,  1),
            ($yesId, 53, $eiId,  1),
            ($noId,  54, $lieId, 1),
            ($yesId, 55, $nsId,  1),
            ($yesId, 56, $eiId,  1),
            ($yesId, 57, $nsId,  1);
        ");
    }

    /**
     * Select first field value
     *
     * @param string $sql the query to run
     *
     * @return string
     */
    protected function field($sql)
    {
        $result = $this->select_one($sql);
        if (empty($result)) {
            return null;
        }
        return reset($result);
    }

    public function down()
    {
        $posts              = WP_DB_PREFIX  . 'posts';
        $terms              = WP_DB_PREFIX  . 'terms';
        $termTaxonomy       = WP_DB_PREFIX  . 'term_taxonomy';
        $termRelashionships = WP_DB_PREFIX  . 'term_relationships';
        $questions          = WPT_DB_PREFIX . 'questions';
        $scores             = WPT_DB_PREFIX . 'scores';

        $this->execute("
            SET FOREIGN_KEY_CHECKS = 0;
            TRUNCATE TABLE $scores;
            TRUNCATE TABLE $questions;
            SET FOREIGN_KEY_CHECKS = 1;
            DELETE FROM $terms WHERE term_id IN (
                SELECT term_id FROM $termTaxonomy WHERE taxonomy LIKE 'wpt\_%'
            );
            DELETE FROM $termRelashionships WHERE term_taxonomy_id IN (
                SELECT term_taxonomy_id FROM $termTaxonomy WHERE taxonomy LIKE 'wpt\_%'
            );
            DELETE FROM $termTaxonomy WHERE taxonomy LIKE 'wpt\_%';
            DELETE FROM $posts WHERE post_parent IN (
                SELECT ID FROM (SELECT ID FROM $posts WHERE post_type = 'wpt_test') inn
            );
            DELETE FROM $posts WHERE post_type = 'wpt_test';
        ");
    }
}
