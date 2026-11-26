# Changelog

## 2.0.1

### Changed

- Supports PHP 8.1 again, like Magento 2.4.6: the Adobe Commerce Marketplace requires modules to allow every PHP
  version of the Magento versions they support.

## 2.0.0

### Breaking changes

- Requires Magento 2.4.6 or newer and PHP 8.2 or newer. Magento 2.4.4 and 2.4.5 and PHP 7.1 to 8.1 are no longer
  supported; use version 1.x for these.
- Requires `infrangible/m2-core` 5.0, `infrangible/m2-foundation` 3.0 and `fewedev/base` 2.4; before, the module used
  m2-core and fewedev/base without requiring them.
- The action `remove` removes whole words (for example CSS classes) instead of every occurrence of the text; before,
  removing the class `page` turned `page-main` into `-main`.
- All classes have native types, and the constructors use constructor property promotion.

### Fixed

- No deprecation notice of PHP 8.4 for the implicitly nullable serializer of the configuration.
- Values of the configuration that are not set (type `config`) add nothing, instead of a space and warnings of PHP.

### Changed

- The module is loaded after the modules it uses.
- README.
- Checks with PHPStan level 9, the Magento coding standard and unit tests on every push.
