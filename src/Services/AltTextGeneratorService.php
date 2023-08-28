<?php

namespace TorqIT\ImageAltGeneratorBundle\Services;

use Pimcore\Model\Asset;
use Pimcore\Model\Asset\Image;
use Pimcore\Tool\Serialize;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AltTextGeneratorService
{
    private Image\Thumbnail\Config $config;
    function __construct(private ConfigChecker $configChecker)
    {
        $this->config = $this->createConfig();
    }

    public function generateAltText(int|Asset $asset)
    {
        if (is_int($asset)) {
            $asset = Image::getById($asset);
        }

        if (!$asset) {
            throw new NotFoundHttpException("Asset does not exist!");
        }

        $altText = $this->fetchAltTextForImage($asset);
        $asset->addMetadata("alt", "input", $altText);

        $asset->save();

        return $altText;
    }

    private function fetchAltTextForImage(Image $imageAsset)
    {
        $thumbnailPath = $imageAsset->getThumbnail($this->config, false)->getLocalFile();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->configChecker->getConfig("image_alt_generator.computervision_url") . "/vision/v3.2/analyze?visualFeatures=Description&language=en&model-version=latest");
        curl_setopt($ch, CURLOPT_POST, 1);

        $headers = [
            "Content-Type: application/octet-stream",

            //Key will be replaced (or removed) when more-core is made public
            "Ocp-Apim-Subscription-Key: " . $this->configChecker->getConfig("image_alt_generator.subscription_key")
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($thumbnailPath));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $arrResponse = json_decode($response, true);
        return $arrResponse["description"]["captions"][0]["text"];
    }

    private function createConfig()
    {
        $pipe = new Image\Thumbnail\Config();

        $pipe->setFormat("PNG");
        $pipe->setQuality("85");
        $pipe->setItems(
            array(
                array(
                    "method" => "contain",
                    "arguments" =>
                    array(
                        "width" => "1200",
                        "height" => "1200",
                        "forceResize" => "false"
                    )
                )
            )
        );
        $pipe->setHighResolution("0.0");

        // set name
        $hash = md5(Serialize::serialize($pipe));
        $pipe->setName('auto_' . $hash);

        return $pipe;
    }
}
