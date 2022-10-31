<?php

declare(strict_types=1);

namespace App\Model\Salon\Service;

use App\Model\Salon\ReadModel\SalonCard;
use App\Model\Salon\ReadModel\SalonFetcher;

class YandexMapConvertor
{
    /**
     * @var SalonCard[]
     */
    private $salons;

    public function __construct(SalonFetcher $salons)
    {
        $this->salons = $salons->getSalons();
    }

    public function render()
    {
        $response["type"] = "FeatureCollection";
        $response["features"] = [];

        foreach ($this->salons as $salon) {
            $element = [];
            $element["type"] = "Feature";
            $element["id"] = $salon->id;
            $element["geometry"] = ["type" => "Point", "coordinates" => [(double)$salon->lat, (double)$salon->lon]];
            $element["properties"] = [
                "balloonContentBody" => $this->renderCardInfo($salon),
                "balloonContentHeader" => $salon->name,
                "card" => $salon
            ];
            $response["features"][] = $element;
        }

        return $response;
    }

    public function renderCardInfo(SalonCard $salon): string
    {
        return 'Тип салона: <b>' . $salon->type.'</b>';
    }

}
