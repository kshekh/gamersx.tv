<?php

namespace App\Service;

use App\Entity\Theme;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

class ThemeImageService
{
    private $params;
    private $theme;
    private $filesystem;

    public function __construct(ParameterBagInterface $params)
    {
        $this->filesystem = new Filesystem();
        $this->params = $params;
    }

    public function setTheme(Theme $theme)
    {
        $this->theme = $theme;
    }

    public function getImageUrl(string $imageType)
    {
        return $this->getImagePath('url', $imageType);
    }

    public function getImageFilePath(string $imageType)
    {
        return $this->getImagePath('filePath', $imageType);
    }

    private function getImagePath(string $pathType, string $imageType)
    {
        $themeRoot;
        if ($pathType === 'url') {
            $themeRoot = $this->params->get('app.theme_url');
        } elseif ($pathType === 'filePath') {
            $themeRoot = $this->params->get('app.theme_path');
        } else {
            return null;
        }

        $itemPath = $themeRoot.$this->theme->getItemType().'/'.$this->theme->getTwitchId().'/';

        switch ($imageType) {
        case Theme::IMAGE_TYPE_BANNER:
            if ($this->theme->getBannerImage()) {
                return $itemPath.$this->theme->getBannerImage();
            }
        case Theme::IMAGE_TYPE_EMBED_BACKGROUND:
            if ($this->theme->getEmbedBackground()) {
                return $itemPath.$this->theme->getEmbedBackground();
            }
        case Theme::IMAGE_TYPE_ART_BACKGROUND:
            if ($this->theme->getArtBackground()) {
                return $itemPath.$this->theme->getArtBackground();
            }
        default:
            return null;
        }
    }

    /**
     * Manages the copying of the file to the relevant place on the server
     */
    public function upload(string $imageType, ?UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        // $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $themeRoot = $this->params->get('app.theme_path');
            $themeRoot = $themeRoot.$this->theme->getItemType().'/'.$this->theme->getTwitchId().'/';
            $file->move($themeRoot, $originalFilename);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $originalFilename;
    }
}

