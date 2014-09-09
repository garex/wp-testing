-- SELECT * FROM wp_testing.wp_t_test;

set @db            := 'wp_testing';
set @global_prefix := 'wp_';
set @prefix        := concat(@global_prefix, 't_');

select isc.column_name, isc.data_type
from information_schema.columns as isc
join (
          select 'get' as op
    union select 'set'
    union select 'encode'
    union select 'prepare'
    union select 'inspect'
) as ops
where isc.table_schema = @db
and isc.table_name = concat(@prefix, 'test')
;
