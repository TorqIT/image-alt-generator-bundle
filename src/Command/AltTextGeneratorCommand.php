<?php

namespace TorqIT\ImageAltGeneratorBundle\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use TorqIT\ImageAltGeneratorBundle\Services\AltTextGeneratorService;
use Pimcore\Model\Asset;

class AltTextGeneratorCommand extends AbstractCommand
{
    function __construct(private AltTextGeneratorService $altTextGeneratorService)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('torq:generate-alt-text')
            ->setDescription('Do Shopify Stuff')
            ->addOption("force", "f", InputOption::VALUE_OPTIONAL, "if force is provided, it will override existing alt text on images")
            ->addArgument("Image", InputArgument::OPTIONAL, "asset location or folder to run command. will run on all assets if not specified");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$image = $input->getArgument("Image")) {
            $helper = $this->getHelper('question');
            $question = new ConfirmationQuestion('No asset provided are you sure you want to run this on all assets?', false);

            if (!$helper->ask($input, $output, $question)) {
                return self::SUCCESS;
            }
        }
        $listing = new Asset\Listing();
        $listing->setCondition("type = ?", ['image']);
        foreach ($listing as $image) {
            $this->altTextGeneratorService->generateAltText($image);
        }
        return self::SUCCESS;
    }
}
