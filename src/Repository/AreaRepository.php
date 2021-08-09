<?php

namespace App\Repository;

use App\Entity\Area;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Area|null find($id, $lockMode = null, $lockVersion = null)
 * @method Area|null findOneBy(array $criteria, array $orderBy = null)
 * @method Area[]    findAll()
 * @method Area[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AreaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Area::class);
    }

    public function getSelectedAreaName($areas)
    {
        $areasData = $this->findBy(['id' => $areas]);
        $areas = [];
        foreach ($areasData as $area) {
            $areas[$area->getId()] = $area->getName();
        }
        $area = implode(',', $areas);
        return $area;
    }
}
