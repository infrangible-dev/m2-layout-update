<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\LayoutUpdate\Model\Config\LayoutUpdate;

use DOMDocument;
use DOMElement;
use DOMNode;
use Magento\Framework\Config\ConverterInterface;

/**
 * Converts the merged layout updates into an array: element name => attribute name => sort order => action => updates
 * with type, ifConfig, configValue, trim and value.
 */
class Converter implements ConverterInterface
{
    /**
     * Converts the merged files.
     *
     * @param DOMDocument $source
     *
     * @return array
     * @phpstan-return array<string, array<string, array<int, array<string, array<int, array<string, mixed>>>>>>
     */
    public function convert($source): array
    {
        $result = [];

        foreach ($this->getChildElements($source, 'layout') as $layout) {
            foreach ($this->getChildElements($layout, 'element') as $element) {
                $elementName = $element->getAttribute('name');

                if ($elementName === '') {
                    continue;
                }

                foreach ($this->getChildElements($element, 'update') as $update) {
                    $attributeName = $update->getAttribute('attributeName');
                    $action = $update->getAttribute('action');
                    $type = $update->getAttribute('type');

                    if ($attributeName === '' || $action === '' || $type === '') {
                        continue;
                    }

                    $sortOrder = (int)$update->getAttribute('sortOrder');

                    $result[ $elementName ][ $attributeName ][ $sortOrder ][ $action ][] = [
                        'type'        => $type,
                        'ifConfig'    => $update->hasAttribute('ifConfig') ? $update->getAttribute('ifConfig') : null,
                        'configValue' => $update->hasAttribute('configValue') ?
                            $update->getAttribute('configValue') : null,
                        'trim'        => filter_var($update->getAttribute('trim'), FILTER_VALIDATE_BOOLEAN),
                        'value'       => $update->nodeValue ?? ''
                    ];
                }
            }
        }

        return $result;
    }

    /**
     * Returns the child elements with the name.
     *
     * @param DOMNode $node
     * @param string $name
     *
     * @return DOMElement[]
     */
    private function getChildElements(DOMNode $node, string $name): array
    {
        $elements = [];

        foreach ($node->childNodes as $childNode) {
            if ($childNode instanceof DOMElement && $childNode->nodeName === $name) {
                $elements[] = $childNode;
            }
        }

        return $elements;
    }
}
