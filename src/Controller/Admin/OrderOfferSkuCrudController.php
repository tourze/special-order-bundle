<?php

namespace Tourze\SpecialOrderBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Tourze\SpecialOrderBundle\Entity\OfferSku;

#[AdminCrud(routePath: '/order/offer-sku', routeName: 'order_offer_sku')]
final class OrderOfferSkuCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OfferSku::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('机会SKU')
            ->setEntityLabelInPlural('机会SKU列表')
            ->setPageTitle('index', '机会SKU管理')
            ->setPageTitle('detail', '机会SKU详情')
            ->setPageTitle('edit', '编辑机会SKU')
            ->setPageTitle('new', '创建机会SKU')
            ->setHelp('index', '管理所有机会SKU信息')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'price', 'currency'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->setMaxLength(9999);
        yield AssociationField::new('chance', '机会');
        yield AssociationField::new('sku', 'SKU');
        yield IntegerField::new('quantity', '数量');
        yield MoneyField::new('price', '价格')->setCurrency('CNY');
        yield TextField::new('currency', '币种');
        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;
        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('chance', '机会'))
            ->add(EntityFilter::new('sku', 'SKU'))
            ->add(NumericFilter::new('quantity', '数量'))
            ->add(NumericFilter::new('price', '价格'))
            ->add(TextFilter::new('currency', '币种'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
        ;
    }
}
