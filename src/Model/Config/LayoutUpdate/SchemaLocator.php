<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\LayoutUpdate\Model\Config\LayoutUpdate;

use Magento\Framework\Config\SchemaLocatorInterface;
use Magento\Framework\Module\Dir;
use Magento\Framework\Module\Dir\Reader;

/**
 * The schema of the files etc/layout_update.xml (etc/layout_update.xsd of this module).
 */
class SchemaLocator implements SchemaLocatorInterface
{
    /** File name of the schema in the directory etc of the module. */
    public const CONFIG_FILE_SCHEMA = 'layout_update.xsd';

    /** @var string */
    private readonly string $schema;

    /**
     * @param Reader $moduleReader
     */
    public function __construct(Reader $moduleReader)
    {
        $this->schema = $moduleReader->getModuleDir(Dir::MODULE_ETC_DIR, 'Infrangible_LayoutUpdate') .
            '/' . self::CONFIG_FILE_SCHEMA;
    }

    /**
     * Returns the schema of the merged configuration.
     *
     * @return string|null
     */
    public function getSchema(): ?string
    {
        return $this->schema;
    }

    /**
     * Returns the schema of every file.
     *
     * @return string|null
     */
    public function getPerFileSchema(): ?string
    {
        return $this->schema;
    }
}
