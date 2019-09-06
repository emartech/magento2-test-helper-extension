<?php
/**
 * Copyright ©2019 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Attributes\Console;

use Emartech\Attributes\Config\Attributes as AttributesConfig;
use Emartech\Attributes\Config\AttributesFactory as AttributesConfigFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AttributeRemove
 * @package Emartech\Attributes\Console
 */
class AttributeRemove extends Command
{
    const TYPE = 'type';

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var AttributesConfigFactory
     */
    private $attributesConfigFactory;

    /**
     * AttributeCreate constructor.
     *
     * @param EavSetupFactory         $eavSetupFactory
     * @param AttributesConfigFactory $attributesConfigFactory
     * @param string|null             $name
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        AttributesConfigFactory $attributesConfigFactory,
        string $name = null
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributesConfigFactory = $attributesConfigFactory;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setName('emartech:attribute:remove')
            ->setDescription('Remove test attributes - optional use --type customer|product|category|customer_address')
            ->setDefinition([
                new InputOption(
                    self::TYPE,
                    null,
                    InputOption::VALUE_OPTIONAL,
                    'Type'
                ),
            ]);

        parent::configure();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     * @throws LocalizedException
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Start");

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create();
        /** @var AttributesConfig $attributesConfig */
        $attributesConfig = $this->attributesConfigFactory->create();

        switch ($input->getOption(self::TYPE)) {
            case 'customer':
                $this->removeCustomerAttributes($output, $eavSetup, $attributesConfig);
                break;
            case 'product':
                $this->removeProductAttributes($output, $eavSetup, $attributesConfig);
                break;
            case 'category':
                $this->removeCategoryAttributes($output, $eavSetup, $attributesConfig);
                break;
            case 'customer_address':
                $this->removeCustomerAddressAttributes($output, $eavSetup, $attributesConfig);
                break;
            case '':
                $this->removeCustomerAttributes($output, $eavSetup, $attributesConfig);
                $this->removeCustomerAddressAttributes($output, $eavSetup, $attributesConfig);
                $this->removeProductAttributes($output, $eavSetup, $attributesConfig);
                $this->removeCategoryAttributes($output, $eavSetup, $attributesConfig);
                break;
        }

        $output->writeln("Finished");
    }

    /**
     * @param OutputInterface  $output
     * @param EavSetup         $eavSetup
     * @param AttributesConfig $attributesConfig
     *
     * @throws LocalizedException
     */
    private function removeCategoryAttributes($output, $eavSetup, $attributesConfig)
    {
        foreach ($attributesConfig->getCategoryAttributes() as $attributeCode => $attributeLabel) {
            $output->writeln("Attribute Remove: " . $attributeCode);
            $eavSetup->removeAttribute($attributesConfig->getCategoryEntityTypeId(), $attributeCode);
        }

        $output->writeln("Category Attributes Removed");
    }

    /**
     * @param OutputInterface  $output
     * @param EavSetup         $eavSetup
     * @param AttributesConfig $attributesConfig
     *
     * @throws LocalizedException
     * @throws \Exception
     */
    private function removeCustomerAttributes($output, $eavSetup, $attributesConfig)
    {
        foreach ($attributesConfig->getCustomerAttributes() as $attributeCode => $attributeLabel) {
            $output->writeln("Attribute Remove: " . $attributeCode);
            $eavSetup->removeAttribute($attributesConfig->getCustomerEntityTypeId(), $attributeCode);
        }

        $output->writeln("Customer Attributes Removed");
    }

    /**
     * @param OutputInterface  $output
     * @param EavSetup         $eavSetup
     * @param AttributesConfig $attributesConfig
     *
     * @throws LocalizedException
     * @throws \Exception
     */
    private function removeCustomerAddressAttributes($output, $eavSetup, $attributesConfig)
    {
        foreach ($attributesConfig->getCustomerAddressAttributes() as $attributeCode => $attributeLabel) {
            $output->writeln("Attribute Remove: " . $attributeCode);
            $eavSetup->removeAttribute($attributesConfig->getCustomerAddressEntityTypeId(), $attributeCode);
        }

        $output->writeln("Customer Address Attributes Removed");
    }

    /**
     * @param OutputInterface  $output
     * @param EavSetup         $eavSetup
     * @param AttributesConfig $attributesConfig
     *
     * @throws LocalizedException
     * @throws \Exception
     */
    private function removeProductAttributes($output, $eavSetup, $attributesConfig)
    {
        foreach ($attributesConfig->getProductAttributes() as $attributeCode => $attributeLabel) {
            $output->writeln("Attribute Remove: " . $attributeCode);
            $eavSetup->removeAttribute($attributesConfig->getProductEntityTypeId(), $attributeCode);
        }
        $output->writeln("Product Attributes Removed");
    }
}