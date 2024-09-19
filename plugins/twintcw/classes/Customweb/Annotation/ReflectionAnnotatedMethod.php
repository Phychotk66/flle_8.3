<?php

require_once 'Customweb/Annotation/IAnnotationReflector.php';
require_once 'Customweb/Annotation/AnnotationsBuilder.php';
require_once 'Customweb/Annotation/ReflectionAnnotatedClass.php';


class Customweb_Annotation_ReflectionAnnotatedMethod extends ReflectionMethod implements Customweb_Annotation_IAnnotationReflector
{
    private
        $annotations;

    public function __construct($class, $name)
    {
        parent::__construct($class, $name);
        
        $this->annotations = $this->createAnnotationBuilder()->build($this);
    }

    #[\ReturnTypeWillChange]
    public function hasAnnotation($class)
    {
        return $this->annotations->hasAnnotation($class);
    }

    #[\ReturnTypeWillChange]
    public function getAnnotation($annotation)
    {
        return $this->annotations->getAnnotation($annotation);
    }

    #[\ReturnTypeWillChange]
    public function getAnnotations()
    {
        return $this->annotations->getAnnotations();
    }

    #[\ReturnTypeWillChange]
    public function getAllAnnotations($restriction = false)
    {
        return $this->annotations->getAllAnnotations($restriction);
    }

    #[\ReturnTypeWillChange]
    public function getDeclaringClass()
    {
        $class = parent::getDeclaringClass();
        
        return new Customweb_Annotation_ReflectionAnnotatedClass($class->getName());
    }

    #[\ReturnTypeWillChange]
    public function getDeclaringClassName() {
    	return parent::getDeclaringClass()->getName();
    }

    #[\ReturnTypeWillChange]
    protected function createAnnotationBuilder()
    {
        return new Customweb_Annotation_AnnotationsBuilder();
    }
}