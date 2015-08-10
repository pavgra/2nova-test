<?php namespace App\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class UserRepository extends EntityRepository
{

    public function getRegisteredUserList()
    {
        $qb = $this->createQueryBuilder('u')
            ->select('u')
            ->orderBy('u.created_at', 'DESC')
            ->getQuery()
            ->useResultCache(true)
            ->setResultCacheId('registered_users_list')
            //->setResultCacheLifetime(120)
            ->getResult(Query::HYDRATE_ARRAY);

        return $qb;
    }
}