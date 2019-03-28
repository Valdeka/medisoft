<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PhraseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder ->add('phrase', TextareaType::class);
        if(isset($options['action'])) {
            $builder->setAction($options['action']);
        }

        if(isset($options['method'])) {
            $builder->setMethod($options['method']);
        }
    }
}
