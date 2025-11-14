# Special Order Bundle

[English](README.md) | [中文](README.zh-CN.md)

A Symfony bundle for managing special order offers and chances within e-commerce applications. This bundle provides functionality for creating and managing special offer opportunities for users, including product-based rewards and promotional items.

## Features

- **Offer Chance Management**: Create and manage special offer opportunities for users
- **Product Integration**: Seamless integration with SPU/SKU product system
- **Admin Interface**: Complete EasyAdmin backend for managing offers and chances
- **JSON-RPC API**: Expose offer data through JSON-RPC procedures
- **Resource Provider**: Support for material prize distribution
- **User-specific Offers**: Personalized offer chances based on user accounts
- **Time-based Validity**: Support for offer expiration and validity periods
- **Doctrine Integration**: Full ORM support with entities and repositories

## Installation

### Requirements

- PHP 8.1 or higher
- Symfony 7.3 or higher
- Doctrine ORM
- EasyAdmin Bundle 4.x

### Install via Composer

```bash
composer require tourze/special-order-bundle
```

### Enable the Bundle

```php
// config/bundles.php
return [
    // ...
    Tourze\SpecialOrderBundle\SpecialOrderBundle::class => ['all' => true],
];
```

## Configuration

The bundle requires the following dependencies to be properly configured:

```yaml
# config/packages/doctrine.yaml
doctrine:
    dbal:
        # Your database configuration
    orm:
        # Your ORM configuration
```

### Required Dependencies

This bundle depends on several other bundles:

- `DoctrineBundle`
- `OrderCoreBundle`
- `SecurityBundle`
- `JsonRPCSecurityBundle`
- `BenefitBundle`
- `ProductCoreBundle`
- `EasyAdminMenuBundle`

## Usage

### Creating Offer Chances

```php
use Tourze\SpecialOrderBundle\Entity\OfferChance;
use Tourze\SpecialOrderBundle\Entity\OfferSku;
use Tourze\ProductCoreBundle\Entity\Sku;

// Create a new offer chance
$offerChance = new OfferChance();
$offerChance->setTitle('Special Discount Offer');
$offerChance->setUser($user);
$offerChance->setStartTime(new \DateTimeImmutable());
$offerChance->setEndTime(new \DateTimeImmutable('+30 days'));
$offerChance->setValid(true);

// Add SKUs to the offer
$offerSku = new OfferSku();
$offerSku->setChance($offerChance);
$offerSku->setSku($sku);
$offerSku->setQuantity(1);
$offerSku->setPrice('99.99');
$offerSku->setCurrency('CNY');

$offerChance->addSku($offerSku);

// Persist the entities
$entityManager->persist($offerChance);
$entityManager->persist($offerSku);
$entityManager->flush();
```

### Using the Resource Provider

The bundle includes a resource provider for material prizes:

```php
use Tourze\SpecialOrderBundle\Service\SpuOfferResourceProvider;

// The provider is auto-registered and can be used through the resource management system
// It automatically creates offer chances when users receive material prizes
```

### JSON-RPC API

Access user's offer chances through JSON-RPC:

```json
{
    "jsonrpc": "2.0",
    "method": "GetOrderOfferChanceList",
    "params": {},
    "id": 1
}
```

Response:
```json
{
    "jsonrpc": "2.0",
    "result": {
        "items": [
            {
                "id": "123456789",
                "title": "Special Discount Offer"
            }
        ],
        "total": 1,
        "page": 1,
        "limit": 20
    },
    "id": 1
}
```

## Database Schema

The bundle creates two main database tables:

### order_offer_chance

Stores information about special offer opportunities:

- `id`: Primary key (snowflake ID)
- `title`: Offer title
- `user_id`: Associated user
- `start_time`: Offer start time
- `end_time`: Offer expiration time
- `use_time`: When the offer was used
- `valid`: Whether the offer is currently valid
- `contract_id`: Associated contract (if any)
- Timestamps and audit fields

### order_offer_sku

Stores SKU information for each offer:

- `id`: Primary key (snowflake ID)
- `chance_id`: Reference to the offer chance
- `sku_id`: Associated product SKU
- `quantity`: Quantity offered
- `price`: Special price
- `currency`: Currency code
- Timestamps and audit fields

## Admin Interface

The bundle provides EasyAdmin controllers for managing:

- **Offer Chances**: `/admin/order/chance`
- **Offer SKUs**: `/admin/order/sku`

These interfaces allow administrators to:
- Create and edit offer chances
- Manage associated SKUs
- Set validity periods
- Track offer usage

## Testing

Run the test suite:

```bash
composer test
```

Run PHPStan analysis:

```bash
composer analyze
```

## License

This bundle is released under the MIT License. See the [LICENSE](LICENSE) file for details.

## Contributing

Contributions are welcome! Please ensure that:

1. All tests pass
2. Code follows PSR-12 standards
3. PHPStan analysis passes
4. Documentation is updated if necessary

## Support

For issues and questions:
- Create an issue in the repository
- Check the documentation for common usage patterns
- Review existing issues for solutions