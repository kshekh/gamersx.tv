<?php

namespace App\Security\Voter;

use App\Entity\PartnerRole;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class HomeVoter extends Voter
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        return (in_array($attribute, ['ROW_EDIT', 'ITEM_EDIT', 'AD_EDIT']) &&
            $subject instanceof \App\Mode\PartneredInterface);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        $roles = $user->getPartnerRoles()->filter(function($pr) {
            $pr->getPartner() === $subject->getPartner();
        });

        if (count($roles) !== 1) {
            return false;
        }

        $role = $roles->first()->getRole();

        $allRoles = [PartnerRole::ROLE_AD_EDITOR, PartnerRole::ROLE_EDITOR, PartnerRole::ROLE_ADMIN];

        $roleIndex = array_search($role, $allRoles);

        switch ($attribute) {
        case 'ROW_EDIT':
            return $roleIndex >= 2;
            break;
        case 'ITEM_EDIT':
            return $roleIndex >= 1;
            break;
        case 'AD_EDIT':
            return $roleIndex >= 0;
            break;
        }

        return false;
    }
}
