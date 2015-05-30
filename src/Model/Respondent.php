<?php
/**
 * @method integer getId() getId() Gets the current value of id
 * @method string getLogin() getLogin() Gets the current value of login
 * @method string getEmail() getEmail() Gets the current value of email
 * @method string getNiceName() getNiceName() Gets the current value of nice name
 * @method string getDisplayName() getDisplayName() Gets the current value of display name
 */
class WpTesting_Model_Respondent extends WpTesting_Model_AbstractModel
{

    protected $columnAliases = array(
        'id'        => 'ID',
        'i_d'       => 'ID',
        'login'     => 'user_login',
        'nice_name' => 'user_nicename',
        'email'     => 'user_email',
    );
}
