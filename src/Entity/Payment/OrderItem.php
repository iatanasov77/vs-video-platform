<?php namespace App\Entity\Payment;

use Doctrine\ORM\Mapping as ORM;
use Vankosoft\PaymentBundle\Model\OrderItem as OrderItemBase;
use Vankosoft\CatalogBundle\Model\Interfaces\PayableObjectAwareInterface;
use Vankosoft\CatalogBundle\Model\Traits\PayableObjectAwareEntity;

/**
 * @Doctrine\Common\Annotations\Annotation\IgnoreAnnotation( "ORM\MappedSuperclass" )
 * @Doctrine\Common\Annotations\Annotation\IgnoreAnnotation("ORM\Column")
 */
#[ORM\Entity]
#[ORM\Table(name: "VSPAY_OrderItem")]
class OrderItem extends OrderItemBase implements PayableObjectAwareInterface
{
    use PayableObjectAwareEntity;
}