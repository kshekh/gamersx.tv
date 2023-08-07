<?php

namespace App\Controller;

use App\Entity\MasterSetting;
use App\Service\ThemeSettingValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\{HeaderUtils,
    JsonResponse,
    Request,
    Response,
    ResponseHeaderBag,
    RedirectResponse};
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;

class ThemeSettingAdminController extends CRUDController
{

    public $em;
    private ThemeSettingValidator $themeSettingValidator;

    public function __construct(EntityManagerInterface $em, ThemeSettingValidator $themeSettingValidator)
    {
        $this->em = $em;
        $this->themeSettingValidator = $themeSettingValidator;
    }

    /**
     * @param $id
     */
    public function saveThemeSettingAction(Request $request): JsonResponse
    {
        $data = $request->request->all();
        $header_logo = $request->files->get('form')['header_logo'];
        if ($header_logo) {
            $data['form']['header_logo'] = $header_logo;
        }
        $image_dir = $this->getParameter('app.images');
        $errorMessages = $this->themeSettingValidator->validateData($data['form']);
        if ($errorMessages) {
            $return = ['errors'=> $errorMessages];
            return new JsonResponse($return);
        }

        try {
            foreach ($data['form'] as $field_name => $field_value) {
                $getMasterSetting = $this->em->getRepository(MasterSetting::class)->findOneBy(['name'=>$field_name]);
                if($getMasterSetting != null) {
                    if ($field_name == 'header_logo') {
                        $originalFilename = pathinfo($header_logo->getClientOriginalName(), PATHINFO_FILENAME);
                        $field_value = $originalFilename.'-'.uniqid().'.'.$header_logo->guessExtension();
                        @unlink($image_dir.'/'.$getMasterSetting->getValue());
                        // Move the file to the directory where brochures are stored
                        $header_logo->move(
                            $image_dir,
                            $field_value
                        );
                    }
                    $getMasterSetting->setValue($field_value);
                    $this->em->flush();
                } else {
                    $masterSetting = new MasterSetting();
                    $masterSetting->setName($field_name);
                    if ($field_name == 'header_logo') {
                        $originalFilename = pathinfo($header_logo->getClientOriginalName(), PATHINFO_FILENAME);
                        $field_value = $originalFilename.'-'.uniqid().'.'.$header_logo->guessExtension();
                        $header_logo->move(
                            $image_dir,
                            $field_value
                        );
                    }
                    $masterSetting->setValue($field_value);
                    $this->em->persist($masterSetting);
                    $this->em->flush();
                }
            }
            $return = ['status'=> 1,'msg'=>'Theme setting saved.'];
        }
        catch (\Exception $e) {
            $return = ['status'=> 0,'msg'=> $e->getMessage()];
        }
        catch (FileException $e) {
            $return = ['status'=> 0,'msg'=> $e->getMessage()];
        }

        return new JsonResponse($return);
    }
}
