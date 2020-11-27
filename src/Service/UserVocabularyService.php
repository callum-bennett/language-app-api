<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\UserVocabulary;
use App\Entity\Word;
use App\Event\WordLearnedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UserVocabularyService
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    private $dispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param User $user
     * @param Word $word
     * @return UserVocabulary
     */
    public function addWord(User $user, Word $word)
    {
        if (!$vocabEntry = $this->em->getRepository(UserVocabulary::class)->findOneBy(['word' => $word])) {
            $vocabEntry = new UserVocabulary();
            $vocabEntry->setUser($user);
            $vocabEntry->setWord($word);
            $vocabEntry->setTimeCreated(time());
            $this->em->persist($vocabEntry);
            $this->em->flush();

            $event = new WordLearnedEvent($vocabEntry);
            $this->dispatcher->dispatch($event, $event::NAME);
        }

        return $vocabEntry;
    }

    /**
     * @param User $user
     * @param Word $word
     * @param $correct
     * @return bool
     */
    public function attemptWord(User $user, Word $word, $correct)
    {
        if (!$vocabEntry = $this->em->getRepository(UserVocabulary::class)->findOneBy(['user' => $user, 'word' => $word])) {
            // @ todo throw exception
            return false;
        }

        if ($correct) {
            $existingCount = $vocabEntry->getCorrect();
            $vocabEntry->setCorrect(++$existingCount);
        } else {
            $existingCount = $vocabEntry->getWrong();
            $vocabEntry->setWrong(++$existingCount);
        }
        $vocabEntry->setLastAttempt(time());

        $this->em->persist($vocabEntry);
        $this->em->flush();

        return true;
    }
}
