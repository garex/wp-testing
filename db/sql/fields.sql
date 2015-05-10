DROP TEMPORARY TABLE IF EXISTS phpdoc;
CREATE TEMPORARY TABLE IF NOT EXISTS phpdoc ENGINE=Memory
AS (
  SELECT 
	REPLACE(column_name, 'field_', '') AS source_name,
	REPLACE(column_name, 'field_', '') AS name,
	REPLACE(column_name, 'field_', '') AS human_name,
	data_type AS type
FROM
    information_schema.columns
WHERE
    table_schema = 'wp_testing_4_2'
        AND table_name = 'wp_t_fields'
);
update phpdoc 
set 
    type = 'integer'
where
    type in ('bigint' , 'int');
update phpdoc 
set 
    type = 'boolean'
where
    type in ('tinyint');
update phpdoc 
set 
    type = 'string'
where
    type in ('text' , 'varchar');
update phpdoc 
set 
    name = replace(name, '_i', 'I');
update phpdoc 
set 
    name = replace(name, '_r', 'R');
update phpdoc 
set 
    name = replace(name, '_v', 'V');
update phpdoc 
set 
    name = CONCAT(UCASE(LEFT(name, 1)), SUBSTRING(name, 2));
update phpdoc 
set 
    human_name = replace(human_name, '_', ' ');

-- * @method integer getId() getId() Gets the current value of id
-- * @method WpTesting_Model_Test setId() setId(integer $id) Sets the value for id

select 
    CONCAT(
		' * @method WpTestingFields_Model_Field set',
		name,
		'() set',
		name,
		'(',type,' $',source_name,') Sets the value for ',
		human_name
	) AS phpdoc
from
    phpdoc;

select 
    CONCAT(
		' * @method ',
		type,
		' get',
		name,
		'() get',
		name,
		'() Gets the current value of ',
		human_name
	) AS phpdoc
from
    phpdoc;


SELECT 
    concat('\'',
            REPLACE(column_name, 'field_', ''),
            '\' => \'',
            column_name,
            '\',') as columnAliases
FROM
    information_schema.columns
WHERE
    table_schema = 'wp_testing_4_2'
        AND table_name = 'wp_t_fields'
        AND column_name != 'test_id'
;