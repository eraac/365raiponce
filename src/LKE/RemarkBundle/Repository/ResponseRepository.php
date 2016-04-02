<?php

namespace LKE\RemarkBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ResponseRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ResponseRepository extends EntityRepository
{
    public function getResponsesByRemark($idRemark, $idUser, $limit, $page)
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.remark = :idRemark')
            ->andWhere('r.postedAt is not null OR r.author = :idAuthor')
            ->setParameter('idRemark', $idRemark)
            ->setParameter('idAuthor', $idUser)
            ->setFirstResult($page * $limit)
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function getResponsesByRemarkComplet($idRemark, $idUser, $limit, $page)
    {
        $qb = getQueryResponsesByRemarkComplet($idRemark, $idUser, $limit, $page)
                ->setFirstResult($page * $limit)
                ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function getQueryResponsesByRemarkComplet($idRemark, $idUser)
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.votes', 'v')
            ->join("r.author", "u")
            ->addSelect('u')
            ->addSelect('v')
            ->where('r.remark = :idRemark')
            ->andWhere('r.postedAt is not null OR r.author = :idAuthor')
            ->orderBy('r.postedAt')
            ->addOrderBy('r.id')
            ->setParameter('idRemark', $idRemark)
            ->setParameter('idAuthor', $idUser);

        return $qb;
    }

    public function getMyResponses($idUser, $limit, $page)
    {
        $qb = $this->queryMyResponses($idUser)
            ->setFirstResult($page * $limit)
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function queryMyResponses($idUser)
    {
        $qb = $this->createQueryBuilder('r')
            ->andWhere('r.author = :idAuthor')
            ->setParameter('idAuthor', $idUser);

        return $qb;
    }

    public function getUnpublishedResponses($limit, $page)
    {
        $qb = $this->queryUnpublishedResponses()
                ->setFirstResult($page * $limit)
                ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function queryUnpublishedResponses()
    {
        $qb = $this->createQueryBuilder('r')
            ->join('r.remark', 're')
            ->addSelect('re')
            ->andWhere('r.postedAt is null');

        return $qb;
    }

    public function getResponseAndVotes($id)
    {
        $qb = $this->createQueryBuilder('r')
                ->leftJoin('r.votes', 'v')
                ->addSelect('v')
                ->where('r.id = :id')
                ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function userHasVote($response, $user)
    {
        $qb = $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->join('r.votes', 'v')
            ->where('r = :response')
            ->andWhere('v.user = :user')
            ->setParameters(array(
                "response" => $response,
                "user" => $user
            ));

        return (bool) $qb->getQuery()->getSingleScalarResult();
    }

    public function countVote($response)
    {
        $qb = $this->createQueryBuilder('r')
            ->select('COUNT(v.id)')
            ->join('r.votes', 'v')
            ->where('r = :response')
            ->setParameter("response", $response);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
