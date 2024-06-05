<?php

namespace App\Service;

use App\Entity\Theme;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;

class ThemeInfo
{
    private UploaderHelper $helper;
    private EntityManagerInterface $em;

    public function __construct(UploaderHelper $helper, EntityManagerInterface $em)
    {
        $this->helper = $helper;
        $this->em = $em;
    }

    public function getThemeInfo($id, $itemType): array
    {
        $theme = $this->em->getRepository(Theme::class)
            ->findOneBy([
                'itemType' => $itemType,
                'topicId' => $id
            ]);

        if (!$theme) {
            return [];
        } else {
            return [
                'banner' => $this->helper->asset($theme, 'bannerImageFile'),
                'embedBg' => $this->helper->asset($theme, 'embedBackgroundFile'),
                'customArt' => $this->helper->asset($theme, 'customArtFile'),
                'artBg' => $this->helper->asset($theme, 'artBackgroundFile'),
            ];
        }

//        return $themeInfo;
    }
}
