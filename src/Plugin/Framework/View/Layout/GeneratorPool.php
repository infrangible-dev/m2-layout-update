<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\LayoutUpdate\Plugin\Framework\View\Layout;

use FeWeDev\Base\Variables;
use Infrangible\Core\Helper\Stores;
use Infrangible\LayoutUpdate\Model\Config\LayoutUpdate;
use Magento\Framework\View\Layout\Generator\Context as GeneratorContext;
use Magento\Framework\View\Layout\Reader\Context;

/**
 * Applies the layout updates (etc/layout_update.xml) to the attributes of the layout elements after the layout was
 * merged and before its blocks and containers are generated, for example additional CSS classes of a container.
 */
class GeneratorPool
{
    /**
     * @param LayoutUpdate $layoutUpdate
     * @param Stores $storeHelper
     * @param Variables $variables
     */
    public function __construct(
        protected LayoutUpdate $layoutUpdate,
        protected Stores $storeHelper,
        protected Variables $variables
    ) {
    }

    /**
     * Applies the updates to the elements of the scheduled structure.
     *
     * @param \Magento\Framework\View\Layout\GeneratorPool $subject
     * @param Context $readerContext
     * @param GeneratorContext $generatorContext
     *
     * @return array
     * @phpstan-return array{0: Context, 1: GeneratorContext}
     * @noinspection PhpUnusedParameterInspection
     */
    public function beforeProcess(
        \Magento\Framework\View\Layout\GeneratorPool $subject,
        Context $readerContext,
        GeneratorContext $generatorContext
    ): array {
        $layoutUpdates = $this->layoutUpdate->get();
        $scheduledStructure = $readerContext->getScheduledStructure();

        foreach (is_array($layoutUpdates) ? $layoutUpdates : [] as $elementName => $elementUpdates) {
            // Magento declares the result as null, it is the scheduled data of the element
            /** @var array<string, mixed>|null $element */
            $element = $scheduledStructure->getStructureElementData((string)$elementName);

            if (! is_array($element) || count($element) === 0 || ! is_array($elementUpdates)) {
                continue;
            }

            $attributes = is_array($element[ 'attributes' ] ?? null) ? $element[ 'attributes' ] : [];

            foreach ($elementUpdates as $attributeName => $attributeUpdates) {
                $attributes[ $attributeName ] = $this->applyUpdates(
                    $this->variables->stringValueOrDefault($attributes[ $attributeName ] ?? null),
                    is_array($attributeUpdates) ? $attributeUpdates : []
                );
            }

            $element[ 'attributes' ] = $attributes;

            $scheduledStructure->setStructureElementData((string)$elementName, $element);
        }

        return [$readerContext, $generatorContext];
    }

    /**
     * Applies the updates of an attribute in the order of their sort order.
     *
     * @param string $currentValue
     * @param array $attributeUpdates
     * @phpstan-param array<int|string, mixed> $attributeUpdates sort order => action => updates
     *
     * @return string
     */
    public function applyUpdates(string $currentValue, array $attributeUpdates): string
    {
        ksort($attributeUpdates, SORT_NUMERIC);

        foreach ($attributeUpdates as $actions) {
            foreach (is_array($actions) ? $actions : [] as $action => $updates) {
                foreach (is_array($updates) ? $updates : [] as $update) {
                    if (! is_array($update) || ! $this->isActive($update)) {
                        continue;
                    }

                    $value = $this->variables->stringValueOrDefault($update[ 'value' ] ?? null);

                    if (($update[ 'type' ] ?? null) === 'config') {
                        $value = $this->variables->stringValueOrDefault($this->storeHelper->getStoreConfig($value));
                    }

                    $currentValue = $this->applyAction($currentValue, (string)$action, $value);

                    if (! empty($update[ 'trim' ])) {
                        $currentValue = trim($currentValue);
                    }
                }
            }
        }

        return $currentValue;
    }

    /**
     * Tells whether the condition of the update is met.
     *
     * The configuration flag of ifConfig is set (not set with a leading "!"), or the configuration value of ifConfig
     * is configValue.
     *
     * @param array $update
     * @phpstan-param array<string, mixed> $update
     *
     * @return bool
     */
    protected function isActive(array $update): bool
    {
        $ifConfig = trim($this->variables->stringValueOrDefault($update[ 'ifConfig' ] ?? null));

        if ($ifConfig === '') {
            return true;
        }

        $configValue = $update[ 'configValue' ] ?? null;

        if (! $this->variables->isEmpty($configValue)) {
            return $this->variables->stringValueOrDefault($this->storeHelper->getStoreConfig($ifConfig)) ===
                $this->variables->stringValueOrDefault($configValue);
        }

        if (str_starts_with($ifConfig, '!')) {
            return ! $this->storeHelper->getStoreConfigFlag(trim(substr($ifConfig, 1)));
        }

        return $this->storeHelper->getStoreConfigFlag($ifConfig);
    }

    /**
     * Applies an action to the value of the attribute.
     *
     * Prefix and suffix add the value separated by a space (an empty value adds nothing), remove removes the words of
     * the value (for example CSS classes), replace replaces the value.
     *
     * @param string $currentValue
     * @param string $action
     * @param string $value
     *
     * @return string
     */
    protected function applyAction(string $currentValue, string $action, string $value): string
    {
        return match ($action) {
            'prefix'  => trim($value . ' ' . $currentValue),
            'suffix'  => trim($currentValue . ' ' . $value),
            'remove'  => implode(' ', array_diff($this->getWords($currentValue), $this->getWords($value))),
            'replace' => $value,
            default   => $currentValue
        };
    }

    /**
     * Returns the words of a value, separated by whitespace.
     *
     * @param string $value
     *
     * @return string[]
     */
    private function getWords(string $value): array
    {
        return preg_split('/\s+/', $value, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    }
}
