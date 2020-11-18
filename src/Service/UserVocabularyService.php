<?php

namespace App\Service;


use App\Entity\Word;
use App\Entity\User;
use App\Entity\UserVocabulary;
use Doctrine\ORM\EntityManagerInterface;

class UserVocabularyService {

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    /**
     * @param User $user
     * @param Word $word
     * @return UserVocabulary
     */
    public function addWord(User $user, Word $word) {

        if (!$vocabEntry = $this->em->getRepository(UserVocabulary::class)->findOneBy(['word' => $word])) {
            $vocabEntry = new UserVocabulary();
            $vocabEntry->setUser($user);
            $vocabEntry->setWord($word);
            $vocabEntry->setTimeCreated(time());
            $this->em->persist($vocabEntry);
            $this->em->flush();
        }

        return $vocabEntry;
    }

    /**
     * @param User $user
     * @param Word $word
     * @param $correct
     * @return bool
     */
    public function attemptWord(User $user, Word $word, $correct) {
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