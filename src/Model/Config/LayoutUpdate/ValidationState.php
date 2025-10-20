<?php

declare(strict_types=1);

namespace Infrangible\LayoutUpdate\Model\Config\LayoutUpdate;

use Magento\Framework\Config\ValidationStateInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class ValidationState implements ValidationStateInterface
{
    public function isValidationRequired(): bool
    {
        return true;
    }
}
