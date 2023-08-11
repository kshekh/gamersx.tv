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
        $header_background = $request->files->get('form')['header_background'];
        $body_background = $request->files->get('form')['body_background'];
        $footer_background = $request->files->get('form')['footer_background'];
        if ($header_logo) {
            $data['form']['header_logo'] = $header_logo;
        }
        if ($header_background) {
            $data['form']['header_background'] = $header_background;
        }
        if ($body_background) {
            $data['form']['body_background'] = $body_background;
        }
        if ($footer_background) {
            $data['form']['footer_background'] = $footer_background;
        }
        $data['form']['font_family'] = $data['form']['font_family']??'';
        $image_dir = $this->getParameter('app.images');
        $errorMessages = $this->themeSettingValidator->validateData($data['form']);
        if ($errorMessages) {
            $return = ['errors'=> $errorMessages];
            return new JsonResponse($return);
        }

        $remote_font_url = $data['form']['remote_font_url']??'';
        $font_type = $data['form']['font_type']??'';
        if($remote_font_url != '' && $font_type == 'remote') {
            $parsedUrl = parse_url($remote_font_url);
            if(isset($parsedUrl['query'])) {
                $queryString =  $parsedUrl['query'];
                parse_str($queryString, $params);
                $fontNameExp = explode(':',$params['family']);
                if(isset($fontNameExp[0])) {
                    $fontName = urldecode($fontNameExp[0]);
                    $data['form']['remote_font_name'] = $fontName;
                }
            }
        }

        try {
            foreach ($data['form'] as $field_name => $field_value) {
                $getMasterSetting = $this->em->getRepository(MasterSetting::class)->findOneBy(['name'=>$field_name]);
                if($getMasterSetting != null) {
                    if ($field_name == 'header_logo' || $field_name == 'header_background' || $field_name == 'body_background' || $field_name == 'footer_background') {
                        $getfile = $request->files->get('form')[$field_name];
                        $originalFilename = pathinfo($getfile->getClientOriginalName(), PATHINFO_FILENAME);
                        $field_value = $originalFilename.'-'.uniqid().'.'.$getfile->guessExtension();
                        @unlink($image_dir.'/'.$getMasterSetting->getValue());
                        // Move the file to the directory where images are stored
                        $getfile->move(
                            $image_dir,
                            $field_value
                        );
                    }
                    if ($field_name == 'font_family') {
                        $field_value = @implode(',',$field_value);
                    }
                    $getMasterSetting->setValue($field_value);
                    $this->em->flush();
                } else {
                    $masterSetting = new MasterSetting();
                    $masterSetting->setName($field_name);
                    if ($field_name == 'header_logo' || $field_name == 'header_background' || $field_name == 'body_background' || $field_name == 'footer_background') {
                        $getfile = $request->files->get('form')[$field_name];
                        $originalFilename = pathinfo($getfile->getClientOriginalName(), PATHINFO_FILENAME);
                        $field_value = $originalFilename.'-'.uniqid().'.'.$getfile->guessExtension();
                        // Move the file to the directory where images are stored
                        $getfile->move(
                            $image_dir,
                            $field_value
                        );
                    }
                    if ($field_name == 'font_family') {
                        $field_value = @implode(',',$field_value);
                    }
                    $masterSetting->setValue($field_value);
                    $this->em->persist($masterSetting);
                    $this->em->flush();
                }
            }

            // Fetch setting data and pass it to response
            $return_data = [];
            $getMasterSetting = $this->em->getRepository(MasterSetting::class)->findAll();
            foreach ($getMasterSetting as $getMasterSettingData) {
                $setting_name = $getMasterSettingData->getName();
                $setting_value = $getMasterSettingData->getValue();
                if ($setting_name == 'header_logo' ||
                    $setting_name == 'header_background' ||
                    $setting_name == 'body_background' ||
                    $setting_name == 'footer_background'
                ) {
                    $setting_value = '/images/'.$setting_value;
                }
                $return_data[$setting_name] = $setting_value;
            }
            $return = ['status'=> 1,'msg'=>'Theme setting saved.','data'=>$return_data];
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
