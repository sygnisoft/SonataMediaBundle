<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\MediaBundle\Command;

use Sonata\ClassificationBundle\Model\ContextInterface;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;
use Sonata\MediaBundle\Model\CategoryManagerInterface;
use Sonata\MediaBundle\Model\MediaManagerInterface;
use Sonata\MediaBundle\Provider\Pool;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @final since sonata-project/media-bundle 3.21.0
 */
class FixMediaContextCommand extends BaseCommand
{
    /**
     * @var CategoryManagerInterface
     */
    private $categoryManager;

    /**
     * @var ContextManagerInterface
     */
    private $contextManager;

    public function __construct(MediaManagerInterface $mediaManager, Pool $pool, ContextManagerInterface $contextManager, CategoryManagerInterface $categoryManager = null)
    {
        $this->contextManager = $contextManager;
        $this->categoryManager = $categoryManager;
        parent::__construct($mediaManager, $pool);
    }

    /**
     * {@inheritdoc}
     */
    public function configure(): void
    {
        $this->setName('sonata:media:fix-media-context');
        $this->setDescription('Generate the default category for each media context');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->categoryManager) {
            throw new \LogicException('The classification feature is disabled.');
        }

        $pool = $this->getMediaPool();
        $contextManager = $this->contextManager;
        $categoryManager = $this->categoryManager;

        foreach ($pool->getContexts() as $context => $contextAttrs) {
            /** @var ContextInterface $defaultContext */
            $defaultContext = $contextManager->findOneBy([
                'id' => $context,
            ]);

            if (!$defaultContext) {
                $output->writeln(sprintf(" > default context for '%s' is missing, creating one", $context));
                $defaultContext = $contextManager->create();
                $defaultContext->setId($context);
                $defaultContext->setName(ucfirst($context));
                $defaultContext->setEnabled(true);

                $contextManager->save($defaultContext);
            }

            $defaultCategory = $categoryManager->getRootCategory($defaultContext);

            if (!$defaultCategory) {
                $output->writeln(sprintf(" > default category for '%s' is missing, creating one", $context));
                $defaultCategory = $categoryManager->create();
                $defaultCategory->setContext($defaultContext);
                $defaultCategory->setName(ucfirst($context));
                $defaultCategory->setEnabled(true);
                $defaultCategory->setPosition(0);

                $categoryManager->save($defaultCategory);
            }
        }

        $output->writeln('Done!');

        return 0;
    }
}
