<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\LayoutUpdate\Test\Unit\Model\Config\LayoutUpdate;

use DOMDocument;
use Infrangible\LayoutUpdate\Model\Config\LayoutUpdate\Converter;
use PHPUnit\Framework\TestCase;

/**
 * Tests the conversion of the layout updates.
 */
class ConverterTest extends TestCase
{
    /**
     * Tests the updates by element, attribute, sort order and action; elements without name and updates without
     * required attributes are left out.
     *
     * @return void
     */
    public function testConvert(): void
    {
        $document = new DOMDocument();
        $document->loadXML(
            '<layout>' .
            '<element name="main">' .
            '<update attributeName="htmlClass" action="suffix" type="string" sortOrder="10">a b</update>' .
            '<update attributeName="htmlClass" action="remove" type="config" ifConfig="!x/y" trim="true">c</update>' .
            '<update attributeName="htmlClass" type="string">no action</update>' .
            '</element>' .
            '<element><update attributeName="htmlClass" action="suffix" type="string">no name</update></element>' .
            '</layout>'
        );

        $suffix = ['type' => 'string', 'ifConfig' => null, 'configValue' => null, 'trim' => false, 'value' => 'a b'];
        $remove = ['type' => 'config', 'ifConfig' => '!x/y', 'configValue' => null, 'trim' => true, 'value' => 'c'];

        $this->assertSame(
            ['main' => ['htmlClass' => [10 => ['suffix' => [$suffix]], 0 => ['remove' => [$remove]]]]],
            (new Converter())->convert($document)
        );
    }
}
