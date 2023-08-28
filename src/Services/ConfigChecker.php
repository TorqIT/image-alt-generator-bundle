<?php

namespace TorqIT\ImageAltGeneratorBundle\Services;

use Pimcore\Model\Element\ValidationException;

class ConfigChecker
{
    const REQUIRED_CONFIGS = [
        'image_alt_generator.computervision_url',
        'image_alt_generator.subscription_key',
    ];
    public function __construct()
    {
    }

    public function checkConfig()
    {
        foreach (self::REQUIRED_CONFIGS as $config) {
            if ('placeholder_value' === \Pimcore::getKernel()->getContainer()->getParameter($config)) {
                throw new ValidationException(sprintf('Required parameter "%s" is not set. Overwrite this parameter in your yml config.', $config));
            }
        }
    }

    public function getConfig($configName)
    {
        if (!in_array($configName, self::REQUIRED_CONFIGS)) {
            throw new ValidationException(sprintf('Requested parameter "%s" is not in the list of this bundles configurations.', $configName));
        }
        if ('placeholder_value' === $value = \Pimcore::getKernel()->getContainer()->getParameter($configName)) {
            throw new ValidationException(sprintf('Required parameter "%s" is not set. Overwrite this parameter in your yml config.', $configName));
        }
        return $value;
    }
}
