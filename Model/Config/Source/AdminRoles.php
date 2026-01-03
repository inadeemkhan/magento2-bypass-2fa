<?php
namespace DevScripts\Bypass2FA\Model\Config\Source;

use Magento\Authorization\Model\ResourceModel\Role\CollectionFactory;
use Magento\Framework\Option\ArrayInterface;

class AdminRoles implements ArrayInterface
{
    private $collectionFactory;

    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    public function toOptionArray()
    {
        $options = [];
        foreach ($this->collectionFactory->create() as $role) {
            if ($role->getRoleType() === 'G') {
                $options[] = ['value' => $role->getId(), 'label' => $role->getRoleName()];
            }
        }
        return $options;
    }
}