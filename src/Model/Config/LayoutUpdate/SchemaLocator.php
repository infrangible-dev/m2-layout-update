<?php

declare(strict_types=1);

namespace Infrangible\LayoutUpdate\Model\Config\LayoutUpdate;

use Magento\Framework\Config\SchemaLocatorInterface;
use Magento\Framework\Module\Dir;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class SchemaLocator implements SchemaLocatorInterface
{
    const CONFIG_FILE_SCHEMA = 'layout_update.xsd';

    /** @var string */
    private $schema;

    /** @var string */
    private $perFileSchema;

    public function __construct(\Magento\Framework\Module\Dir\Reader $moduleReader)
    {
        $configDir = $moduleReader->getModuleDir(
            Dir::MODULE_ETC_DIR,
            'Infrangible_LayoutUpdate'
        );

        $this->schema = $configDir . DIRECTORY_SEPARATOR . self::CONFIG_FILE_SCHEMA;
        $this->perFileSchema = $configDir . DIRECTORY_SEPARATOR . self::CONFIG_FILE_SCHEMA;
    }

    public function getSchema(): ?string
    {
        return $this->schema;
    }

    public function getPerFileSchema(): ?string
    {
        return $this->perFileSchema;
    }
}
