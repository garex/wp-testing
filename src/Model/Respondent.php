<?php
/**
 * @method integer getId() Gets the current value of id
 * @method string getLogin() Gets the current value of login
 * @method string getEmail() Gets the current value of email
 * @method string getNiceName() Gets the current value of nice name
 * @method string getDisplayName() Gets the current value of display name
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
