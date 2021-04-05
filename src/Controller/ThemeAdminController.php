<?php

declare(strict_types=1);

namespace App\Controller;

use Sonata\AdminBundle\Controller\CRUDController;

final class ThemeAdminController extends CRUDController{

	/**
	 * Create action.
	 *
	 * @throws AccessDeniedException If access is not granted
	 *
	 * @return Response
	 */
	public function createAction()
	{
		$request = $this->getRequest();

		$this->assertObjectExists($request);

		$this->admin->checkAccess('create');

		// the key used to lookup the template
		$templateKey = 'edit';

		$class = new \ReflectionClass($this->admin->hasActiveSubClass() ? $this->admin->getActiveSubClass() : $this->admin->getClass());

		if ($class->isAbstract()) {
			return $this->renderWithExtraParams(
				'@SonataAdmin/CRUD/select_subclass.html.twig',
				[
					'base_template' => $this->getBaseTemplate(),
					'admin' => $this->admin,
					'action' => 'create',
				],
				null
			);
		}

		$newObject = $this->admin->getNewInstance();

		$preResponse = $this->preCreate($request, $newObject);
		if (null !== $preResponse) {
			return $preResponse;
		}

		$this->admin->setSubject($newObject);

		$form = $this->admin->getForm();

		$form->setData($newObject);
		$form->handleRequest($request);

		if ($form->isSubmitted()) {
			$isFormValid = $form->isValid();

			// persist if the form was valid and if in preview mode the preview was approved
			if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved())) {
				/** @phpstan-var T $submittedObject */
				$submittedObject = $form->getData();
				$this->admin->setSubject($submittedObject);
				$this->admin->checkAccess('create', $submittedObject);

				try {
					$newObject = $this->admin->create($submittedObject);

					if ($this->isXmlHttpRequest()) {
						return $this->handleXmlHttpRequestSuccessResponse($request, $newObject);
					}

					$this->addFlash(
						'sonata_flash_success',
						$this->trans(
							'flash_create_success',
							['%name%' => $this->escapeHtml($this->admin->toString($newObject))],
							'SonataAdminBundle'
						)
					);

					// redirect to edit mode
					return $this->redirectTo($newObject);
				} catch (ModelManagerException $e) {
					$this->handleModelManagerException($e);

					$isFormValid = false;
				}
			}

			// show an error message if the form failed validation
			if (!$isFormValid) {
				if ($this->isXmlHttpRequest() && null !== ($response = $this->handleXmlHttpRequestErrorResponse($request, $form))) {
					return $response;
				}

				$this->addFlash(
					'sonata_flash_error',
					$this->trans(
						'flash_create_error',
						['%name%' => $this->escapeHtml($this->admin->toString($newObject))],
						'SonataAdminBundle'
					)
				);
			} elseif ($this->isPreviewRequested()) {
				// pick the preview template if the form was valid and preview was requested
				$templateKey = 'preview';
				$this->admin->getShow();
			}
		}

		$formView = $form->createView();
		// set the theme for the current Admin Form
		$this->setFormTheme($formView, $this->admin->getFormTheme());

		// NEXT_MAJOR: Remove this line and use commented line below it instead
		$template = $this->admin->getTemplate($templateKey);
		// $template = $this->templateRegistry->getTemplate($templateKey);

		return $this->renderWithExtraParams($template, [
			'action' => 'create',
			'form' => $formView,
			'object' => $newObject,
			'objectId' => null,
		]);
	}

}
