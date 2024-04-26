<?php

namespace App\Security\Voter;

use App\Entity\HomeRowItem;
use App\Entity\PartnerRole;
use App\Model\PartneredInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;

class HomeVoter extends Voter
{
    private $security;
    private $roles;

    public function __construct(Security $security, RoleHierarchyInterface $roles)
    {
        $this->security = $security;
        $this->roles = $roles;
    }

    protected function supports($attribute, $subject)
    {
        //  This voter votes on HOME_ROW and HOME_ROW_ITEM permissions
        return $subject instanceof \App\Admin\HomeRowAdmin || $subject instanceof \App\Entity\HomeRow ||
            $subject instanceof \App\Admin\HomeRowItemAdmin || $subject instanceof \App\Entity\HomeRowItem;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        // if the user is anonymous, do not grant access
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        // App Admins can edit anything
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if ($subject instanceof AbstractAdmin) {
            return true;
        } elseif ($subject instanceof PartneredInterface) {
            $roles = $user->getPartnerRoles()->filter(function($pr) use ($subject) {
                return $pr->getPartner() === $subject->getPartner();
            });
        } else {
            return false;
        }

        if (count($roles) !== 1) {
            return false;
        }
        $role = $roles->first()->getRole();

        // You can use the roles hierarchy by defining in security.yml
        // dd($this->roles->getReachableRoles([new Role('ROLE_PARTNER_ADMIN')]));

        // All of the permissions for an Admin object
        // LIST 	view the list of objects
        // VIEW 	view the detail of one object
        // CREATE 	create a new object
        // EDIT 	update an existing object
        // HISTORY 	access to the history of edition of an object
        // DELETE 	delete an existing object
        // EXPORT 	(for the native Sonata export links)
        // ALL

        $editorRoles = ['ROLE_ADMIN_HOME_ROW_EDIT', 'ROLE_ADMIN_HOME_ROW_LIST', 'ROLE_ADMIN_HOME_ROW_ITEM_VIEW',
            'ROLE_ADMIN_HOME_ROW_ITEM_EDIT', 'ROLE_ADMIN_HOME_ROW_ITEM_LIST', 'ROLE_ADMIN_HOME_ROW_ITEM_ITEM_VIEW'];

        $viewerRoles = ['ROLE_ADMIN_HOME_ROW_LIST', 'ROLE_ADMIN_HOME_ROW_ITEM_LIST', 'ROLE_ADMIN_HOME_ROW_VIEW', 'ROLE_ADMIN_HOME_ROW_ITEM_VIEW'];

        if (in_array($attribute, $viewerRoles)) {
            return true;
        }

        if ($role === PartnerRole::ADMIN) {
            return true;
        } elseif ($role === PartnerRole::EDITOR) {
            return in_array($attribute, $editorRoles);
        } elseif ($role === PartnerRole::AD_EDITOR) {
            return in_array($attribute, $editorRoles) && ($subject instanceof HomeRowItem) &&
                ($subject->getItemType() === HomeRowItem::TYPE_LINK);
        }

        return false;

    }
}
