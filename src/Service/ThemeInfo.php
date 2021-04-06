<?php

namespace App\Service;

use App\Entity\Theme;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;

class ThemeInfo
{
    private $helper;
    private $em;

    public function __construct(UploaderHelper $helper, EntityManagerInterface $em)
    {
        $this->helper = $helper;
        $this->em = $em;
    }

    public function getThemeInfo($id, $itemType) {
        $theme = $this->em->getRepository(Theme::class)
            ->findOneBy([
                'itemType' => $itemType,
                'twitchId' => $id
            ]);

        if (!$theme) {
            return [];
        } else {
            return [
                'banner' => $this->helper->asset($theme, 'bannerImageFile'),
                'artBg' => $this->helper->asset($theme, 'artBackgroundFile'),
                'embedBg' => $this->helper->asset($theme, 'embedBackgroundFile'),
            ];
        }

        return $themeInfo;
    }
}
