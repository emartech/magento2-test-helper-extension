<?php
/**
 * Copyright ©2020 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Attributes\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\Event\ManagerInterface;

class CreateCustomEventData extends Command
{
    const DATA     = 'data';
    const ID       = 'id';
    const STORE_ID = 'store_id';

    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * CreateCustomEventData constructor.
     *
     * @param ManagerInterface $eventManager
     * @param string|null      $name
     */
    public function __construct(
        ManagerInterface $eventManager,
        string $name = null
    ) {
        $this->eventManager = $eventManager;

        parent::__construct($name);
    }


    protected function configure()
    {
        $this
            ->setName('emartech:customevent:create')
            ->setDescription(
                'Create custom event - use --data [DATA] --id [EVENT_ID] --store_id [STORE_ID]'
            )
            ->setDefinition(
                [
                    new InputOption(
                        self::DATA,
                        null,
                        InputOption::VALUE_REQUIRED,
                        'Data'
                    ),
                    new InputOption(
                        self::ID,
                        null,
                        InputOption::VALUE_REQUIRED,
                        'Id'
                    ),
                    new InputOption(
                        self::STORE_ID,
                        null,
                        InputOption::VALUE_REQUIRED,
                        'Store ID'
                    ),
                ]
            );

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

        $data = $input->getOption(self::DATA);
        $id = $input->getOption(self::ID);
        $storeId = $input->getOption(self::STORE_ID);

        if (!$id) {
            $id = rand(10000, 99999);
        }
        if (!$storeId) {
            $storeId = 1;
        }

        $this->eventManager->dispatch(
            'emarsys_create_custom_event',
            [
                'event_id'   => $id,
                'event_data' => $data,
                'store_id'   => $storeId,
            ]
        );

        $output->writeln("Finished");
    }
}