<?php
/**
 * This is the abstract class for singletons.
 *
 * It is implements singleton pattern.
 */
abstract class Singleton
{
    /**
     * Singleton protecting clone.
     *
     * @return Repo_Abstract
     */
    protected function __clone()
    {}

    /**
     * Singleton protecting construct.
     *
     * @return Manager_Abstract
     */
    protected function __construct()
    {
        $this->init();
    }

    /**
     * Init function for extending classes.
     *
     * @return void
     */
    protected function init()
    {}
}