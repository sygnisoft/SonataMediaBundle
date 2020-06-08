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

use Sonata\Doctrine\Model\ManagerInterface;
use Sonata\MediaBundle\Model\MediaManagerInterface;
use Sonata\MediaBundle\Provider\Pool;
use Symfony\Component\Console\Command\Command;

/**
 * This command can be used to re-generate the thumbnails for all uploaded medias.
 *
 * Useful if you have existing media content and added new formats.
 */
abstract class BaseCommand extends Command
{
    /**
     * @var MediaManagerInterface
     */
    protected $mediaManager;

    /**
     * @var Pool
     */
    private $pool;

    public function __construct(MediaManagerInterface $mediaManager, Pool $pool)
    {
        parent::__construct();
        $this->mediaManager = $mediaManager;
        $this->pool = $pool;
    }

    /**
     * @return MediaManagerInterface
     */
    public function getMediaManager()
    {
        return $this->mediaManager;
    }

    /**
     * @return Pool
     */
    public function getMediaPool()
    {
        return $this->pool;
    }
}
