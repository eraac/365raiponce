<?php

namespace CoreBundle\Repository;

use CoreBundle\Entity\Remark;
use CoreBundle\Service\KeyBuilder;
use Doctrine\ORM\QueryBuilder;
use UserBundle\Entity\User;

/**
 * ResponseRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ResponseRepository extends AbstractPostedRepository
{
    /**
     * @var int
     */
    private $lifetimeCacheCountResponse;


    /**
     * @param int $lifetimeCacheCountResponse
     */
    public function setLifetimeCacheCountResponse(int $lifetimeCacheCountResponse)
    {
        $this->lifetimeCacheCountResponse = $lifetimeCacheCountResponse;
    }

    /**
     * @param Remark $remark
     *
     * @return int
     */
    public function countResponsePublishedForRemark(Remark $remark) : int
    {
        $qb = $this->createQueryBuilder('r');
        $expr = $qb->expr();

        $qb
            ->select($expr->count('r.id'))
            ->where(
                $expr->eq('r.remark', ':remark'),
                $expr->isNotNull('r.postedAt')
            )
            ->setParameter('remark', $remark)
        ;

        $query = $qb
            ->getQuery()
            ->useResultCache(true, $this->lifetimeCacheCountResponse, KeyBuilder::keyCountPublishedResponseForRemark($remark))
        ;

        return $query->getSingleScalarResult();
    }

    /**
     * @param Remark $remark
     *
     * @return int
     */
    public function countResponseUnpublishedForRemark(Remark $remark) : int
    {
        $qb = $this->createQueryBuilder('r');
        $expr = $qb->expr();

        $qb
            ->select($expr->count('r.id'))
            ->where(
                $expr->eq('r.remark', ':remark'),
                $expr->isNull('r.postedAt')
            )
            ->setParameter('remark', $remark)
        ;

        $query = $qb
            ->getQuery()
            ->useResultCache(true, $this->lifetimeCacheCountResponse, KeyBuilder::keyCountUnpublishedResponseForRemark($remark))
        ;

        return $query->getSingleScalarResult();
    }

    /**
     * @return QueryBuilder
     */
    public function qbFindAll() : QueryBuilder
    {
        return $this
            ->createQueryBuilder('r')
            ->leftJoin('r.author', 'a')
            ->addSelect('a');
    }

    /**
     * @return QueryBuilder
     */
    public function qbFindAllPublished() : QueryBuilder
    {
        return $this
            ->qbFindAll()
            ->where('r.postedAt IS NOT NULL');
    }

    /**
     * @param Remark $remark
     *
     * @return QueryBuilder
     */
    public function qbFindAllPublishedInRemark(Remark $remark) : QueryBuilder
    {
        return $this->qbFindAll()
            ->andWhere('r.remark = :remark')
            ->setParameter('remark', $remark);
    }

    /**
     * @return QueryBuilder
     */
    public function qbFindAllUnpublished() : QueryBuilder
    {
        return $this
            ->qbFindAll()
            ->where('r.postedAt IS NULL');
    }

    /**
     * @param User $user
     *
     * @return QueryBuilder
     */
    public function qbFindAllByUser(User $user) : QueryBuilder
    {
        return $this
            ->qbFindAll()
            ->where('r.author = :user')
            ->setParameter('user', $user);
    }

    /**
     * @param User $user
     *
     * @return QueryBuilder
     */
    public function qbFindAllUnpublishedByUser(User $user) : QueryBuilder
    {
        return $this
            ->qbFindAll()
            ->where('r.author = :user', 'r.postedAt IS NULL')
            ->setParameter('user', $user);
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

        $this->safeLeftJoin($qb, 'remark', 're');
        $this->safeLeftJoin($qb, 'emotion', $alias, 're.');

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

        $this->safeLeftJoin($qb, 'remark', 're');
        $this->safeLeftJoin($qb, 'theme', $alias, 're.');

        return $this->getEqOrIn($qb, $theme, $alias . '.id', 'theme');
    }

    /**
     * @param QueryBuilder $qb
     * @param              $remark
     *
     * @return QueryBuilder
     */
    public function filterByRemark(QueryBuilder $qb, $remark) : QueryBuilder
    {
        return $this->filterByWithJoin($qb, 'remark', $remark, 're');
    }

    /**
     * @param QueryBuilder $qb
     * @param int|array    $author
     *
     * @return QueryBuilder
     */
    public function filterByAuthor(QueryBuilder $qb, $author) : QueryBuilder
    {
        return $this->filterByWithJoin($qb, 'author', $author, 'a');
    }

    /**
     * @param QueryBuilder $qb
     * @param              $scaleEmotion
     *
     * @return QueryBuilder
     */
    public function filterByScaleEmotion(QueryBuilder $qb, $scaleEmotion) : QueryBuilder
    {
        $alias = 're';

        $this->safeLeftJoin($qb, 'remark', $alias);

        return $this->getEqOrIn($qb, $scaleEmotion, $alias . '.scaleEmotion', 'scale_emotion');
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $orderBy
     * @param string       $order
     *
     * @return QueryBuilder
     */
    public function orderByPostedAt(QueryBuilder $qb, string $orderBy, string $order) : QueryBuilder
    {
        return $this->applyOrder($qb, 'postedAt', $order);
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
        $alias = 're';

        $this->safeLeftJoin($qb, 'remark', $alias);

        return $this->applyOrder($qb, '.id', $order, $alias);
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $orderBy
     * @param string       $order
     *
     * @return QueryBuilder
     */
    public function orderByAuthor(QueryBuilder $qb, string $orderBy, string $order) : QueryBuilder
    {
        $alias = 'a';

        $this->safeLeftJoin($qb, 'author', $alias);

        return $this->applyOrder($qb, '.username', $order, $alias);
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

        $this->safeLeftJoin($qb, 'remark', 're');
        $this->safeLeftJoin($qb, 'emotion', $alias, 're.');

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

        $this->safeLeftJoin($qb, 'remark', 're');
        $this->safeLeftJoin($qb, 'theme', $alias, 're.');

        return $this->applyOrder($qb, '.name', $order, $alias);
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $orderBy
     * @param string       $order
     *
     * @return QueryBuilder
     */
    public function orderByScaleEmotion(QueryBuilder $qb, string $orderBy, string $order) : QueryBuilder
    {
        $alias = 're';

        $this->safeLeftJoin($qb, 'remark', $alias);

        return $this->applyOrder($qb, '.scaleEmotion', $order, $alias);
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     *
     * @return int
     */
    public function countPublished(\DateTime $from, \DateTime $to) : int
    {
        $qb = $this->count('r');

        $qb
            ->where($qb->expr()->isNotNull('r.postedAt'))
            ->andWhere('r.postedAt > :from', 'r.postedAt < :to')
            ->setParameters([
                'from' => $from,
                'to' => $to
            ])
        ;

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     *
     * @return int
     */
    public function countUnpublished(\DateTime $from, \DateTime $to) : int
    {
        $qb = $this->count('r');

        $this
            ->filterByPeriod($qb, $from, $to)
            ->andWhere($qb->expr()->isNull('r.postedAt'))
        ;

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $groupBy
     *
     * @return QueryBuilder
     */
    public function groupByEmotion(QueryBuilder $qb, string $groupBy) : QueryBuilder
    {
        $this->safeLeftJoin($qb, 'remark', 're');
        $this->safeLeftJoin($qb, 'emotion', 'e', 're.');

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
        $this->safeLeftJoin($qb, 'remark', 're');
        $this->safeLeftJoin($qb, 'theme', 't', 're.');

        return $this->groupBy($qb, 't.id', 'theme_id');
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $groupBy
     *
     * @return QueryBuilder
     */
    public function groupByRemark(QueryBuilder $qb, string $groupBy) : QueryBuilder
    {
        $this->safeLeftJoin($qb, 'remark', 're');

        return $this->groupBy($qb, 're.id', 'remark_id');
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $groupBy
     *
     * @return QueryBuilder
     */
    public function groupByAuthor(QueryBuilder $qb, string $groupBy) : QueryBuilder
    {
        $this->safeLeftJoin($qb, 'author', 'a');

        return $this->groupBy($qb, 'a.id', 'author_id');
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $groupBy
     *
     * @return QueryBuilder
     */
    public function groupByScaleEmotion(QueryBuilder $qb, string $groupBy) : QueryBuilder
    {
        $this->safeLeftJoin($qb, 'remark', 're');

        return $this->groupBy($qb, 're.scaleEmotion', 'scale_emotion');
    }
}
