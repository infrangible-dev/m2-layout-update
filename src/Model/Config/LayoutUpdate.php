<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\LayoutUpdate\Model\Config;

use Infrangible\LayoutUpdate\Model\Config\LayoutUpdate\Reader;
use Magento\Framework\Config\CacheInterface;
use Magento\Framework\Config\Data;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * The merged layout updates of the layout_update.xml files of all modules, cached in the configuration cache.
 */
class LayoutUpdate extends Data
{
    /**
     * @param Reader $reader
     * @param CacheInterface $cache
     * @param SerializerInterface|null $serializer
     */
    public function __construct(Reader $reader, CacheInterface $cache, ?SerializerInterface $serializer = null)
    {
        parent::__construct($reader, $cache, 'infrangible_layout_update', $serializer);
    }
}
