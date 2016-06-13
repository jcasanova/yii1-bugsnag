<?php

namespace demi\bugsnag\yii1;

use Yii;
use CErrorHandler;

/**
 * Bugsnag error handler class for Yii1
 * Send exceptions to bugsnag
 */
class BugsnagErrorHandler extends CErrorHandler
{
    /**
     * Name of bugsnag component: Yii::app()->bugsnag
     *
     * @var string
     */
    public $bugsnagComponentName = 'bugsnag';

    /**
     * @inheritdoc
     */
    protected function handleException($exception)
    {
        /** @var \demi\bugsnag\yii1\BugsnagComponent $bugsnag */
        $bugsnag = Yii::app()->getComponent($this->bugsnagComponentName);

        // Notify bugsnag exception
        $bugsnag->notifyException($exception);

        parent::handleException($exception);
    }
}