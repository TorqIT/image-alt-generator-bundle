<?php

namespace TorqIT\ImageAltGeneratorBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

class ImageAltGeneratorBundle extends AbstractPimcoreBundle
{
    public function getJsPaths()
    {
        return [
            '/bundles/imagealtgenerator/js/pimcore/startup.js'
        ];
    }
}