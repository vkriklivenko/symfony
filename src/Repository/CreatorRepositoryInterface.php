<?php


namespace App\Repository;


use App\Entity\Creator;

interface CreatorRepositoryInterface
{
    /**
     * @return Creator[]
     */
    public function getAllCreator(): array;

    /**
     * @param int $categoryId
     * @return Creator
     */
    public function getOneCreator(int $categoryId): object;

    /**
     * @param Creator $creator
     * @return Creator
     */
    public function setCreateCreator(Creator $creator): object;

    /**
     * @param Creator $creator
     * @return Creator
     */
    public function setUpdateCreator(Creator $creator): object;

    /**
     * @param Creator $creator
     */
    public function setDeleteCreator(Creator $creator);
}