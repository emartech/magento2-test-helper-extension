<?php
/**
 * Copyright ©2019 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Attributes\Console;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CustomerAttributeSet
 * @package Emartech\Attributes\Console
 */
class CustomerAttributeSet extends Command
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * CustomerAttributeSet constructor.
     *
     * @param string|null                 $name
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        string $name = null,
        CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($name);
        $this->customerRepository = $customerRepository;
    }

    protected function configure()
    {
        $this
            ->setName('emartech:customer-attribute:set')
            ->setDescription('Set a given attribute on a customer use --customer_id [customer ID] --attribute_name [name] --attribute_value [value]')
            ->setDefinition([
                new InputOption(
                    'customer_id',
                    null,
                    InputOption::VALUE_REQUIRED,
                    'customer ID'
                ),
                new InputOption(
                    'attribute_name',
                    null,
                    InputOption::VALUE_REQUIRED,
                    'attribute name'
                ),
                new InputOption(
                    'attribute_value',
                    null,
                    InputOption::VALUE_REQUIRED,
                    'attribute value'
                ),
            ]);

        parent::configure();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $customerId = $input->getOption('customer_id');
        $attribute = $input->getOption('attribute_name');
        $value = $input->getOption('attribute_value');

        $customer = $this->customerRepository->getById($customerId);
        $customer->setAttribute($attribute, $value);
        $customer->save();
    }
}