<?php
define('DB_NAME',          'wpti');
define('DB_USER',          'wpti');
define('DB_PASSWORD',      'wpti');
define('DB_HOST',          'localhost');
define('DB_CHARSET',       'utf8');
define('DB_COLLATE',       '');
define('WPLANG',           '');
define('FS_METHOD',        'direct');
define('WP_MEMORY_LIMIT',  '64M');

define('AUTH_KEY',         '#tca;V8]yS$CD*CS|frVh_mwbh/,bMDtM6;;JI2/ko|hw;)||2Zo`GAhl$nX)-Jv');
define('SECURE_AUTH_KEY',  'g.Uvx(}@Hq%?Ov9|JP3*V1gI+8**-ng=8-6$3.1@x_`?NPDE|R/YrqX{eQeky.v2');
define('LOGGED_IN_KEY',    'N+pe~pVdb]48,GoT8M.V#(6&zQpl|J 0C0`:RLT$|bT:p[/fQ?9pI9g2*cog6JCo');
define('NONCE_KEY',        'w$V7]QmXOR`PfFfnNWNV4ZNgFMC*;:qH9Ar+x@2J~Vn9.vT>,B#Sh6rm7DY!<[|I');
define('AUTH_SALT',        'Sj5udb>uE5kux+d.C1bE9*u& 6.U3>yhMX#,|{h8!(lv+Uu2:4n19qG5&O j6fzX');
define('SECURE_AUTH_SALT', '6Y#]P*tWNe;C+jr^`XGf]/pLlqa}|daoS1QbtHBx=@v%j*6~#:XY<,ES/j*XHWk^');
define('LOGGED_IN_SALT',   'LItgu<[qx4/-GXt0WK=E*Hq|g0w&*Vj-Y>U6@<cb|~t~pEXXrahwsNC=TY-ns,Id');
define('NONCE_SALT',       '%w5[454qdnpFZNV&-)%i[/YR[0v4enFp69bI[Xr1:]qMYV|a8N51]7y-VU>[kRx$');

$table_prefix = 'wp_';

if ( !defined('ABSPATH') )
    define('ABSPATH', dirname(__FILE__) . '/');

require_once(ABSPATH . 'wp-settings.php');
