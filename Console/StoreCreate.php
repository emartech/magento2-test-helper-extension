<?php
/**
 * Copyright ©2020 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Attributes\Console;

use Magento\Store\Api\Data\GroupInterfaceFactory;
use Magento\Store\Api\Data\StoreInterfaceFactory;
use Magento\Store\Api\Data\WebsiteInterfaceFactory;
use Magento\Store\Api\GroupRepositoryInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Model\Group;
use Magento\Store\Model\Store;
use Magento\Store\Model\Website;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StoreCreate extends Command
{
    const WEBSITE_SUFFIX     = 'website';
    const STORE_GROUP_SUFFIX = 'store_group';
    const STORE_SUFFIX       = 'store';

    const CODE = 'code';
    const NAME = 'name';

    /**
     * @var WebsiteRepositoryInterface
     */
    private $websiteRepository;

    /**
     * @var WebsiteInterfaceFactory
     */
    private $websiteFactory;

    /**
     * @var GroupRepositoryInterface
     */
    private $groupRepository;

    /**
     * @var GroupInterfaceFactory
     */
    private $groupFactory;

    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @var StoreInterfaceFactory
     */
    private $storeFactory;

    /**
     * StoreCreate constructor.
     *
     * @param WebsiteRepositoryInterface $websiteRepository
     * @param WebsiteInterfaceFactory    $websiteFactory
     * @param GroupRepositoryInterface   $groupRepository
     * @param GroupInterfaceFactory      $groupFactory
     * @param StoreRepositoryInterface   $storeRepository
     * @param StoreInterfaceFactory      $storeFactory
     * @param string|null                $name
     */
    public function __construct(
        WebsiteRepositoryInterface $websiteRepository,
        WebsiteInterfaceFactory $websiteFactory,
        GroupRepositoryInterface $groupRepository,
        GroupInterfaceFactory $groupFactory,
        StoreRepositoryInterface $storeRepository,
        StoreInterfaceFactory $storeFactory,
        string $name = null
    ) {
        $this->websiteRepository = $websiteRepository;
        $this->websiteFactory = $websiteFactory;
        $this->groupRepository = $groupRepository;
        $this->groupFactory = $groupFactory;
        $this->storeRepository = $storeRepository;
        $this->storeFactory = $storeFactory;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setName('emartech:store:create')
            ->setDescription('Create test website, store group, store - use --name [name] --code [code]')
            ->setDefinition([
                new InputOption(
                    'name',
                    null,
                    InputOption::VALUE_REQUIRED,
                    'Name'
                ),
                new InputOption(
                    'code',
                    null,
                    InputOption::VALUE_REQUIRED,
                    'Code'
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

        $code = $input->getOption(self::CODE);
        $name = $input->getOption(self::NAME);

        $websiteCode = $code . '_' . self::WEBSITE_SUFFIX;
        $websiteName = $name . ' ' . self::WEBSITE_SUFFIX;

        try {
            $website = $this->websiteRepository->get($websiteCode);
        } catch (\Exception $e) {
            /** @var Website $website */
            $website = $this->websiteFactory->create();
            $website
                ->setCode($websiteCode)
                ->setName($websiteName)
                ->save();
        }

        if ($website->getId()) {
            $groupCode = $code . '_' . self::STORE_GROUP_SUFFIX;
            $groupName = $name . ' ' . self::STORE_GROUP_SUFFIX;

            /** @var Group $group */
            $group = $this->groupFactory->create();
            $group
                ->setName($groupName)
                ->setCode($groupCode)
                ->setWebsiteId($website->getId())
                ->setRootCategoryId(2)
                ->save();

            if ($group->getId()) {
                $storeCode = $code . '_' . self::STORE_SUFFIX;
                $storeName = $name . ' ' . self::STORE_SUFFIX;

                try {
                    $store = $this->storeRepository->get($storeCode);
                } catch (\Exception $e) {
                    /** @var Store $store */
                    $store = $this->storeFactory->create();
                    $store
                        ->setWebsiteId($website->getId())
                        ->setGroupId($group->getId())
                        ->setName($storeName)
                        ->setCode($storeCode)
                        ->setIsActive(1)
                        ->save();
                }
            }
        }

        $output->writeln("Finished");
    }
}