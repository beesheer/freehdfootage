<?php

/**
 * Application bootsrap class. This is the place to initialize all the resources.
 *
 * For example, the db, session, etc.
 *
 * @author Bin Xu
 * @version 1.0
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    /**
     * Set up the customized routes.
     *
     * @return void
     */
    protected function _initRoute() {
        Zend_Loader::loadClass('Helper_Custom404');
        $frontController = Zend_Controller_Front::getInstance();
        $frontController->registerPlugin(new Helper_Custom404());
    }

    /**
     * Set up the config.
     *
     * @return void
     */
    protected function _initConfig() {
        $config = new Zend_Config_Ini(CONFIG_PATH . 'app.ini', APPLICATION_ENV);
        Zend_Registry::getInstance()->config = $config;
    }

    /**
     * Set up the caches. Currently, just the db meta cache.
     *
     * @return void
     */
    protected function _initCache() {
        // Db metadata cache
        $frontendOptions = array(
            'automatic_serialization' => true
        );
        $backendOptions = array(
            'cache_dir' => DATA_PATH . 'cache' . DS . 'dbMeta'
        );
        $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
        Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
    }

    /**
     * Acl plugin.
     *
     * @return void
     */
    protected function _initAcl() {
        $frontController = Zend_Controller_Front::getInstance();
        $acl = new Acl_Client();
        $frontController->registerPlugin(new Controller_Front_Acl($acl));
    }

    /**
     * Set up layout according to different areas.
     *
     * @return void
     */
    protected function _initLayout() {
        $layout = 'client-default';
        if ($layout) {
            Zend_Layout::startMvc(
                    array(
                        'layoutPath' => APPLICATION_PATH . 'layouts',
                        'layout' => $layout
                    )
            );
        }
    }

    /**
     * Initialize the logger at the location provided
     * in the configuration (app.ini)
     *
     * @return void
     */
    protected function _initLog() {
        $config = Zend_Registry::getInstance()->config;
        $logger = Zend_Log::factory($config->resources->log);
        Zend_Registry::getInstance()->set('logger', $logger);
    }

    /**
     * Initialize locale and translate. Just to be aware the default locale
     * changes depending on what is set in the users cookie. This means that
     * 'translate' will be en_US for english request and fr_CA for a french one.
     *
     * @return void
     */
    protected function _initLocaleAndTranslate() {
        $defaultLocale = 'en_US';
        $locale = $defaultLocale;
        $supportedLocale = array(
            'en_US',
            'fr_CA'
        );

        // Try to get from cookie
        if (isset($_COOKIE['locale'])) {
            $locale = $_COOKIE['locale'];
        }

        // Get from direct request, and this overwrites the facebook data or cookie.
        if (isset($_GET['locale'])) {
            $locale = $_GET['locale'];
            // Set in cookie
            setcookie("locale", $locale, time() + 3600 * 24 * 30, '/');
        }

        // We only support en_US or fr_CA
        if (array_search($locale, $supportedLocale) === false) {
            $locale = $defaultLocale;
        }

        Zend_Registry::getInstance()->locale = $locale;

        // Locale csv file
        $csvFile = APPLICATION_PATH . 'languages' . DS . $locale . '.csv';

        // Initialize translate
        $translate = new Zend_Translate(
                array(
            'adapter' => 'csv',
            'content' => $csvFile,
            'locale' => $locale,
            'separator' => ';'
                )
        );
        Zend_Registry::getInstance()->translate = $translate;

        // The other locale language
        $otherLocale = $locale == 'fr_CA' ? 'en_US' : 'fr_CA';
        $otherCsvFile = APPLICATION_PATH . 'languages' . DS . $otherLocale . '.csv';
        $otherTranslate = new Zend_Translate(
                array(
            'adapter' => 'csv',
            'content' => $otherCsvFile,
            'locale' => $otherLocale,
            'separator' => ';'
                )
        );
        Zend_Registry::getInstance()->otherTranslate = $otherTranslate;
    }

    /**
     * Add some special route.
     *
     * @return void
     */
    protected function _initSpeicialRoute()
    {
        $this->bootstrap('FrontController');
        $front = $this->getResource('FrontController');
        $router = $front->getRouter();

        // Public media asset link
        $route = new Zend_Controller_Router_Route(
            'url/:type/:key',
            array(
                'module' => 'default',
                'controller' => 'url',
                'action'     => 'index'
            )
        );
        $router->addRoute('url', $route);

        // Public media preview link
        $route = new Zend_Controller_Router_Route(
            'preview/:type/:key',
            array(
                'module' => 'default',
                'controller' => 'url',
                'action'     => 'preview'
            )
        );
        $router->addRoute('preview', $route);
    }
}
