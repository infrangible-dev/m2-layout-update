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
                            /** @var DOMAttr $elementNameNode */
                            $elementNameNode = $elementAttributes->getNamedItem('name');

                            if ($elementNameNode) {
                                /** @var DOMNode $childChildChildNode */
                                foreach ($childChildNode->childNodes as $childChildChildNode) {
                                    if ($childChildChildNode->nodeName === 'update') {
                                        /** @var DOMNamedNodeMap $updateAttributes */
                                        $updateAttributes = $childChildChildNode->attributes;

                                        if ($updateAttributes) {
                                            /** @var DOMAttr $attributeNameNode */
                                            $attributeNameNode = $updateAttributes->getNamedItem('attributeName');
                                            /** @var DOMAttr $actionNode */
                                            $actionNode = $updateAttributes->getNamedItem('action');
                                            /** @var DOMAttr $typeNode */
                                            $typeNode = $updateAttributes->getNamedItem('type');
                                            /** @var DOMAttr $ifConfigNode */
                                            $ifConfigNode = $updateAttributes->getNamedItem('ifConfig');
                                            /** @var DOMAttr $configValueNode */
                                            $configValueNode = $updateAttributes->getNamedItem('configValue');
                                            /** @var DOMAttr $trimNode */
                                            $trimNode = $updateAttributes->getNamedItem('trim');
                                            /** @var DOMAttr $sortOrderNode */
                                            $sortOrderNode = $updateAttributes->getNamedItem('sortOrder');

                                            if ($attributeNameNode && $actionNode && $typeNode) {
                                                $elementName = (string)$elementNameNode->nodeValue;
                                                $attributeName = (string)$attributeNameNode->nodeValue;
                                                $sortOrder = $sortOrderNode ? (int)$sortOrderNode->nodeValue : 0;
                                                $action = (string)$actionNode->nodeValue;
                                                $type = (string)$typeNode->nodeValue;
                                                $ifConfig = $ifConfigNode ? (string)$ifConfigNode->nodeValue : null;
                                                $configValue =
                                                    $configValueNode ? (string)$configValueNode->nodeValue : null;
                                                $trim = $trimNode ? filter_var(
                                                    (string)$trimNode->nodeValue,
                                                    FILTER_VALIDATE_BOOLEAN
                                                ) : false;
                                                $value = (string)$childChildChildNode->nodeValue;

                                                $result[ $elementName ][ $attributeName ][ $sortOrder ][ $action ][] = [
                                                    'type'        => $type,
                                                    'ifConfig'    => $ifConfig,
                                                    'configValue' => $configValue,
                                                    'trim'        => $trim,
                                                    'value'       => $value
                                                ];
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