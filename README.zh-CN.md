# 特惠订单 Bundle

[English](README.md) | [中文](README.zh-CN.md)

一个用于在电子商务应用中管理特惠订单和机会的 Symfony Bundle。此包提供创建和管理用户特惠机会的功能，包括基于产品的奖励和促销商品。

## 功能特性

- **特惠机会管理**：为用户创建和管理特惠机会
- **产品集成**：与 SPU/SKU 产品系统无缝集成
- **管理界面**：完整的 EasyAdmin 后台管理界面
- **JSON-RPC API**：通过 JSON-RPC 程序暴露特惠数据
- **资源提供者**：支持实物奖品发放
- **用户专属特惠**：基于用户账户的个性化特惠机会
- **时效性验证**：支持特惠过期和有效期管理
- **Doctrine 集成**：完整的 ORM 支持，包含实体和仓储

## 安装

### 系统要求

- PHP 8.1 或更高版本
- Symfony 7.3 或更高版本
- Doctrine ORM
- EasyAdmin Bundle 4.x

### 通过 Composer 安装

```bash
composer require tourze/special-order-bundle
```

### 启用 Bundle

```php
// config/bundles.php
return [
    // ...
    Tourze\SpecialOrderBundle\SpecialOrderBundle::class => ['all' => true],
];
```

## 配置

此包需要正确配置以下依赖项：

```yaml
# config/packages/doctrine.yaml
doctrine:
    dbal:
        # 您的数据库配置
    orm:
        # 您的 ORM 配置
```

### 必需依赖

此包依赖于以下几个 bundle：

- `DoctrineBundle`
- `OrderCoreBundle`
- `SecurityBundle`
- `JsonRPCSecurityBundle`
- `BenefitBundle`
- `ProductCoreBundle`
- `EasyAdminMenuBundle`

## 使用方法

### 创建特惠机会

```php
use Tourze\SpecialOrderBundle\Entity\OfferChance;
use Tourze\SpecialOrderBundle\Entity\OfferSku;
use Tourze\ProductCoreBundle\Entity\Sku;

// 创建新的特惠机会
$offerChance = new OfferChance();
$offerChance->setTitle('特别折扣优惠');
$offerChance->setUser($user);
$offerChance->setStartTime(new \DateTimeImmutable());
$offerChance->setEndTime(new \DateTimeImmutable('+30 days'));
$offerChance->setValid(true);

// 向特惠添加 SKU
$offerSku = new OfferSku();
$offerSku->setChance($offerChance);
$offerSku->setSku($sku);
$offerSku->setQuantity(1);
$offerSku->setPrice('99.99');
$offerSku->setCurrency('CNY');

$offerChance->addSku($offerSku);

// 持久化实体
$entityManager->persist($offerChance);
$entityManager->persist($offerSku);
$entityManager->flush();
```

### 使用资源提供者

此包包含一个实物奖品资源提供者：

```php
use Tourze\SpecialOrderBundle\Service\SpuOfferResourceProvider;

// 提供者会自动注册，可以通过资源管理系统使用
// 当用户收到实物奖品时，会自动创建特惠机会
```

### JSON-RPC API

通过 JSON-RPC 访问用户的特惠机会：

```json
{
    "jsonrpc": "2.0",
    "method": "GetOrderOfferChanceList",
    "params": {},
    "id": 1
}
```

响应：
```json
{
    "jsonrpc": "2.0",
    "result": {
        "items": [
            {
                "id": "123456789",
                "title": "特别折扣优惠"
            }
        ],
        "total": 1,
        "page": 1,
        "limit": 20
    },
    "id": 1
}
```

## 数据库架构

此包创建两个主要的数据库表：

### order_offer_chance

存储特惠机会信息：

- `id`：主键（雪花 ID）
- `title`：特惠标题
- `user_id`：关联用户
- `start_time`：特惠开始时间
- `end_time`：特惠过期时间
- `use_time`：特惠使用时间
- `valid`：特惠是否当前有效
- `contract_id`：关联合同（如果有）
- 时间戳和审计字段

### order_offer_sku

存储每个特惠的 SKU 信息：

- `id`：主键（雪花 ID）
- `chance_id`：特惠机会引用
- `sku_id`：关联产品 SKU
- `quantity`：提供的数量
- `price`：特价
- `currency`：货币代码
- 时间戳和审计字段

## 管理界面

此包提供 EasyAdmin 控制器用于管理：

- **特惠机会**：`/admin/order/chance`
- **特惠 SKU**：`/admin/order/sku`

这些界面允许管理员：
- 创建和编辑特惠机会
- 管理关联的 SKU
- 设置有效期
- 跟踪特惠使用情况

## 测试

运行测试套件：

```bash
composer test
```

运行 PHPStan 分析：

```bash
composer analyze
```

## 许可证

此包使用 MIT 许可证发布。详见 [LICENSE](LICENSE) 文件。

## 贡献

欢迎贡献！请确保：

1. 所有测试通过
2. 代码遵循 PSR-12 标准
3. PHPStan 分析通过
4. 如有必要，更新文档

## 支持

如有问题和疑问：
- 在仓库中创建 issue
- 查看文档了解常见使用模式
- 查看现有 issue 寻找解决方案