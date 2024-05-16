<?php

namespace App\Traits;

use App\Entity\ErrorLog;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

trait ErrorLogTrait
{
    private $entityManager;

    /**
     * @required
     * @param EntityManagerInterface $entityManager
     */
    public function setEntityManager(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @param ConstraintViolationListInterface $validationResult
     *
     * @return array<int|string, (mixed)>
     */
    public function log_error($error_message, $status_code, $error_type, $container_id = null)
    {
        $error_log = new ErrorLog;
        $error_log->setErrorMessage($error_message);
        $error_log->setErrorType($error_type);
        $error_log->setCreatedAt(new DateTime());
        $error_log->setStatusCode($status_code);
        $error_log->setContainerId($container_id);
        $this->entityManager->persist($error_log);
        $this->entityManager->flush();
    }
}
