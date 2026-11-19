<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\LayoutUpdate\Model\Config\LayoutUpdate;

use Magento\Framework\Config\Dom;
use Magento\Framework\Config\FileResolverInterface;
use Magento\Framework\Config\Reader\Filesystem;

/**
 * Reads and merges the files etc/layout_update.xml of the modules; elements with the same name are merged.
 */
class Reader extends Filesystem
{
    /**
     * @var array
     * @phpstan-var array<string, string>
     */
    protected $_idAttributes = ['/layout/element' => 'name'];

    /**
     * @param FileResolverInterface $fileResolver
     * @param Converter $converter
     * @param SchemaLocator $schemaLocator
     * @param ValidationState $validationState
     * @param array $idAttributes
     * @param string $domDocumentClass
     * @param string $defaultScope
     * @phpstan-param array<string, string> $idAttributes
     */
    public function __construct(
        FileResolverInterface $fileResolver,
        Converter $converter,
        SchemaLocator $schemaLocator,
        ValidationState $validationState,
        array $idAttributes = [],
        string $domDocumentClass = Dom::class,
        string $defaultScope = 'global'
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
