<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Mutuelle;
use App\Entity\Ville;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Patient
 *
 * @author Asus
 */
class PatientType extends AbstractType
{
    //put your code here
    /**
     * configureOptions.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     * @SuppressWarnings(PHPMD.ElseExpression)
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //'widget' => 'single_text',
        $builder
            ->setMethod('POST')
                  ->add('id', HiddenType::class)
                  ->add('nom', TextType::class, ['attr' => ['placeholder'=>'Nom']])
                  ->add('prenom', TextType::class, ['attr' => ['placeholder'=>'Prénom']])
                  ->add('cin', TextType::class, ['attr' => ['placeholder'=>'Cin']])
                  ->add('adresse', TextType::class, ['attr' => ['placeholder'=>'Adresse']])
                  ->add('sexe', ChoiceType::class, ['attr' => ['placeholder'=>'Sexe'],'choices'=>['Masculin'=>'Masculin','Féminin'=>'Féminin']])
                  ->add('tel', TextType::class, ['attr' => ['placeholder'=>'Téléphone']])
                  ->add('date_naiss', DateType::class, ['widget' => 'single_text','html5' => false,'attr' => ['placeholder'=>'Date de naissance']])
                  ->add('situation', ChoiceType::class, ['attr' => ['data-placeholder'=>'Situation'],'choices' => [
                    'Marié' => "Marié",
                    'Célibataire' => "Célibataire"]])
                  ->add('ville', EntityType::class, ['class' => Ville::class,'choice_label' => 'ville'])
                  ->add('mutuelle', EntityType::class, ['class' => Mutuelle::class,'choice_label' => 'nom'])
                  ->add('date_resultat', TextType::class, ['mapped' => false])
                  ->add('rc', TextareaType::class, ['mapped' => false])
                  ->add('age', TextType::class, ['mapped' => false])
                  ->add('save', SubmitType::class);
    }
}
