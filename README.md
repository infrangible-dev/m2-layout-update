# Infrangible Layout Update

Changes attributes of layout elements after Magento merged the layout, for example CSS classes of containers, also
depending on the configuration. The updates are declared in `etc/layout_update.xml` of a module (schema:
`etc/layout_update.xsd` of this module, referenced as
`urn:magento:module:Infrangible_LayoutUpdate:etc/layout_update.xsd` so that IDEs find it; Magento validates with the
schema of the module either way):

```xml
<?xml version="1.0"?>
<layout xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:module:Infrangible_LayoutUpdate:etc/layout_update.xsd">
    <element name="div.sidebar.main">
        <update attributeName="htmlClass" action="suffix" type="string" sortOrder="10">sidebar-wide</update>
        <update attributeName="htmlClass" action="suffix" type="string"
                ifConfig="vendor_module/general/direction" configValue="top">sidebar-top</update>
        <update attributeName="htmlClass" action="remove" type="string" ifConfig="!vendor_module/general/show">
            sidebar-visible
        </update>
    </element>
</layout>
```

| Attribute       | Meaning                                                                                              |
|-----------------|------------------------------------------------------------------------------------------------------|
| `attributeName` | attribute of the element, for example `htmlClass`, `htmlId` or `htmlTag` of a container              |
| `action`        | `prefix` and `suffix` add the value separated by a space, `remove` removes its words, `replace` replaces the attribute |
| `type`          | `string`: the value is the content of the element; `config`: the content is a configuration path, its value of the store is used |
| `ifConfig`      | only if the configuration flag is set, with a leading `!` only if it is not set                      |
| `configValue`   | with `ifConfig`: only if the configuration value is this value                                       |
| `trim`          | removes whitespace at the beginning and end of the attribute afterwards                              |
| `sortOrder`     | updates of an attribute are applied in the order of their sort order                                 |

The updates of all modules are merged and cached with the configuration.

## Requirements

- Magento Open Source or Adobe Commerce 2.4.6 or newer
- PHP 8.2 or newer

## Installation

```bash
composer require infrangible/m2-layout-update
bin/magento module:enable Infrangible_LayoutUpdate
bin/magento setup:upgrade
```

In production mode, also run `bin/magento setup:di:compile`.

## License

Infrangible Layout Update is licensed under the MIT License - see the LICENSE file for details.
