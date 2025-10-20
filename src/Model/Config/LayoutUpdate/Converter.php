<?php

declare(strict_types=1);

namespace Infrangible\LayoutUpdate\Model\Config\LayoutUpdate;

use DOMAttr;
use DOMDocument;
use DOMNamedNodeMap;
use DOMNode;
use FeWeDev\Base\Arrays;
use Magento\Framework\Config\ConverterInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Converter implements ConverterInterface
{
    /** @var Arrays */
    protected $arrays;

    public function __construct(Arrays $arrays)
    {
        $this->arrays = $arrays;
    }

    /**
     * @param DOMDocument $source
     */
    public function convert($source): array
    {
        $result = [];

        /** @var DOMNode $childNode */
        foreach ($source->childNodes as $childNode) {
            if ($childNode->nodeName === 'layout') {
                /** @var DOMNode $childChildNode */
                foreach ($childNode->childNodes as $childChildNode) {
                    if ($childChildNode->nodeName === 'element') {
                        /** @var DOMNamedNodeMap $elementAttributes */
                        $elementAttributes = $childChildNode->attributes;

                        if ($elementAttributes) {
                            /** @var DOMAttr $elementName */
                            $elementName = $elementAttributes->getNamedItem('name');

                            if ($elementName) {
                                /** @var DOMNode $childChildChildNode */
                                foreach ($childChildNode->childNodes as $childChildChildNode) {
                                    if ($childChildChildNode->nodeName === 'update') {
                                        /** @var DOMNamedNodeMap $updateAttributes */
                                        $updateAttributes = $childChildChildNode->attributes;

                                        if ($updateAttributes) {
                                            $attributeName = $updateAttributes->getNamedItem('attributeName');
                                            $action = $updateAttributes->getNamedItem('action');
                                            $trim = $updateAttributes->getNamedItem('trim');
                                            $sortOrder = $updateAttributes->getNamedItem('sortOrder');

                                            if ($attributeName && $action) {
                                                $result = $this->arrays->addDeepValue(
                                                    $result,
                                                    [
                                                        (string)$elementName->nodeValue,
                                                        (string)$attributeName->nodeValue,
                                                        $sortOrder ? (int)$sortOrder->nodeValue : 0,
                                                        (string)$action->nodeValue
                                                    ],
                                                    [
                                                        'value' => (string)$childChildChildNode->nodeValue,
                                                        'trim'  => $trim ? filter_var(
                                                            (string)$trim->nodeValue,
                                                            FILTER_VALIDATE_BOOLEAN
                                                        ) : false
                                                    ]
                                                );
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }
}