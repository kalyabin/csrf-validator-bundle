<?php

namespace Krtv\Bundle\CsrfValidatorBundle\ReaderManager;

use Doctrine\Common\Annotations\Annotation;
use Symfony\Component\Security\Csrf\CsrfToken;

/**
 * Class ReaderManagerInterface
 * @package Krtv\Bundle\CsrfValidatorBundle\ReaderManager
 */
interface ReaderManagerInterface
{
    /**
     * @param \ReflectionMethod $action
     * @return bool|Annotation
     */
    public function supports(\ReflectionMethod $action);

    /**
     * @param \ReflectionClass $controller
     * @return bool|Annotation
     */
    public function supportsClass(\ReflectionClass $controller);

    /**
     * @param Annotation $annotation
     * @return bool
     */
    public function validate(Annotation $annotation);
}
