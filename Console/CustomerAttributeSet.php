<?php
/**
 * Copyright ©2019 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Attributes\Console;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerFactory;
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
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * CustomerAttributeSet constructor.
     *
     * @param CustomerRepositoryInterface $customerRepository
     * @param string|null                 $name
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        CustomerFactory $customerFactory,
        string $name = null
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        parent::__construct($name);
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

        $customer = $this->customerFactory->create()->load($customerId)->getDataModel();
        $customer->setCustomAttribute($attribute, $value);
        $this->customerRepository->save($customer);
    }
}