<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\LayoutUpdate\Test\Unit\Plugin;

use FeWeDev\Base\Variables;
use Infrangible\Core\Helper\Stores;
use Infrangible\LayoutUpdate\Model\Config\LayoutUpdate;
use Infrangible\LayoutUpdate\Plugin\Framework\View\Layout\GeneratorPool;
use Magento\Framework\View\Layout\Generator\Context as GeneratorContext;
use Magento\Framework\View\Layout\Reader\Context;
use Magento\Framework\View\Layout\ScheduledStructure;
use PHPUnit\Framework\TestCase;

/**
 * Tests the updates of the attributes of layout elements: the actions, their order and their conditions.
 */
class GeneratorPoolTest extends TestCase
{
    /**
     * Returns the plugin with the layout updates and the configuration values.
     *
     * @param array<string, mixed> $layoutUpdates
     * @param array<string, mixed> $config
     *
     * @return GeneratorPool
     */
    private function createPlugin(array $layoutUpdates = [], array $config = []): GeneratorPool
    {
        $layoutUpdate = $this->createStub(LayoutUpdate::class);
        $layoutUpdate->method('get')->willReturn($layoutUpdates);

        $storeHelper = $this->createStub(Stores::class);
        $storeHelper->method('getStoreConfig')->willReturnCallback(fn(string $path): mixed => $config[ $path ] ?? null);
        $storeHelper->method('getStoreConfigFlag')->willReturnCallback(
            fn(string $path): bool => (bool)($config[ $path ] ?? false)
        );

        return new GeneratorPool($layoutUpdate, $storeHelper, new Variables());
    }

    /**
     * Returns an update.
     *
     * @param string $value
     * @param string|null $ifConfig
     * @param string|null $configValue
     * @param string $type
     *
     * @return array<string, mixed>
     */
    private function update(
        string $value,
        ?string $ifConfig = null,
        ?string $configValue = null,
        string $type = 'string'
    ): array {
        return ['type' => $type, 'ifConfig' => $ifConfig, 'configValue' => $configValue, 'trim' => false,
                'value' => $value];
    }

    /**
     * Tests the actions in the order of the sort order.
     *
     * @return void
     */
    public function testActions(): void
    {
        $plugin = $this->createPlugin();

        $this->assertSame(
            'first page-main last',
            $plugin->applyUpdates(
                'page-main page',
                [
                    20 => ['suffix' => [$this->update('last')]],
                    5  => ['remove' => [$this->update('page')]],
                    10 => ['prefix' => [$this->update('first')]]
                ]
            )
        );
        $this->assertSame('new', $plugin->applyUpdates('old', [0 => ['replace' => [$this->update('new')]]]));
        $this->assertSame('added', $plugin->applyUpdates('', [0 => ['suffix' => [$this->update('added')]]]));
    }

    /**
     * Tests that remove removes whole words; before, "page" was removed from "page-main" as well.
     *
     * @return void
     */
    public function testRemoveWords(): void
    {
        $this->assertSame(
            'page-main sidebar',
            $this->createPlugin()->applyUpdates('page-main page sidebar', [0 => ['remove' => [$this->update('page')]]])
        );
    }

    /**
     * Tests the conditions: configuration flag, negated flag, configuration value, and values of the configuration.
     *
     * @return void
     */
    public function testConditions(): void
    {
        $plugin = $this->createPlugin([], ['a/b/on' => '1', 'a/b/off' => '0', 'a/b/direction' => 'top',
                                           'a/b/class' => 'from-config']);

        $this->assertSame(
            'on not-off top from-config',
            $plugin->applyUpdates(
                '',
                [
                    0 => [
                        'suffix' => [
                            $this->update('on', 'a/b/on'),
                            $this->update('off', 'a/b/off'),
                            $this->update('not-off', '!a/b/off'),
                            $this->update('not-on', '!a/b/on'),
                            $this->update('top', 'a/b/direction', 'top'),
                            $this->update('bottom', 'a/b/direction', 'bottom'),
                            $this->update('a/b/class', null, null, 'config'),
                            $this->update('a/b/missing', null, null, 'config')
                        ]
                    ]
                ]
            )
        );
    }

    /**
     * Tests that the updates are applied to the attributes of the scheduled elements.
     *
     * @return void
     */
    public function testBeforeProcess(): void
    {
        $scheduledStructure = new ScheduledStructure();
        $scheduledStructure->setStructureElementData('main', ['attributes' => ['htmlClass' => 'column main']]);

        $readerContext = $this->createStub(Context::class);
        $readerContext->method('getScheduledStructure')->willReturn($scheduledStructure);

        $plugin = $this->createPlugin(
            [
                'main'    => ['htmlClass' => [10 => ['suffix' => [$this->update('wide')]]]],
                'missing' => ['htmlClass' => [10 => ['suffix' => [$this->update('ignored')]]]]
            ]
        );

        $plugin->beforeProcess(
            $this->createStub(\Magento\Framework\View\Layout\GeneratorPool::class),
            $readerContext,
            $this->createStub(GeneratorContext::class)
        );

        $this->assertSame(
            ['attributes' => ['htmlClass' => 'column main wide']],
            $scheduledStructure->getStructureElementData('main')
        );
        $this->assertNull($scheduledStructure->getStructureElementData('missing'));
    }
}
