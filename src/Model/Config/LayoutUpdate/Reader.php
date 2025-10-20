<?php

declare(strict_types=1);

namespace Infrangible\LayoutUpdate\Model\Config\LayoutUpdate;

use Magento\Framework\Config\Dom;
use Magento\Framework\Config\FileResolverInterface;
use Magento\Framework\Config\Reader\Filesystem;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Reader extends Filesystem
{
    /** @var array */
    protected $_idAttributes = [
        '/layout/element' => 'name'
    ];

    /**
     * @param array                 $idAttributes
     * @param string                $domDocumentClass
     * @param string                $defaultScope
     */
    public function __construct(
        FileResolverInterface $fileResolver,
        Converter $converter,
        SchemaLocator $schemaLocator,
        ValidationState $validationState,
        $idAttributes = [],
        $domDocumentClass = Dom::class,
        $defaultScope = 'global'
    ) {
        parent::__construct(
            $fileResolver,
            $converter,
            $schemaLocator,
            $validationState,
            'layout_update.xml',
            $idAttributes,
            $domDocumentClass,
            $defaultScope
        );
    }
}