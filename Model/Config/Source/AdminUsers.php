<?php
namespace DevScripts\Bypass2FA\Model\Config\Source;

use Magento\User\Model\ResourceModel\User\CollectionFactory;
use Magento\Framework\Option\ArrayInterface;

class AdminUsers implements ArrayInterface
{
    private CollectionFactory $collectionFactory;

    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    public function toOptionArray()
    {
        $options = [];
        $collection = $this->collectionFactory->create();
        foreach ($collection as $user) {
            $options[] = [
                'value' => $user->getUsername(),
                'label' => $user->getUsername()
            ];
        }
        return $options;
    }
}