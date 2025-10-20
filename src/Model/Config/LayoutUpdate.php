<?php

declare(strict_types=1);

namespace Infrangible\LayoutUpdate\Model\Config;

use Infrangible\LayoutUpdate\Model\Config\LayoutUpdate\Reader;
use Magento\Framework\Config\CacheInterface;
use Magento\Framework\Config\Data;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class LayoutUpdate extends Data
{
    public function __construct(
        Reader $reader,
        CacheInterface $cache,
        SerializerInterface $serializer = null
    ) {
        parent::__construct(
            $reader,
            $cache,
            'infrangible_layout_update',
            $serializer
        );
    }
}
