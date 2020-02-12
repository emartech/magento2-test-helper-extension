<?php
/**
 * Copyright ©2020 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Attributes\Console;

use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Catalog\Observer\SwitchPriceAttributeScopeOnConfigChange;
use Magento\Config\App\Config\Type\System as SystemConfig;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Event\Observer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PriceScopeChange extends Command
{
    const FLAG = 'flag';

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * PriceScopeChange constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param string|null            $name
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        string $name = null
    ) {
        $this->objectManager = $objectManager;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setName('emartech:price-scope:change')
            ->setDescription('Change Price scope --flag')
            ->setDefinition([
                new InputOption(
                    'flag',
                    null,
                    InputOption::VALUE_REQUIRED,
                    'Flag'
                ),
            ]);

        parent::configure();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Start");

        $flag = (bool)$input->getOption(self::FLAG);

        /** @var Config $configResource */
        $configResource = $this->objectManager->get(Config::class);
        $configResource->saveConfig(
            CatalogHelper::XML_PATH_PRICE_SCOPE,
            (int)$flag,
            'default',
            0
        );

        /** @var SystemConfig $config */
        $config = $this->objectManager->get(SystemConfig::class);
        $config->clean();


        $observer = $this->objectManager->get(Observer::class);
        $this->objectManager->get(SwitchPriceAttributeScopeOnConfigChange::class)
            ->execute($observer);

        $output->writeln("Finished");
    }
}