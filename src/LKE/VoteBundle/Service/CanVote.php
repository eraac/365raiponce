<?php

namespace LKE\VoteBundle\Service;

use LKE\RemarkBundle\Entity\Response;
use LKE\UserBundle\Entity\User;

class CanVote
{
    const MAX_VOTE_PER_DAY = 20;

    private $doctrine;

    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function canVote(Response $response, User $user)
    {
        return !$this->hasAlreadyVote($response, $user) && !$this->hasUseAllVoteToday($user);
    }

    private function hasAlreadyVote(Response $response, User $user)
    {
        return $this->doctrine->getRepository("LKERemarkBundle:Response")->userHasVote($response, $user);
    }

    private function hasUseAllVoteToday(User $user)
    {
        $countVoteToday = $this->doctrine->getRepository("LKEVoteBundle:Vote")->countVoteForUser($user);

        return $countVoteToday >= self::MAX_VOTE_PER_DAY;
    }
}