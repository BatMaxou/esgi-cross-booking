<?php

namespace App\Form;

use App\Entity\Reservation\TeamReservation;
use App\Entity\Team;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamReservationType extends AbstractType
{
    public const PREFIX = 'team_reservation_';

    private int $nextBuildPrefix = 0;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        ++$this->nextBuildPrefix;

        $label = 'Réserver pour ';
        if (isset($options['team'])) {
            $team = $options['team'];
            if ($team instanceof Team) {
                $label .= $team->getName();
            }
        }

        $builder
            ->add('submit', SubmitType::class, [
                'label' => $label,
                'attr' => [
                    'class' => 'btn info',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TeamReservation::class,
            'team' => null,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return sprintf('%s%d', self::PREFIX, $this->nextBuildPrefix);
    }
}
