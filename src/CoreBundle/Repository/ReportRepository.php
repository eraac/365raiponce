<?php

namespace CoreBundle\Repository;

use Doctrine\ORM\QueryBuilder;

/**
 * ReportRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ReportRepository extends AbstractRepository
{
    /**
     * @return QueryBuilder
     */
    public function qbFindAll() : QueryBuilder
    {
        return $this
            ->createQueryBuilder('r')
        ;
    }

    /**
     * @param QueryBuilder $qb
     * @param int|string   $timestamp
     *
     * @return QueryBuilder
     */
    public function filterByReportedBefore(QueryBuilder $qb, $timestamp) : QueryBuilder
    {
        $alias = $this->getAlias($qb);

        $qb
            ->andWhere($alias . 'reportedAt < :reported_before')
            ->setParameter('reported_before', $this->dateFromTimestamp($timestamp))
        ;

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param int|string   $timestamp
     *
     * @return QueryBuilder
     */
    public function filterByReportedAfter(QueryBuilder $qb, $timestamp) : QueryBuilder
    {
        $alias = $this->getAlias($qb);

        $qb
            ->andWhere($alias . 'reportedAt > :reported_after')
            ->setParameter('reported_after', $this->dateFromTimestamp($timestamp))
        ;

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param int|string|array $response
     *
     * @return QueryBuilder
     */
    public function filterByResponse(QueryBuilder $qb, $response) : QueryBuilder
    {
        return $this->filterByWithJoin($qb, 'response', $response, 're');
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $orderBy
     * @param string       $order
     *
     * @return QueryBuilder
     */
    public function orderByReportedAt(QueryBuilder $qb, string $orderBy, string $order) : QueryBuilder
    {
        return $this->applyOrder($qb, 'reportedAt', $order);
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $orderBy
     * @param string       $order
     *
     * @return QueryBuilder
     */
    public function orderByResponse(QueryBuilder $qb, string $orderBy, string $order) : QueryBuilder
    {
        return $this->applyOrder($qb, 'response', $order);
    }
}