<?php


namespace App\Model;


use App\Model\EditableRolesBuilder;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Sonata\AdminBundle\Admin\Pool;
use Symfony\Contracts\Translation\TranslatorInterface;

class CustomEditableRolesBuilder extends EditableRolesBuilder
{
    public function __construct(
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker,
        Pool $pool,
        array $rolesHierarchy = []
    ) {
        parent::__construct($tokenStorage, $authorizationChecker, $pool, $rolesHierarchy);
    }

    public function getRoles($domain = false, $expanded = true)
    {
        $roles = [];

        if (!$this->tokenStorage->getToken()) {
            return $roles;
        }

        $this->iterateAdminRoles(function ($role, $isMaster) use ($domain, &$roles): void {
            if ($isMaster) {
                $roles[$role] = $this->translateRole($role, $domain);
            }
        });

        $isMaster = $this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN');

        foreach ($this->rolesHierarchy as $name => $rolesHierarchy) {
            if ($this->authorizationChecker->isGranted($name) || $isMaster) {
                $roles[$name] = $this->translateRole($name, $domain);
                if ($expanded) {
                    $result = array_map([$this, 'translateRole'], $rolesHierarchy, array_fill(0, \count($rolesHierarchy), $domain));
                    $roles[$name] .= ': '.implode(', ', $result);
                }
                foreach ($rolesHierarchy as $role) {
                    if (!isset($roles[$role])) {
                        $roles[$role] = $this->translateRole($role, $domain);
                    }
                }
            }
        }

        return $roles;
    }

    private function iterateAdminRoles(callable $func): void
    {
        // get roles from the Admin classes
        foreach ($this->pool->getAdminServiceIds() as $id) {
            try {
                $admin = $this->pool->getInstance($id);
            } catch (\Exception $e) {
                continue;
            }

            $isMaster = $admin->isGranted('MASTER');
            $securityHandler = $admin->getSecurityHandler();
            // TODO get the base role from the admin or security handler
            $baseRole = $securityHandler->getBaseRole($admin);

            if ('' === $baseRole) { // the security handler related to the admin does not provide a valid string
                continue;
            }

            foreach ($admin->getSecurityInformation() as $role => $permissions) {
                $role = sprintf($baseRole, $role);
                \call_user_func($func, $role, $isMaster, $permissions);
            }
        }
    }

    /*
  * @param string $role
  * @param string|bool|null $domain
  *
  * @return string
  */
    private function translateRole($role, $domain)
    {
        // translation domain is false, do not translate it,
        // null is fallback to message domain
        if (false === $domain || !isset($this->translator)) {
            return $role;
        }

        return $this->translator->trans($role, [], $domain);
    }
}