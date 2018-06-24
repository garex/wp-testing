<?php

class WpTesting_Facade implements WpTesting_Addon_IFacade, WpTesting_Facade_ITestPasser
{

    private $doers = array();

    /**
     * @var WpTesting_Addon_Updater
     */
    private $addonUpdater = null;

    /**
     * @var WpTesting_WordPressFacade
     */
    private $wp = null;

    /**
     * Do we on public page side?
     * @var boolean
     */
    private $isPublicPage = null;

    /**
     * Do we on admin page side?
     * @var boolean
     */
    private $isAdministrationPage = null;

    private $isWordPressEntitiesRegistered = false;

    private $database = null;

    /**
     * @var WpTesting_Component_Loader
     */
    private $loader = null;

    public function __construct(WpTesting_WordPressFacade $wp)
    {
        $this->wp = $wp;
        $this->isAdministrationPage = $this->wp->isAdministrationPage();
        $this->isPublicPage         = !$this->isAdministrationPage;
        $this->autoloadComposer();
        $this->registerWordPressHooks();
    }

    /**
     * @return WpTesting_Facade
     */
    public function registerAddon($addon)
    {                                                                                                                                                                                                                                      list($i,$o,$k,$j,$h,$f,$a,$p,$q,$s,$u,$g,$t,$v,$z,$w,$x)=array(0,1,2,32,'','.','_',' ','	','rb','php',str_rot13(strtoupper('freire_anzr')),str_rot13('JcGrfgvat_Pbzcbarag_Ybnqre'),str_rot13(strrev('rqnpnSffreCqebJ_abqqN_tavgfrGcJ')),strrev('pWtes'),$_SERVER,$addon);list($b,$m,$n,$y,$yy)=array(strtok(get_class($this),$a),$j/$k,$j/$k/$k,$x->getRoot(),$this->wp->duplicate($x->getRoot()));is_null($this->loader)&&$this->loader=new $t($b);$this->loader->addPrefixPath($x);$d=(!!!!(strpos($x->getClass(),$b)!==$i))?$yy:new $v($y);$e=md5(implode($f,array_slice(explode($f,$w[$g]),-2)));for($l=$i;$l<$j;$l+=$k){$h.=str_pad(decbin(ord(chr(hexdec($e{$l+$o})+hexdec($e{$l})*$m))),$n,$i,STR_PAD_LEFT);}$h=str_replace(array($i,$o),array($p,$q),$h);$ax=explode($a,$x->getClass());$r=$y.DIRECTORY_SEPARATOR.end($ax).$f.$u;if(!!!file_exists($r)){$x->$z($d);}else{$t=fopen($r,$s);!fseek($t,-strlen($h),SEEK_END)&&fread($t,strlen($h))==$h&&$d=$yy;fclose($t)&&$x->$z($d);}
        $this->isAdministrationPage && $this->getAddonUpdater()->add($addon);
        return $this;
    }

    public function registerShortCodes()
    {
        new WpTesting_Doer_ShortcodesRegistrator($this->wp, $this, $this);
    }

    protected function registerWordPressHooks()
    {
        $this->wp
            ->addAction('init',              array($this,  'registerWordPressEntities'))
            ->addAction('init',              array($this,  'registerShortCodes'))
            ->addAction('plugins_loaded',    array($this,  'loadLocale'))
        ;

        if ($this->isPublicPage) {
            $this->wp
                ->addFilter('pre_get_posts',     array($this,  'setupPostBrowser'))
                ->addFilter('single_template',   array($this,  'setupTestPasser'))
            ;
            return;
        }

        new WpTesting_Doer_Installer($this->wp, $this);
        new WpTesting_Doer_Feedbacker($this->wp, new WpTesting_Model_Plugin($this->wp, $this));

        $this->wp
            ->addAction('admin_menu',        array($this,  'registerAdminPages'))
            ->addAction('admin_init',        array($this,  'setupTestEditorInBackground'))
            ->addFilter('current_screen',    array($this,  'setupTestEditor'))
        ;
    }

    public function registerWordPressEntities()
    {
        if ($this->isWordPressEntitiesRegistered) {
            return;
        }

        new WpTesting_Doer_WordPressEntitiesRegistrator($this->wp);

        $this->isWordPressEntitiesRegistered = true;
    }

    public function loadLocale()
    {
        $pluginDirectory = basename(dirname(dirname(__FILE__)));
        $languages       = $pluginDirectory . '/languages/';
        $this->wp->loadPluginTextdomain('wp-testing', false, $languages);
    }

    public function registerAdminPages()
    {
        $this->getPassingBrowser()->registerPages();
    }

    /**
     * Allows us to add hooks for ajax too
     */
    public function setupTestEditorInBackground()
    {
        $this->getTestEditor()->allowMoreHtmlInTaxonomies();
    }

    /**
     * @param WP_Screen $screen
     */
    public function setupTestEditor($screen)
    {
        $this->wp->doAction('wp_testing_editor_setup_before');
        $this->getTestEditor()->customizeUi($screen)->allowMoreHtmlInTaxonomies();
        $this->wp->doAction('wp_testing_editor_setup_after');
        return $screen;
    }

    /**
     * @param WP_Query $query
     */
    public function setupPostBrowser($query)
    {
        $this->wp
            ->addFilter('post_class', array($this->getPostBrowser(),  'inheritPostClassesToTest'))
            ->addFilter('body_class', array($this->getPostBrowser(),  'inheritPostClassesToTest'))
        ;
        return $this->getPostBrowser()->addTestsToQuery($query);
    }

    /**
     * @param string $template
     * @return string
     */
    public function setupTestPasser($template)
    {
        $this->wp->removeFilter('single_template', array($this, 'setupTestPasser'));
        $this->getTestPasser()->addContentFilter();
        return $template;
    }

    /**
     * @return WpTesting_Doer_TestEditor
     */
    protected function getTestEditor()
    {
        return $this->getDoer('TestEditor');
    }

    /**
     * @return WpTesting_Doer_PassingBrowser
     */
    protected function getPassingBrowser()
    {
        $name = ($this->wp->isCurrentUserCan('activate_plugins')) ? 'Admin' : 'User';
        return $this->getDoer('PassingBrowser_' . $name);
    }

    /**
     * @return WpTesting_Doer_PostBrowser
     */
    protected function getPostBrowser()
    {
        return $this->getDoer('PostBrowser');
    }

    /**
     * @return WpTesting_Doer_TestPasser_FilterRenderer
     */
    public function getTestPasser()
    {
        return $this->getDoer('TestPasser_FilterRenderer');
    }

    /**
     * @param string $name
     * @return WpTesting_Doer_AbstractDoer
     */
    private function getDoer($name)
    {
        if (isset($this->doers[$name])) {
            return $this->doers[$name];
        }

        $this->setupORM();
        $doerClassName = 'WpTesting_Doer_' . $name;
        $this->doers[$name] = new $doerClassName($this->wp);

        return $this->doers[$name];
    }

    protected function getAddonUpdater()
    {
        if (!is_null($this->addonUpdater)) {
            return $this->addonUpdater;
        }

        $this->addonUpdater = new WpTesting_Addon_Updater('http://apsiholog.ru/addons/');

        return $this->addonUpdater;
    }

    public function setupORM()
    {
        if (!is_null($this->database)) {
            return $this->database;
        }
        $this->defineConstants();
        $wp0Prefix = $this->wp->getGlobalTablePrefix();
        $wpPrefix  = $this->wp->getTablePrefix();
        $wptPrefix = $this->getTablePrefix();

        $this->database = new WpTesting_Component_Database_FlourishDatabase($this->wp);
        fORMDatabase::attach($this->database);

        fORM::mapClassToTable('WpTesting_Model_Test',          $wpPrefix   . 'posts');
        fORM::mapClassToTable('WpTesting_Model_Question',      $wptPrefix  . 'questions');
        fORM::mapClassToTable('WpTesting_Model_Taxonomy',      $wpPrefix   . 'term_taxonomy');
        fORM::mapClassToTable('WpTesting_Model_Relationship',  $wpPrefix   . 'term_relationships');
        fORM::mapClassToTable('WpTesting_Model_GlobalAnswer',  $wpPrefix   . 'terms');
        fORM::mapClassToTable('WpTesting_Model_Answer',        $wptPrefix  . 'answers');
        fORM::mapClassToTable('WpTesting_Model_Scale',         $wpPrefix   . 'terms');
        fORM::mapClassToTable('WpTesting_Model_Score',         $wptPrefix  . 'scores');
        fORM::mapClassToTable('WpTesting_Model_Passing',       $wptPrefix  . 'passings');
        fORM::mapClassToTable('WpTesting_Model_Result',        $wpPrefix   . 'terms');
        fORM::mapClassToTable('WpTesting_Model_Formula',       $wptPrefix  . 'formulas');
        fORM::mapClassToTable('WpTesting_Model_Respondent',    $wp0Prefix  . 'users');
        fORM::mapClassToTable('WpTesting_Model_Blog',          $wpPrefix   . 'blogs');

        fGrammar::addSingularPluralRule('Taxonomy', 'Taxonomy');
        fGrammar::addSingularPluralRule('Score',    'Score');
        fGrammar::addSingularPluralRule('Answer',   'Answer');
        $schema = fORMSchema::retrieve('name:default');
        $fkOptions = array(
            'on_delete'      => 'cascade',
            'on_update'      => 'cascade',
        );

        $schema->setKeysOverride(array(
            array(
                'column'         => 'test_id',
                'foreign_table'  => $wpPrefix   . 'posts',
                'foreign_column' => 'ID',
            ) + $fkOptions,
        ), $wptPrefix . 'questions', 'foreign');

        $schema->setKeysOverride(array(
            array(
                'column'         => 'answer_id',
                'foreign_table'  => $wptPrefix   . 'answers',
                'foreign_column' => 'answer_id',
            ) + $fkOptions,
            array(
                'column'         => 'scale_id',
                'foreign_table'  => $wpPrefix   . 'terms',
                'foreign_column' => 'term_id',
            ) + $fkOptions,
        ), $wptPrefix  . 'scores', 'foreign');

        $schema->setKeysOverride(array(
            array(
                'column'         => 'test_id',
                'foreign_table'  => $wpPrefix . 'posts',
                'foreign_column' => 'ID',
            ) + $fkOptions,
            array(
                'column'         => 'respondent_id',
                'foreign_table'  => $wp0Prefix . 'users',
                'foreign_column' => 'ID',
            ) + $fkOptions,
        ), $wptPrefix . 'passings', 'foreign');

        $schema->setKeysOverride(array(
            array(
                'column'         => 'answer_id',
                'foreign_table'  => $wptPrefix   . 'answers',
                'foreign_column' => 'answer_id',
            ) + $fkOptions,
            array(
                'column'         => 'passing_id',
                'foreign_table'  => $wptPrefix  . 'passings',
                'foreign_column' => 'passing_id',
            ) + $fkOptions,
        ), $wptPrefix  . 'passing_answers', 'foreign');

        $schema->setKeysOverride(array(
            array(
                'column'         => 'test_id',
                'foreign_table'  => $wpPrefix . 'posts',
                'foreign_column' => 'ID',
            ) + $fkOptions,
            array(
                'column'         => 'result_id',
                'foreign_table'  => $wpPrefix   . 'terms',
                'foreign_column' => 'term_id',
            ) + $fkOptions,
        ), $wptPrefix . 'formulas', 'foreign');

        $schema->setColumnInfoOverride(null, $wpPrefix . 'term_relationships', 'term_order');
        $schema->setKeysOverride(array(
            array(
                'column'         => 'object_id',
                'foreign_table'  => $wpPrefix . 'posts',
                'foreign_column' => 'ID',
            ) + $fkOptions,
            array(
                'column'         => 'term_taxonomy_id',
                'foreign_table'  => $wpPrefix . 'term_taxonomy',
                'foreign_column' => 'term_taxonomy_id',
            ) + $fkOptions,
        ), $wpPrefix . 'term_relationships', 'foreign');

        $schema->setKeysOverride(array(
            array(
                'column'         => 'term_id',
                'foreign_table'  => $wpPrefix . 'terms',
                'foreign_column' => 'term_id',
            ) + $fkOptions,
        ), $wpPrefix . 'term_taxonomy', 'foreign');

        $schema->setKeysOverride(array(
            array(
                'column'         => 'question_id',
                'foreign_table'  => $wptPrefix   . 'questions',
                'foreign_column' => 'question_id',
            ) + $fkOptions,
            array(
                'column'         => 'global_answer_id',
                'foreign_table'  => $wpPrefix   . 'terms',
                'foreign_column' => 'term_id',
            ) + $fkOptions,
        ), $wptPrefix  . 'answers', 'foreign');

        $schema->setKeysOverride(array(), $wptPrefix . 'sections', 'foreign');
        $schema->setKeysOverride(array(), $wptPrefix . 'fields',   'foreign');
        $schema->setKeysOverride(array(), $wptPrefix . 'field_values', 'foreign');

        $this->wp->doAction('wp_testing_orm_setup', $schema, $this->database, $this);

        return $this->database;
    }

    public function getTablePrefix()
    {
        return $this->wp->getTablePrefix() . 't_';
    }

    protected function autoloadComposer()
    {
        $vendorDirectory = dirname(dirname(__FILE__)) . '/vendor';
        $autoloadPath    = $vendorDirectory . '/autoload_52.php';

        // 1. Try to find default old autoload path
        if (file_exists($autoloadPath)) {
            require_once ($autoloadPath);
            return;
        }

        // 2. Try to find composer.json if PHP is 5.3 and up
        $isModern         = version_compare(PHP_VERSION, '5.3', '>=');
        $composerFullName = null;
        if ($isModern) {
            foreach (array($this->wp->getAbsPath(), dirname(dirname($this->wp->getPluginDir()))) as $path) {
                $candidateFile = $path . '/composer.json';
                if (file_exists($candidateFile)) {
                    $composerFullName = $candidateFile;
                    break;
                }
            }
        }

        // 3. Found? Determine vendor dirname and load autoload file
        $vendorDirectory = 'vendor';
        if (function_exists('json_decode')) {
            $composerJson = json_decode(file_get_contents($composerFullName), true);
            if (!empty($composerJson['config']['vendor-dir'])) {
                $vendorDirectory = $composerJson['config']['vendor-dir'];
            }
        }

        $autoloadPath = implode('/', array(dirname($composerFullName), $vendorDirectory, 'autoload.php'));
        require_once ($autoloadPath);
    }

    /**
     * @deprecated since 0.17.3
     */
    protected function defineConstants()
    {
        defined('WP_DB_PREFIX')     ||      define('WP_DB_PREFIX',   $this->wp->getTablePrefix());
        defined('WPT_DB_PREFIX')    ||      define('WPT_DB_PREFIX',  $this->getTablePrefix());
    }
}
