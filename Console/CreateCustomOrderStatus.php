<?php
/**
 * Copyright ©2019 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Attributes\Console;

use Exception;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Sales\Model\Order\Status;
use Magento\Sales\Model\Order\StatusFactory;
use Magento\Sales\Model\ResourceModel\Order\Status as StatusResource;
use Magento\Sales\Model\ResourceModel\Order\StatusFactory as StatusResourceFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CustomerAttributeSet
 * @package Emartech\Attributes\Console
 */
class CreateCustomOrderStatus extends Command
{
    const ORDER_STATE_COMPLETE = 'complete';

    /**
     * CustomerAttributeSet constructor.
     *
     * @param StatusFactory $statusFactory
     * @param StatusResourceFactory $statusResourceFactory
     * @param string|null $name
     */
    public function __construct(
        StatusFactory $statusFactory,
        StatusResourceFactory $statusResourceFactory,
        string $name = null
    ) {
        $this->statusFactory = $statusFactory;
        $this->statusResourceFactory = $statusResourceFactory;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setName('emartech:custom_order_status:create')
            ->setDescription('Create a custom order status with complete state use --status_name [slug type name] --status_label [Status Label]')
            ->setDefinition([
                new InputOption(
                    'status_name',
                    null,
                    InputOption::VALUE_REQUIRED,
                    'status name'
                ),
                new InputOption(
                    'status_label',
                    null,
                    InputOption::VALUE_REQUIRED,
                    'status label'
                )
            ]);

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var StatusResource $statusResource */
        $statusResource = $this->statusResourceFactory->create();
        /** @var Status $status */
        $status = $this->statusFactory->create();
        $status->setData([
            'status' => $input->getOption('status_name'),
            'label' => $input->getOption('status_label'),
        ]);
        try {
            $statusResource->save($status);
        } catch (AlreadyExistsException $exception) {
            return;
        }
        $status->assignState(self::ORDER_STATE_COMPLETE, false, true);
    }
}