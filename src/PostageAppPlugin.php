<?php
namespace convergine\postageapp;

use convergine\postageapp\transport\PostageAppAdapter;
use Craft;
use craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\helpers\MailerHelper;
use yii\base\Event;

class PostageAppPlugin extends Plugin {
	public static $plugin;

	public function init() {

        /* plugin initialization */
		$this->hasCpSection = false;
		$this->hasCpSettings = false;
		parent::init();

        /* register default translations */
        $this->defaultTranslations();
        self::$plugin = $this;

        $eventName = $this->getEventName();
        if($eventName) {
            Event::on(
                MailerHelper::class,
                $eventName,
                function(RegisterComponentTypesEvent $event) {
                    $event->types[] = PostageAppAdapter::class;
                }
            );
        }
    }

    private function getEventName() : ?string {
        if(defined('craft\helpers\MailerHelper::EVENT_REGISTER_MAILER_TRANSPORTS')) {
            return MailerHelper::EVENT_REGISTER_MAILER_TRANSPORTS;
        } else if(defined('craft\helpers\MailerHelper::EVENT_REGISTER_MAILER_TRANSPORT_TYPES')) {
            return MailerHelper::EVENT_REGISTER_MAILER_TRANSPORT_TYPES;
        }
        return null;
    }

    private function defaultTranslations() : void {
        /* register default translations */
        $translations = [
            'API Key' => 'API Key',
            'The PostageApp API key.' => 'The PostageApp API key.'
        ];

        Craft::$app->view->registerTranslations('convergine-postageapp', $translations);
    }
}