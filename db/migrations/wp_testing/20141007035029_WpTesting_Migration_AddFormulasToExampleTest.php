<?php

class WpTesting_Migration_AddFormulasToExampleTest extends WpTesting_Migration_UpdateData
{
    public function up()
    {
        $posts    = $this->globalPrefix . 'posts';
        $terms    = $this->globalPrefix . 'terms';
        $formulas = $this->pluginPrefix . 'formulas';
        $termTaxonomy       = $this->globalPrefix . 'term_taxonomy';
        $termRelashionships = $this->globalPrefix . 'term_relationships';

        $testSlug = 'eysencks-personality-inventory-epi-extroversionintroversion';
        $testId   = $this->field("SELECT ID FROM $posts WHERE post_type = 'wpt_test' AND post_name = '$testSlug' ORDER BY ID LIMIT 1");

        $this->execute("
            INSERT INTO $terms
            (name,          slug,                 term_group) VALUES
            ('Sanguine',    'result-sanguine',    0),
            ('Choleric',    'result-choleric',    0),
            ('Melancholic', 'result-melancholic', 0),
            ('Phlegmatic',  'result-phlegmatic',  0);
        ");

        $sanguineId    = $this->field("SELECT term_id FROM $terms WHERE slug = 'result-sanguine'    LIMIT 1");
        $cholericId    = $this->field("SELECT term_id FROM $terms WHERE slug = 'result-choleric'    LIMIT 1");
        $melancholicId = $this->field("SELECT term_id FROM $terms WHERE slug = 'result-melancholic' LIMIT 1");
        $phlegmaticId  = $this->field("SELECT term_id FROM $terms WHERE slug = 'result-phlegmatic'  LIMIT 1");

        $this->execute("
            INSERT INTO $termTaxonomy
            (taxonomy,     parent, count, term_id,        description) VALUES
            ('wpt_result', 0,      1,     $sanguineId,    'The sanguine temperament is traditionally associated with air. People with this temperament tend to be lively, sociable, carefree, talkative, and pleasure-seeking. They may be warm-hearted and optimistic. They can make new friends easily, be imaginative and artistic, and often have many ideas. They can be flighty and changeable; thus sanguine personalities may struggle with following tasks all the way through and be chronically late or forgetful.\n\nPedagogically, they can be best reached through awakening their love for a subject and admiration of people.'),
            ('wpt_result', 0,      1,     $cholericId,    'The choleric temperament is traditionally associated with fire. People with this temperament tend to be egocentric and extroverted. They may be excitable, impulsive, and restless, with reserves of aggression, energy, and/or passion, and try to instill that in others.\n\nThey tend to be task-oriented people and are focused on getting a job done efficiently; their motto is usually \"do it now.\" They can be ambitious, strong-willed and like to be in charge. They can show leadership, are good at planning, and are often practical and solution-oriented. They appreciate receiving respect and esteem for their work.\n\nPedagogically, they can be best reached through mutual respect and appropriate challenges that recognize their capacities.'),
            ('wpt_result', 0,      1,     $melancholicId, 'The melancholic temperament is traditionally associated with the element of earth. People with this temperament may appear serious, introverted, cautious or even suspicious. They can become preoccupied with the tragedy and cruelty in the world and are susceptible to depression and moodiness. They may be focused and conscientious. They often prefer to do things themselves, both to meet their own standards and because they are not inherently sociable.\n\nPedagogically, they can be best met by awakening their sympathy for others and the suffering of the world.'),
            ('wpt_result', 0,      1,     $phlegmaticId,  'The phlegmatic temperament is traditionally associated with water. People with this temperament may be inward and private, thoughtful, reasonable, calm, patient, caring, and tolerant. They tend to have a rich inner life, seek a quiet, peaceful atmosphere, and be content with themselves. They tend to be steadfast, consistent in their habits, and thus steady and faithful friends.\n\nPedagogically, their interest is often awakened by experiencing others\' interest in a subject.\n\nPeople of this temperament may appear somewhat ponderous or clumsy. Their speech tends to be slow or appear hesitant.');
        ");

        $this->execute("
            INSERT INTO $termRelashionships (object_id, term_taxonomy_id, term_order) VALUES
            ($testId, (SELECT term_taxonomy_id FROM $termTaxonomy WHERE term_id = $sanguineId),    1),
            ($testId, (SELECT term_taxonomy_id FROM $termTaxonomy WHERE term_id = $cholericId),    2),
            ($testId, (SELECT term_taxonomy_id FROM $termTaxonomy WHERE term_id = $melancholicId), 3),
            ($testId, (SELECT term_taxonomy_id FROM $termTaxonomy WHERE term_id = $phlegmaticId),  4);
        ");

        $this->execute("
            INSERT INTO $formulas (test_id, result_id, formula_source) VALUES
            ($testId, $sanguineId,     'scale-extraversion-introversion  > 50% AND scale-neuroticism-stability <= 50%'),
            ($testId, $cholericId,     'scale-extraversion-introversion  > 50% AND scale-neuroticism-stability  > 50%'),
            ($testId, $melancholicId,  'scale-extraversion-introversion <= 50% AND scale-neuroticism-stability  > 50%'),
            ($testId, $phlegmaticId,   'scale-extraversion-introversion <= 50% AND scale-neuroticism-stability <= 50%');
        ");
    }

    public function down()
    {
        $terms    = $this->globalPrefix . 'terms';
        $formulas = $this->pluginPrefix . 'formulas';
        $termTaxonomy       = $this->globalPrefix . 'term_taxonomy';
        $termRelashionships = $this->globalPrefix . 'term_relationships';

        $this->execute("
            SET FOREIGN_KEY_CHECKS = 0;
            TRUNCATE TABLE $formulas;
            SET FOREIGN_KEY_CHECKS = 1;
            DELETE FROM $termRelashionships WHERE term_taxonomy_id IN (
                SELECT term_taxonomy_id FROM $termTaxonomy WHERE taxonomy = 'wpt_result'
            );
            DELETE FROM $terms WHERE term_id IN (
                SELECT term_id FROM $termTaxonomy WHERE taxonomy = 'wpt_result'
            );
            DELETE FROM $termTaxonomy WHERE taxonomy = 'wpt_result';
        ");
    }
}
