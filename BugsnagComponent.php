<?php

namespace jcasanova\bugsnag\yii1;

use Yii;
use Bugsnag;

/**
 * Bugsnag component
 *
 * @property-read Bugsnag\Client $client
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
     * Absolute path to the root of your application.
     *
     * @var string
     */
    public $projectRoot;
    /**
     * Bugsnag client instance
     *
     * @var Bugsnag\Client
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
    }

    /**
     * Get bugsnag client instance
     *
     * @return Bugsnag\Client
     */
    public function getClient()
    {
        if ($this->_client) {
            return $this->_client;
        }

        // Client
        $client = Bugsnag\Client::make($this->bugsnagApiKey);
        $client->setNotifyReleaseStages($this->notifyReleaseStages);
        $client->setReleaseStage($this->releaseStage);
        $client->setAppVersion('1.2.3');
        $client->setFilters($this->filters);
        // Set project root
        if ($this->projectRoot !== null) {
            $client->setProjectRoot($this->projectRoot);
        }
        // Set user info
        $user = $this->getUserData();
        if ($user) {
            $client->registerCallback(function($report) use ($user) {
                $report->setUser($user);
            });
        }
        // Session tracking
        $client->setAutoCaptureSessions(true);

        // Store client
        $this->_client = $client;

        return $this->_client;
    }

    /**
     * Notify Bugsnag of a non-fatal/handled throwable
     *
     * @param \Throwable $throwable the throwable to notify Bugsnag about
     * @param callable $callback  function($report)
     */
    public function notifyException($throwable, $callback = null)
    {
        $this->client->notifyException($throwable, $callback);
    }

    /**
     * Notify Bugsnag of a non-fatal/handled error
     *
     * @param string $name     the name of the error, a short (1 word) string
     * @param string $message  the error message
     * @param callable $callback  function($report)
     */
    public function notifyError($name, $message, $callback = null)
    {
        $this->client->notifyError($name, $message, $callback);
    }

    /**
     * Returns user information
     *
     * @return array
     */
    public function getUserData()
    {
        if (!Yii::app()->hasComponent('user') || Yii::app()->user->isGuest) {
            return null;
        }

        return ['id' => Yii::app()->user->id];
    }
}
