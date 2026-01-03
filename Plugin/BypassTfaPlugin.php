<?php
namespace DevScripts\Bypass2FA\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Backend\Model\Auth\Session as AdminSession;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;

class BypassTfaPlugin
{
    const XML_PATH_ENABLE = 'devscripts_bypass2fa/general/enable';
    const XML_PATH_USERS  = 'devscripts_bypass2fa/general/users';
    const XML_PATH_ROLES  = 'devscripts_bypass2fa/general/roles';
    const XML_PATH_IPS    = 'devscripts_bypass2fa/general/ips';

    private $scopeConfig;
    private $adminSession;
    private $remoteAddress;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        AdminSession $adminSession,
        RemoteAddress $remoteAddress
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->adminSession = $adminSession;
        $this->remoteAddress = $remoteAddress;
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

        $ip = $this->remoteAddress->getRemoteAddress();
        $allowedIps = $this->scopeConfig->getValue(self::XML_PATH_IPS);
        if ($allowedIps && in_array($ip, array_map('trim', explode(',', $allowedIps)), true)) {
            return true;
        }

        $allowedUsers = $this->scopeConfig->getValue(self::XML_PATH_USERS);
        if ($allowedUsers && in_array($user->getUsername(), array_map('trim', explode(',', $allowedUsers)), true)) {
            return true;
        }

        $allowedRoles = $this->scopeConfig->getValue(self::XML_PATH_ROLES);
        if ($allowedRoles) {
            foreach (explode(',', $allowedRoles) as $roleId) {
                if (in_array((int)$roleId, $user->getRoles(), true)) {
                    return true;
                }
            }
        }

        return $result;
    }
}