<?php

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TaskVoter extends Voter {
    const DELETE_TASK = 'delete_task';

	public function __construct(private Security $security) {}

    protected function supports(string $attribute, $subject): bool {
        if (!in_array($attribute, [self::DELETE_TASK])) {
            return false;
        }

        if (!$subject instanceof Task) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Task $task */
        $task = $subject;

        switch ($attribute) {
            case self::DELETE_TASK:
                return $this->canDelete($task, $user);
        }
    }

    private function canDelete(Task $task, User $user): bool {
		if (in_array("ROLE_ANONYMOUS", $task->getUser()->getRoles()) && $this->security->isGranted("ROLE_ADMIN")) {
            return true;
        }

		return $task->getUser() === $user;
    }
}
