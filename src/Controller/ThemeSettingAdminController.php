<?php

namespace App\Controller;

use App\Entity\MasterSetting;
use App\Entity\MasterTheme;
use App\Service\AwsS3Service;
use App\Service\ThemeSettingValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\{File\UploadedFile,
    HeaderUtils,
    JsonResponse,
    Request,
    Response,
    ResponseHeaderBag,
    RedirectResponse};
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\FormView;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Vich\UploaderBundle\Handler\UploadHandler;
use Vich\UploaderBundle\Storage\StorageInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

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
     * List action.
     *
     * @throws AccessDeniedException If access is not granted
     *
     * @return Response
     */
    public function listAction()
    {
        $request = $this->getRequest();

        $this->assertObjectExists($request);

        $this->admin->checkAccess('list');

        $preResponse = $this->preList($request);
        if (null !== $preResponse) {
            return $preResponse;
        }

        if ($listMode = $request->get('_list_mode')) {
            $this->admin->setListMode($listMode);
        }

        $datagrid = $this->admin->getDatagrid();
        $formView = $datagrid->getForm()->createView();

        // set the theme for the current Admin Form
        $this->setFormTheme($formView, $this->admin->getFilterTheme());

        // NEXT_MAJOR: Remove this line and use commented line below it instead
        $template = $this->admin->getTemplate('list');
        // $template = $this->templateRegistry->getTemplate('list');

        $getMasterTheme = $this->em->getRepository(MasterTheme::class)->findAll();
        if(empty($getMasterTheme)) {
            $masterTheme = new MasterTheme();
            $masterTheme->setName('Default');
            $masterTheme->setStatus(1);
            $this->em->persist($masterTheme);
            $this->em->flush();
            $getMasterTheme = $this->em->getRepository(MasterTheme::class)->findAll();
        }

        return $this->renderWithExtraParams($template, [
            'action' => 'list',
            'form' => $formView,
            'masterThemes' => $getMasterTheme,
            'datagrid' => $datagrid,
            'csrf_token' => $this->getCsrfToken('sonata.batch'),
            'export_formats' => $this->has('sonata.admin.admin_exporter.do-not-use') ?
                $this->get('sonata.admin.admin_exporter.do-not-use')->getAvailableFormats($this->admin) :
                $this->admin->getExportFormats(),
        ]);
    }

    /**
     * Sets the admin form theme to form view. Used for compatibility between Symfony versions.
     */
    private function setFormTheme(FormView $formView, ?array $theme = null): void
    {
        $twig = $this->get('twig');

        $twig->getRuntime(FormRenderer::class)->setTheme($formView, $theme);
    }

    /**
     * @param $id
     */
    public function saveThemeSettingAction(Request $request,AwsS3Service $awsS3Service): JsonResponse
    {
        $data = $request->request->all();
        $image_dir = $this->getParameter('app.images');
        $header_logo = $request->files->get('form')['header_logo'];
        $header_background = $request->files->get('form')['header_background'];
        $body_background = $request->files->get('form')['body_background'];
        $footer_background = $request->files->get('form')['footer_background'];
        $bucketName = $this->getParameter('app.aws_s3_bucket_name');
        $s3_custom_url = $this->getParameter('app.aws_s3_custom.uri_prefix');

        $action_type = $data['action_type']??'';
        $theme_id = $data['theme']??'';
        $selectedTheme = null;
        if($action_type == 'apply') {
            // Curruent theme apply
            $getMasterTheme = $this->em->getRepository(MasterTheme::class)->findAll();
            foreach ($getMasterTheme as $getMasterSettingData) {
                if($getMasterSettingData->getId() == $theme_id) {
                    $getMasterSettingData->setStatus(1);
                    $selectedTheme = $getMasterSettingData;
                } else {
                    $getMasterSettingData->setStatus(0);
                }
                $this->em->flush();
            }
        } else if($action_type == 'without_preset') {

            // get default theme data for assignment
            $selectedTheme = $this->em->getRepository(MasterTheme::class)->findOneBy(['name'=> 'Default']);

            if(empty($selectedTheme)) {
                $masterTheme = new MasterTheme();
                $masterTheme->setName('Default');
                $masterTheme->setStatus(1);
                $this->em->persist($masterTheme);
                $this->em->flush();
                $selectedTheme =  $masterTheme;
            }

            // getting selected theme data in assigning it to default theme
            $getMasterSetting = $this->em->getRepository(MasterSetting::class)->findBy(['master_theme' => $theme_id]);
            foreach ($getMasterSetting as $getMasterSettingData) {
                if (
                    $getMasterSettingData->getName() == 'header_background' ||
                    $getMasterSettingData->getName() == 'body_background' ||
                    $getMasterSettingData->getName() == 'footer_background' ||
                    $getMasterSettingData->getName() == 'header_logo'
                ) {
                    $getMasterSetting = $this->em->getRepository(MasterSetting::class)->findOneBy(['name'=>$getMasterSettingData->getName(),'master_theme'=>$selectedTheme->getId()]);
                    if($getMasterSetting != null) {
                        $getMasterSetting->setValue($getMasterSettingData->getValue());
                        $this->em->flush();
                    } else {
                        $masterSetting = new MasterSetting();
                        $masterSetting->setName($getMasterSettingData->getName());
                        $masterSetting->setValue($getMasterSettingData->getValue());
                        $masterSetting->setMasterTheme($selectedTheme);
                        $this->em->persist($masterSetting);
                        $this->em->flush();
                    }
                }
            }

            $getMasterTheme = $this->em->getRepository(MasterTheme::class)->findAll();
            foreach ($getMasterTheme as $getMasterSettingData) {
                if($getMasterSettingData->getId() == $selectedTheme->getId()) {
                    $getMasterSettingData->setStatus(1);
                    $theme_id = $selectedTheme->getId();
                } else {
                    $getMasterSettingData->setStatus(0);
                }
                $this->em->flush();
            }
        }

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

        $theme_name = $data['theme_name']??'';
        $return_errors = [];
        if($action_type == 'save_theme') {
            if ($theme_name != '') {
                $getMasterTheme = $this->em->getRepository(MasterTheme::class)->findOneBy(['name' => $theme_name]);
                if ($getMasterTheme != null) {
                    $return_errors['themename'] = 'The theme name is already taken.';
                } else {
                    $masterTheme = new MasterTheme();
                    $masterTheme->setName($theme_name);
                    $masterTheme->setStatus(0);
                    $this->em->persist($masterTheme);
                    $this->em->flush();
                    $selectedTheme =  $masterTheme;
                }
            } else {
                $return_errors['themename'] = 'The theme name is required.';
            }
        }

        $errorMessages = $this->themeSettingValidator->validateData($data['form']);
        if ($errorMessages || $return_errors) {
            if(!empty($return_errors)) {
                $return = ['errors'=> array_merge($return_errors,$errorMessages)];
            } else {
                $return = ['errors'=> $errorMessages];
            }
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
                $getMasterSetting = $this->em->getRepository(MasterSetting::class)->findOneBy(['name'=>$field_name,'master_theme'=>$theme_id]);
                if($getMasterSetting != null) {
                    if ($field_name == 'header_logo' || $field_name == 'header_background' || $field_name == 'body_background' || $field_name == 'footer_background') {
                        $getfile = $request->files->get('form')[$field_name];
                        $originalFilename = pathinfo($getfile->getClientOriginalName(), PATHINFO_FILENAME);
                        $field_value = $originalFilename.'-'.uniqid().'.'.$getfile->guessExtension();

                        $file_header_background_temp_src = $getfile->getPathname(); // The S3 object key
                        // Upload the file to S3
                        $return = $awsS3Service->uploadFile($bucketName, $field_value, $file_header_background_temp_src);
                    }
                    if ($field_name == 'font_family') {
                        $field_value = @implode(',',$field_value);
                    }
                    $getMasterSetting->setValue($field_value);
                    $getMasterSetting->setMasterTheme($selectedTheme);
                    $this->em->flush();
                } else {
                    $masterSetting = new MasterSetting();
                    $masterSetting->setName($field_name);
                    if ($field_name == 'header_logo' || $field_name == 'header_background' || $field_name == 'body_background' || $field_name == 'footer_background') {
                        $getfile = $request->files->get('form')[$field_name];
                        $originalFilename = pathinfo($getfile->getClientOriginalName(), PATHINFO_FILENAME);
                        $field_value = $originalFilename.'-'.uniqid().'.'.$getfile->guessExtension();

                        $file_header_background_temp_src = $getfile->getPathname(); // The S3 object key
                        // Upload the file to S3
                        $return = $awsS3Service->uploadFile($bucketName, $field_value, $file_header_background_temp_src);

                    }
                    if ($field_name == 'font_family') {
                        $field_value = @implode(',',$field_value);
                    }
                    $masterSetting->setValue($field_value);
                    $masterSetting->setMasterTheme($selectedTheme);
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
                    $setting_value = $s3_custom_url.'/'.$setting_value;
                }
                $return_data[$setting_name] = $setting_value;
            }
            if($action_type == 'save_theme') {
                $return_data['theme_data'] = ['id' => $selectedTheme->getId(),'name' => $selectedTheme->getName()];
            }
            $msg = '';
            if($action_type == 'apply') {
                $msg = '"'.$selectedTheme->getName().'" theme has been applied successfully.';
            } else if($action_type == 'save_theme') {
                $msg = '"'.$selectedTheme->getName().'" theme saved successfully. To apply in front, select and click on apply button.';
            } else {
                $msg = 'Default theme settings has been applied successfully.';
            }
            $return = ['status'=> 1,'msg'=>$msg,'data'=>$return_data];
        }
        catch (\Exception $e) {
            $return = ['status'=> 0,'msg'=> $e->getMessage()];
        }
        catch (FileException $e) {
            $return = ['status'=> 0,'msg'=> $e->getMessage()];
        }

        return new JsonResponse($return);
    }

    public function saveThemeAction(Request $request): JsonResponse
    {
        $data = $request->request->all();
        $theme_name = $data['theme_name']??'';
        $return_data = [];
        if($theme_name != '') {
            $getMasterTheme = $this->em->getRepository(MasterTheme::class)->findOneBy(['name' => $theme_name]);
            if($getMasterTheme != null) {
                $return = ['errors'=> ['themename'=>'The theme name is already taken.']];
            } else {
                $masterTheme = new MasterTheme();
                $masterTheme->setName($theme_name);
                $masterTheme->setStatus(0);
                $this->em->persist($masterTheme);
                $this->em->flush();
                $return_data = ['id' => $masterTheme->getId(),'name' => $masterTheme->getName()];
                $return = ['status'=> 1,'msg'=> '"'.$masterTheme->getName().'" theme saved successfully. To apply in front, select and click on apply button.','data'=>$return_data];
            }
        } else {
            $return = ['errors'=> ['themename'=>'The theme name is required.']];
        }
        return new JsonResponse($return);
    }

    public function getThemeSettingAction(Request $request): JsonResponse
    {
        $data = $request->request->all();
        $s3_custom_url = $this->getParameter('app.aws_s3_custom.uri_prefix');
        $theme_id = $data['theme_id']??'';
        // Fetch setting data and pass it to response
        $return_data = [];
        $getMasterSetting = $this->em->getRepository(MasterSetting::class)->findBy(['master_theme'=>$theme_id]);
        foreach ($getMasterSetting as $getMasterSettingData) {
            $setting_name = $getMasterSettingData->getName();
            $setting_value = $getMasterSettingData->getValue();
            if ($setting_name == 'header_logo' ||
                $setting_name == 'header_background' ||
                $setting_name == 'body_background' ||
                $setting_name == 'footer_background'
            ) {
                $setting_value = $s3_custom_url.'/'.$setting_value;
            }
            $return_data[$setting_name] = $setting_value;
        }
        $return = ['status'=> 1,'msg'=>'Success.','data'=>$return_data];
        return new JsonResponse($return);
    }
}
