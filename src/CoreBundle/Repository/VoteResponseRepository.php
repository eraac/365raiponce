<?php

namespace CoreBundle\Repository;

use CoreBundle\Entity\Response;
use Doctrine\ORM\QueryBuilder;
use UserBundle\Entity\User;

/**
 * VoteResponseRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class VoteResponseRepository extends AbstractVoteRepository
{
    /**
     * @param Response $response
     * @param User     $user
     *
     * @return bool
     */
    public function userHasVoteFor(Response $response, User $user) : bool
    {
        $qb = $this->createQueryBuilder('v');

        $expr = $qb->expr()->count('v.id');

        $qb->select($expr)
            ->where(
                'v.user = :user',
                'v.response = :response'
            )
            ->setParameters([
                'user' => $user,
                'response' => $response
            ])
        ;

        $query = $qb
            ->getQuery()
            ->useResultCache(true, $this->lifetimeCacheVoteUser, 'user-has-vote-response-' . $response->getId() . '-user-' . $user->getId())
        ;

        return (bool) $query->getSingleScalarResult();
    }

    /**
     * @param Response $response
     *
     * @return int
     */
    public function countVoteForResponse(Response $response) : int
    {
        $qb = $this->createQueryBuilder('v');
        $expr = $qb->expr();

        $qb
            ->select($expr->count('v.id'))
            ->where($expr->eq('v.response', ':response'))
            ->setParameter('response', $response)
        ;

        $query = $qb
            ->getQuery()
            ->useResultCache(true, $this->lifetimeCacheCountVote, 'count-votes-response-' . $response->getId())
        ;

        return $query->getSingleScalarResult();
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     *
     * @return int
     */
    public function countAll(\DateTime $from, \DateTime $to) : int
    {
        $qb = $this->count('v');

        $qb = $this->filterByPeriod($qb, $from, $to);

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param QueryBuilder $qb
     * @param int|array    $emotion
     *
     * @return QueryBuilder
     */
    public function filterByEmotion(QueryBuilder $qb, $emotion) : QueryBuilder
    {
        $alias = 'e';

        $this->safeLeftJoin($qb, 'response', 'res');
        $this->safeLeftJoin($qb, 'remark', 'rem', 'res.');
        $this->safeLeftJoin($qb, 'emotion', $alias, 'rem.');

        return $this->getEqOrIn($qb, $emotion, $alias . '.id', 'emotion');
    }

    /**
     * @param QueryBuilder $qb
     * @param int|array    $theme
     *
     * @return QueryBuilder
     */
    public function filterByTheme(QueryBuilder $qb, $theme) : QueryBuilder
    {
        $alias = 't';

        $this->safeLeftJoin($qb, 'response', 'res');
        $this->safeLeftJoin($qb, 'remark', 'rem', 'res.');
        $this->safeLeftJoin($qb, 'theme', $alias, 'rem.');

        return $this->getEqOrIn($qb, $theme, $alias . '.id', 'theme');
    }

    /**
     * @param QueryBuilder $qb
     * @param int|array    $response
     *
     * @return QueryBuilder
     */
    public function filterByResponse(QueryBuilder $qb, $response) : QueryBuilder
    {
        return $this->filterByWithJoin($qb, 'response', $response, 'res');
    }

    /**
     * @param QueryBuilder $qb
     * @param int|array    $remark
     *
     * @return QueryBuilder
     */
    public function filterByRemark(QueryBuilder $qb, $remark) : QueryBuilder
    {
        $alias = 'rem';

        $this->safeLeftJoin($qb, 'response', 'res');
        $this->safeLeftJoin($qb, 'remark', $alias, 'res.');

        return $this->getEqOrIn($qb, $remark, $alias . '.id', 'remark');
    }

    /**
     * @param QueryBuilder $qb
     * @param int|array    $receiver
     *
     * @return QueryBuilder
     */
    public function filterByReceiver(QueryBuilder $qb, $receiver) : QueryBuilder
    {
        $this->safeLeftJoin($qb, 'response', 'res');
        $this->safeLeftJoin($qb, 'author', 'rec', 'res.');

        return $this->getEqOrIn($qb, $receiver, 'rec.id', 'receiver');
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $orderBy
     * @param string       $order
     *
     * @return QueryBuilder
     */
    public function orderByEmotion(QueryBuilder $qb, string $orderBy, string $order) : QueryBuilder
    {
        $alias = 'e';

        $this->safeLeftJoin($qb, 'response', 'res');
        $this->safeLeftJoin($qb, 'remark', 'rem', 'res.');
        $this->safeLeftJoin($qb, 'emotion', $alias, 'rem.');

        return $this->applyOrder($qb, '.name', $order, $alias);
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $orderBy
     * @param string       $order
     *
     * @return QueryBuilder
     */
    public function orderByTheme(QueryBuilder $qb, string $orderBy, string $order) : QueryBuilder
    {
        $alias = 't';

        $this->safeLeftJoin($qb, 'response', 'res');
        $this->safeLeftJoin($qb, 'remark', 'rem', 'res.');
        $this->safeLeftJoin($qb, 'theme', $alias, 'rem.');

        return $this->applyOrder($qb, '.name', $order, $alias);
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $orderBy
     * @param string       $order
     *
     * @return QueryBuilder
     */
    public function orderByRemark(QueryBuilder $qb, string $orderBy, string $order) : QueryBuilder
    {
        $alias = 'rem';

        $this->safeLeftJoin($qb, 'response', 'res');
        $this->safeLeftJoin($qb, 'remark', $alias, 'res.');

        return $this->applyOrder($qb, '.id', $order, $alias);
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $orderBy
     * @param string       $order
     *
     * @return QueryBuilder
     */
    public function orderByReceiver(QueryBuilder $qb, string $orderBy, string $order) : QueryBuilder
    {
        $alias = 'rec';

        $this->safeLeftJoin($qb, 'response', 'res');
        $this->safeLeftJoin($qb, 'author', $alias, 'res.');

        return $this->applyOrder($qb, '.username', $order, $alias);
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $groupBy
     *
     * @return QueryBuilder
     */
    public function groupByEmotion(QueryBuilder $qb, string $groupBy) : QueryBuilder
    {
        $this->safeLeftJoin($qb, 'response', 'res');
        $this->safeLeftJoin($qb, 'remark', 'rem', 'res.');
        $this->safeLeftJoin($qb, 'emotion', 'e', 'rem.');

        return $this->groupBy($qb, 'e.id', 'emotion_id');
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $groupBy
     *
     * @return QueryBuilder
     */
    public function groupByTheme(QueryBuilder $qb, string $groupBy) : QueryBuilder
    {
        $this->safeLeftJoin($qb, 'response', 'res');
        $this->safeLeftJoin($qb, 'remark', 'rem', 'res.');
        $this->safeLeftJoin($qb, 'theme', 't', 'rem.');

        return $this->groupBy($qb, 't.id', 'theme_id');
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $groupBy
     *
     * @return QueryBuilder
     */
    public function groupByReceiver(QueryBuilder $qb, string $groupBy) : QueryBuilder
    {
        $alias = 'rec';

        $this->safeLeftJoin($qb, 'response', 'res');
        $this->safeLeftJoin($qb, 'author', $alias, 'res.');

        return $this->groupBy($qb, $alias . '.id', 'receiver_id');
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $groupBy
     *
     * @return QueryBuilder
     */
    public function groupByRemark(QueryBuilder $qb, string $groupBy) : QueryBuilder
    {
        $this->safeLeftJoin($qb, 'response', 'res');
        $this->safeLeftJoin($qb, 'remark', 'rem', 'res.');

        return $this->groupBy($qb, 'rem.id', 'remark_id');
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $groupBy
     *
     * @return QueryBuilder
     */
    public function groupByResponse(QueryBuilder $qb, string $groupBy) : QueryBuilder
    {
        $this->safeLeftJoin($qb, 'response', 'res');

        return $this->groupBy($qb, 'res.id', 'response_id');
    }
}
