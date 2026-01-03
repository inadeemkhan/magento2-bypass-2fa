<?php
namespace DevScripts\Bypass2FA\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Backend\Model\Auth\Session as AdminSession;

class BypassTfaPlugin
{
    const XML_PATH_ENABLE = 'devscripts_bypass2fa/general/enable';
    const XML_PATH_USERS  = 'devscripts_bypass2fa/general/users';

    private ScopeConfigInterface $scopeConfig;
    private AdminSession $adminSession;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        AdminSession $adminSession
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->adminSession = $adminSession;
    }

    public function afterIsGranted($subject, $result)
    {
        if (!$this->scopeConfig->isSetFlag(self::XML_PATH_ENABLE)) {
            return $result;
        }

        $user = $this->adminSession->getUser();
        if (!$user) {
            return $result;
        }

        $allowedUsers = $this->scopeConfig->getValue(self::XML_PATH_USERS);
        if (!$allowedUsers) {
            return $result;
        }

        $allowed = array_map('trim', explode(',', $allowedUsers));
        if (in_array($user->getUsername(), $allowed, true)) {
            return true;
        }

        return $result;
    }
}