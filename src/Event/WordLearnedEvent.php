<?php

namespace App\Event;

use App\Entity\UserVocabulary;
use Symfony\Contracts\EventDispatcher\Event;

class WordLearnedEvent extends Event
{
    public const NAME = 'word.learned';

    protected $vocabItem;

    public function __construct(UserVocabulary $vocabItem)
    {
        $this->vocabItem = $vocabItem;
    }

    public function getVocabItem()
    {
        return $this->vocabItem;
    }
}
