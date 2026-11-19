<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\LayoutUpdate\Model\Config\LayoutUpdate;

use Magento\Framework\Config\ValidationStateInterface;

/**
 * The files etc/layout_update.xml are always validated against the schema.
 */
class ValidationState implements ValidationStateInterface
{
    /**
     * Returns that the files are validated.
     *
     * @return bool
     */
    public function isValidationRequired(): bool
    {
        return true;
    }
}
