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
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Tourze\SpecialOrderBundle\Entity\OfferChance;

#[AdminCrud(routePath: '/order/offer-chance', routeName: 'order_offer_chance')]
final class OrderOfferChanceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OfferChance::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('报价机会')
            ->setEntityLabelInPlural('报价机会列表')
            ->setPageTitle('index', '报价机会管理')
            ->setPageTitle('detail', '报价机会详情')
            ->setPageTitle('edit', '编辑报价机会')
            ->setPageTitle('new', '创建报价机会')
            ->setHelp('index', '管理所有报价机会信息')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'title'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->setMaxLength(9999);
        yield TextField::new('title', '标题');
        yield AssociationField::new('user', '用户');
        yield DateTimeField::new('startTime', '生效时间')
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;
        yield DateTimeField::new('endTime', '失效时间')
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;
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
            ->add(TextFilter::new('title', '标题'))
            ->add(EntityFilter::new('user', '用户'))
            ->add(DateTimeFilter::new('startTime', '生效时间'))
            ->add(DateTimeFilter::new('endTime', '失效时间'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
        ;
    }
}
