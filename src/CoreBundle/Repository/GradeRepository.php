<?php

namespace CoreBundle\Repository;

use Doctrine\ORM\QueryBuilder;

/**
 * GradeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class GradeRepository extends AbstractRepository
{
    /**
     * @return QueryBuilder
     */
    public function qbFindAll() : QueryBuilder
    {
        return $this->createQueryBuilder('g');
    }

    /**
     * @param QueryBuilder $qb
     * @param string|array $name
     *
     * @return QueryBuilder
     */
    public function filterByName(QueryBuilder $qb, $name) : QueryBuilder
    {
        return $this->filterBy($qb, 'name', $name);
    }

    /**
     * @param QueryBuilder $qb
     * @param int|array $score
     *
     * @return QueryBuilder
     */
    public function filterByScoreMin(QueryBuilder $qb, $score) : QueryBuilder
    {
        $alias = $this->getAlias($qb);
        $expr = $qb->expr();

        $qb->andWhere($expr->gte($alias . 'score', $score));

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param int|array $score
     *
     * @return QueryBuilder
     */
    public function filterByScoreMax(QueryBuilder $qb, $score) : QueryBuilder
    {
        $alias = $this->getAlias($qb);
        $expr = $qb->expr();

        $qb->andWhere($expr->lte($alias . 'score', $score));

        return $qb;
    }
}
