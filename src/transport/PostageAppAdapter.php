<?php
namespace convergine\postageapp\transport;

use Craft;
use craft\behaviors\EnvAttributeParserBehavior;
use craft\helpers\App;
use craft\mail\transportadapters\BaseTransportAdapter;
use Symfony\Component\Mailer\Transport\AbstractTransport;

class PostageAppAdapter extends BaseTransportAdapter {
    public static function displayName(): string {
        return 'PostageApp';
    }

    public ?string $api_key = null;

    public function attributeLabels() {
        return [
            'api_key' => Craft::t('convergine-postageapp', 'API Key')
        ];
    }

    public function behaviors(): array {
        $behaviors = parent::behaviors();
        $behaviors['parser'] = [
            'class' => EnvAttributeParserBehavior::class,
            'attributes' => [
                'api_key'
            ],
        ];
        return $behaviors;
    }

    protected function defineRules(): array {
        return [
            [['api_key'], 'required']
        ];
    }

    public function getSettingsHtml(): ?string {
        return Craft::$app->getView()->renderTemplate('convergine-postageapp/settings', [
            'adapter' => $this
        ]);
    }
    
    public function defineTransport(): array|AbstractTransport {
        return new PostageAppTransport(App::parseEnv($this->api_key));
    }
}
