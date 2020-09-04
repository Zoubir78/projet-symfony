<?php

namespace App\Security\Voter;

use App\Entity\Event;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Voter déterminant les droits de suppression d'un événement
 */
class EventVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
         // Le voter ne doit intervenir que s'il s'agit de l'attribut (similaire à un role) 
        // et si le sujet (ce sur quoi on vérifie le droit) est une instance de Event
        return in_array($attribute, ['EVENT_DELETE'])
            && $subject instanceof Event;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var Event $subject */

        $user = $token->getUser();
        // Utilisateur non connecté = pas le droit de supprimer
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Administrateur = autorisé à supprimer tout les événements
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        // Utilisateur auteur de l'événement = autorisé à supprimer son propre événement
        if ($user === $subject->getAuthor()) {
            return true;
        }

        // Aucun des cas précédents = pas le droit de supprimer
        return false;
    }
}
