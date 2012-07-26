<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 * (c) Paulo Ribeiro <paulo@duocriativa.com.br>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BFOS\BrasilBundle\Doctrine\Form\Type;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityChoiceList;
use Symfony\Bridge\Doctrine\Form\EventListener\MergeCollectionListener;
use Symfony\Bridge\Doctrine\Form\DataTransformer\EntitiesToArrayTransformer;
use Symfony\Bridge\Doctrine\Form\DataTransformer\EntityToIdTransformer;
use Symfony\Component\Form\AbstractType;

class AjaxEntityType extends AbstractType
{
    protected $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['multiple']) {
            $builder
                ->addEventSubscriber(new MergeCollectionListener())
                ->prependClientTransformer(new EntitiesToArrayTransformer($options['choice_list']))
            ;
        } else {
            $builder->prependClientTransformer(new EntityToIdTransformer($options['choice_list']));
        }
    }

    public function getDefaultOptions(array $options)
    {
        $defaultOptions = array(
            'em'                => null,
            'class'             => null,
            'property'          => null,
            'query_builder'     => null,
            'choices'           => null,
        );

        $options = array_replace($defaultOptions, $options);

        if (!isset($options['choice_list'])) {
            $defaultOptions['choice_list'] = new EntityChoiceList(
                $this->registry->getEntityManager($options['em']),
                $options['class'],
                $options['property'],
                $options['query_builder'],
                $options['choices']
            );
        }

        return $defaultOptions;
    }


    public function getParent()
    {
        return 'bfos_brasil_ajax_choice';
    }

    public function getName()
    {
        return 'bfos_brasil_ajax_entity';
    }
}
