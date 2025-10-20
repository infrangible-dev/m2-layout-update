<?php

declare(strict_types=1);

namespace Infrangible\LayoutUpdate\Plugin\Framework\View\Layout;

use FeWeDev\Base\Arrays;
use Infrangible\LayoutUpdate\Model\Config\LayoutUpdate;
use Magento\Framework\View\Layout\Reader\Context;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class GeneratorPool
{
    /** @var LayoutUpdate */
    protected $layoutUpdate;

    /** @var Arrays */
    protected $arrays;

    public function __construct(LayoutUpdate $layoutUpdate, Arrays $arrays)
    {
        $this->layoutUpdate = $layoutUpdate;
        $this->arrays = $arrays;
    }

    /** @noinspection PhpUnusedParameterInspection */
    public function beforeProcess(
        \Magento\Framework\View\Layout\GeneratorPool $subject,
        Context $readerContext,
        \Magento\Framework\View\Layout\Generator\Context $generatorContext
    ): array {
        $layoutUpdates = $this->layoutUpdate->get();

        if ($layoutUpdates) {
            foreach ($layoutUpdates as $elementName => $elementUpdates) {
                $element = $readerContext->getScheduledStructure()->getStructureElementData($elementName);

                if ($element) {
                    foreach ($elementUpdates as $attributeName => $attributeUpdates) {
                        ksort(
                            $attributeUpdates,
                            SORT_NUMERIC
                        );

                        $currentValue = $this->arrays->getValue(
                            $element,
                            sprintf(
                                'attributes:%s',
                                $attributeName
                            )
                        );

                        foreach ($attributeUpdates as $actions) {
                            foreach ($actions as $action => $valueData) {
                                $value = $valueData[ 'value' ];
                                $trim = $valueData[ 'trim' ];

                                if ($currentValue === null) {
                                    $currentValue = '';
                                }

                                if ($action === 'prefix') {
                                    if (strlen($currentValue) > 0) {
                                        $currentValue = sprintf(
                                            '%s %s',
                                            $value,
                                            $currentValue
                                        );
                                    } else {
                                        $currentValue = $value;
                                    }
                                } elseif ($action === 'suffix') {
                                    if (strlen($currentValue) > 0) {
                                        $currentValue = sprintf(
                                            '%s %s',
                                            $currentValue,
                                            $value
                                        );
                                    } else {
                                        $currentValue = $value;
                                    }
                                } elseif ($action === 'remove') {
                                    $currentValue = str_replace(
                                        $value,
                                        '',
                                        $currentValue
                                    );
                                } elseif ($action === 'replace') {
                                    $currentValue = $value;
                                }

                                if ($trim) {
                                    $currentValue = trim($currentValue);
                                }
                            }
                        }

                        $element = $this->arrays->addDeepValue(
                            $element,
                            ['attributes', $attributeName],
                            $currentValue
                        );
                    }

                    $readerContext->getScheduledStructure()->setStructureElementData(
                        $elementName,
                        $element
                    );
                }
            }
        }

        return [$readerContext, $generatorContext];
    }
}
