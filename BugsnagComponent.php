<?php

namespace demi\bugsnag\yii1;

use Bugsnag_Client;

/**
 * Bugsnag component
 *
 * @property-read Bugsnag_Client $client
 */
class BugsnagComponent extends \CApplicationComponent
{
    /**
     * You bugsnag api key
     *
     * @var string
     */
    public $bugsnagApiKey;
    /**
     * Set which release stages should be allowed to notify Bugsnag
     * Eg. array("production", "development")
     *
     * @var string
     */
    public $releaseStage;
    /**
     * All possible release stages
     *
     * @var array
     */
    public $notifyReleaseStages = ['production', 'development'];
    /**
     * Set the strings to filter out from metaData arrays before sending then to Bugsnag.
     * Eg. array("password", "credit_card")
     *
     * @var array
     */
    public $filters = ['password'];

    /**
     * Bugsnag client instance
     *
     * @var Bugsnag_Client
     */
    protected $_client;

    /**
     * Initialize bugsnag client
     *
     * @throws \Exception
     */
    public function init()
    {
        parent::init();

        if (empty($this->bugsnagApiKey)) {
            throw new \Exception('You must set bugsnag API key');
        }

        // Config
        if ($this->releaseStage === null) {
            $this->releaseStage = defined('YII_DEBUG') && YII_DEBUG ? 'development' : 'production';
        }

        // Client
        $client = new Bugsnag_Client($this->bugsnagApiKey);
        $client->setNotifyReleaseStages($this->notifyReleaseStages);
        $client->setReleaseStage($this->releaseStage);
        $client->setFilters($this->filters);
        // Store client
        $this->_client = $client;

        // Register
        $this->register();
    }

    /**
     * Attach bugsnag error/exception handlers
     */
    public function register()
    {
        $bugsnag = $this->client;

        set_error_handler(array($bugsnag, "errorHandler"));
        set_exception_handler(array($bugsnag, "exceptionHandler"));
    }

    /**
     * Get bugsnag client instance
     *
     * @return Bugsnag_Client
     */
    public function getClient()
    {
        return $this->_client;
    }
}