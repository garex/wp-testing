<?php
/**
 * @method integer getId() getId() Gets the current value of id
 * @method WpTesting_Model_Test setId() setId(integer $id) Sets the value for id
 * @method string getTitle() getTitle() Gets the current value of title
 * @method WpTesting_Model_Test setTitle() setTitle(string $title) Sets the value for title
 * @method fTimestamp getCreated() getCreated() Gets the current value of created
 * @method WpTesting_Model_Test setCreated() setCreated(fTimestamp|string $created) Sets the value for created
 * @method fTimestamp getModified() getModified() Gets the current value of modified
 * @method WpTesting_Model_Test setModified() setModified(fTimestamp|string $modified) Sets the value for modified
 */
class WpTesting_Model_Test extends WpTesting_Model_AbstractModel
{

    protected $columnAliases = array(
        'title'     => 'post_title',
        'created'   => 'post_date',
        'modified'  => 'post_modified',
    );

    public function __construct($key = null)
    {
        if ($key instanceof WP_Post) {
            if ($key->post_type != 'wpt_test') {
                return;
            }
            $postAsArray = (array)$key;
            unset($postAsArray['filter']);
            return parent::__construct(new ArrayIterator(array($postAsArray)));
        }
        return parent::__construct($key);
    }

}
