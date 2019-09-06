<?php
/**
 * Copyright ©2019 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Attributes\Console;

use Emartech\Attributes\Config\Attributes as AttributesConfig;
use Emartech\Attributes\Config\AttributesFactory as AttributesConfigFactory;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AttributeCreate
 * @package Emartech\Attributes\Console
 */
class AttributeCreate extends Command
{
    const TYPE = 'type';

    /**
     * @var EavConfig
     */
    private $eavConfig;

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
     * @param EavConfig               $eavConfig
     * @param EavSetupFactory         $eavSetupFactory
     * @param AttributesConfigFactory $attributesConfigFactory
     * @param string|null             $name
     */
    public function __construct(
        EavConfig $eavConfig,
        EavSetupFactory $eavSetupFactory,
        AttributesConfigFactory $attributesConfigFactory,
        string $name = null
    ) {
        $this->eavConfig = $eavConfig;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributesConfigFactory = $attributesConfigFactory;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setName('emartech:attribute:create')
            ->setDescription('Create test attributes - optional use --type customer|product|category|customer_address')
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
                $this->createCustomerAttributes($output, $eavSetup, $attributesConfig);
                break;
            case 'product':
                $this->createProductAttributes($output, $eavSetup, $attributesConfig);
                break;
            case 'category':
                $this->createCategoryAttributes($output, $eavSetup, $attributesConfig);
                break;
            case 'customer_address':
                $this->createCustomerAddressAttributes($output, $eavSetup, $attributesConfig);
                break;
            case '':
                $this->createCustomerAttributes($output, $eavSetup, $attributesConfig);
                $this->createCustomerAddressAttributes($output, $eavSetup, $attributesConfig);
                $this->createProductAttributes($output, $eavSetup, $attributesConfig);
                $this->createCategoryAttributes($output, $eavSetup, $attributesConfig);
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
    private function createCategoryAttributes($output, $eavSetup, $attributesConfig)
    {
        foreach ($attributesConfig->getCategoryAttributes() as $attributeCode => $attributeLabel) {
            $output->writeln("Attribute Create: " . $attributeCode);

            $this->createAttribute(
                $eavSetup,
                $attributesConfig->getCategoryEntityTypeId(),
                [],
                $attributeCode,
                $attributeLabel,
                $attributesConfig
            );
        }

        $output->writeln("Category Attributes Created");
    }

    /**
     * @param OutputInterface  $output
     * @param EavSetup         $eavSetup
     * @param AttributesConfig $attributesConfig
     *
     * @throws LocalizedException
     * @throws \Exception
     */
    private function createCustomerAttributes($output, $eavSetup, $attributesConfig)
    {
        foreach ($attributesConfig->getCustomerAttributes() as $attributeCode => $attributeLabel) {
            $output->writeln("Attribute Create: " . $attributeCode);

            $this->createAttribute(
                $eavSetup,
                $attributesConfig->getCustomerEntityTypeId(),
                $attributesConfig->getCustomerUsedInForms(),
                $attributeCode,
                $attributeLabel,
                $attributesConfig
            );
        }

        $output->writeln("Customer Attributes Created");
    }

    /**
     * @param OutputInterface  $output
     * @param EavSetup         $eavSetup
     * @param AttributesConfig $attributesConfig
     *
     * @throws LocalizedException
     * @throws \Exception
     */
    private function createCustomerAddressAttributes($output, $eavSetup, $attributesConfig)
    {
        foreach ($attributesConfig->getCustomerAddressAttributes() as $attributeCode => $attributeLabel) {
            $output->writeln("Attribute Create: " . $attributeCode);

            $this->createAttribute(
                $eavSetup,
                $attributesConfig->getCustomerAddressEntityTypeId(),
                $attributesConfig->getCustomerAddressUsedInForms(),
                $attributeCode,
                $attributeLabel,
                $attributesConfig
            );
        }

        $output->writeln("Customer Address Attributes Created");
    }

    /**
     * @param OutputInterface  $output
     * @param EavSetup         $eavSetup
     * @param AttributesConfig $attributesConfig
     *
     * @throws LocalizedException
     * @throws \Exception
     */
    private function createProductAttributes($output, $eavSetup, $attributesConfig)
    {
        foreach ($attributesConfig->getProductAttributes() as $attributeCode => $attributeLabel) {
            $output->writeln("Attribute Create: " . $attributeCode);

            $this->createAttribute(
                $eavSetup,
                $attributesConfig->getProductEntityTypeId(),
                [],
                $attributeCode,
                $attributeLabel,
                $attributesConfig
            );
        }
        $output->writeln("Product Attributes Created");
    }

    /**
     * @param EavSetup         $eavSetup
     * @param string           $entityTypeId
     * @param string[]         $usedInForms
     * @param string           $attributeCode
     * @param string           $attributeLabel
     * @param AttributesConfig $attributesConfig
     *
     * @throws LocalizedException
     * @throws \Exception
     */
    private function createAttribute(
        $eavSetup,
        $entityTypeId,
        $usedInForms,
        $attributeCode,
        $attributeLabel,
        $attributesConfig
    ) {
        $eavSetup->removeAttribute($entityTypeId, $attributeCode);

        $data = [
            'type'       => 'varchar',
            'backend'    => '',
            'frontend'   => '',
            'class'      => '',
            'source'     => '',
            'label'      => $attributeLabel,
            'input'      => 'text',
            'required'   => false,
            'default'    => '',
            'system'     => false,
            'sort_order' => 600,
            'position'   => 600,
        ];

        if ($entityTypeId === $attributesConfig->getProductEntityTypeId()) {
            $data = array_merge(
                $data,
                [
                    'apply_to'     => 'simple',
                    'visible'      => true,
                    'group'        => 'General',
                    'user_defined' => true,
                ]
            );
        } elseif ($entityTypeId === $attributesConfig->getCategoryEntityTypeId()) {
            $data = array_merge(
                $data,
                [
                    'visible' => true,
                    'group'   => 'Display Settings',
                ]
            );
        }

        $eavSetup->addAttribute(
            $entityTypeId,
            $attributeCode,
            $data
        );

        if ($usedInForms) {
            $attribute = $this->eavConfig->getAttribute($entityTypeId, $attributeCode);
            $attribute->setData('used_in_forms', $usedInForms);
            $attribute->save();
        }
    }
}