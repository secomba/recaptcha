<?php

namespace FoF\ReCaptcha\Listeners;

use Flarum\Foundation\Event\Validating;
use Flarum\Settings\SettingsRepositoryInterface;
use FoF\ReCaptcha\Validators\RecaptchaValidator;
use ReCaptcha\ReCaptcha;

class AddValidatorRule
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * @param SettingsRepositoryInterface $settings
     */
    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    public function handle(Validating $event)
    {
        $secret = $this->settings->get('fof-recaptcha.credentials.secret');

        if (!empty($secret)) {
            if ($event->type instanceof RecaptchaValidator) {
                $event->validator->addExtension(
                    'recaptcha',
                    function($attribute, $value, $parameters) use ($secret) {
                        return !empty($value) && (new ReCaptcha($secret))->verify($value)->isSuccess();
                    }
                );
            }
        }
    }
}
