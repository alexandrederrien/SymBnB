<?php

namespace App\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

/**
 * Class Paginator
 * @package App\Service
 *
 * Classe de pagination qui extrait toute notion de calcul et de récupération de données de nos controllers
 * Elle nécéssite après instanciation qu'on lui passe l'entité sur laquelle on souhaite travailler
 */
class Paginator
{
    private $entityClass;
    private $limit = 10;
    private $currentPage = 1;
    private $manager;
    private $twig;
    private $route;
    private $templatePath;

    public function __construct(ObjectManager $manager, Environment $twig, RequestStack $requestStack, $templatePath)
    {
        $this->route            = $requestStack->getCurrentRequest()->attributes->get('_route');
        $this->manager          = $manager;
        $this->twig             = $twig;
        $this->templatePath     = $templatePath;
    }

    public function display()
    {
        $this->twig->display($this->templatePath, [
            'page' => $this->currentPage,
            'nbPages' => $this->getNbPages(),
            'route' => $this->route
        ]);
    }

    public function getData()
    {
        if(empty($this->entityClass)) {
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle vous voulez paginer");
        }
        $offset = $this->limit * ($this->currentPage - 1);

        $repo = $this->manager->getRepository($this->entityClass);
        $data = $repo->findBy([], [], $this->limit, $offset);

        return $data;
    }

    public function getNbPages()
    {
        if(empty($this->entityClass)) {
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle vous voulez paginer");
        }

        $repo = $this->manager->getRepository($this->entityClass);
        $total = count($repo->findAll());
        $nbPages = ceil($total / $this->limit);

        return $nbPages;
    }

    /**
     * @return mixed
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * @param mixed $entityClass
     * @return Paginator
     */
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;
        return $this;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     * @return Paginator
     */
    public function setLimit(int $limit): Paginator
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @param int $currentPage
     * @return Paginator
     */
    public function setCurrentPage(int $currentPage): Paginator
    {
        $this->currentPage = $currentPage;
        return $this;
    }

    /**
     * @return ObjectManager|int
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @param ObjectManager|int $manager
     * @return Paginator
     */
    public function setManager($manager)
    {
        $this->manager = $manager;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    /**
     * @param mixed $templatePath
     * @return Paginator
     */
    public function setTemplatePath($templatePath)
    {
        $this->templatePath = $templatePath;
        return $this;
    }
}