<?php

namespace App\Service;

use App\Form\WeatherFormType;
use Symfony\Component\HttpFoundation\Request;

class WeatherService {

    public function getWeather($form) {
        $data = $form->getData();
        $data['nom'] = str_replace(' ','%',$data['nom']);
        $url = "https://api-adresse.data.gouv.fr/search/?q=".$data['nom']."&".$data['type'];
        $raw = file_get_contents($url);
        $json = json_decode($raw);
        if (sizeof($json->features) > 0) {
            $city = $json->features[0]->properties->city;
            $latitude = $json->features[0]->geometry->coordinates[1];
            $longitude = $json->features[0]->geometry->coordinates[0];
            $key = "6ca3af0577942eba4a74a9f86a5315fc";
            $url = "https://api.darksky.net/forecast/" . $key . "/" . $latitude . "," . $longitude;
            $json = file_get_contents($url);
            $parsee = json_decode($json, true);
            $temperature = floor($parsee["currently"]["temperature"]);
            $humidity = $parsee["currently"]["humidity"] * 100;
            $windSpeed = round($parsee["currently"]["windSpeed"] * 1.406, 2);
            $icon = $parsee["currently"]["icon"];
            $jour = $this->jourSemaine(date("N"));
            $JourNbANS = date("d/m/Y");
            $temps = $this->tempsFr($icon);
            $icon = $this->icon($icon);
            $degree = ceil(($temperature - 32)*5/9);
        } else {
            return false;
        }
        return array($city, $jour, $JourNbANS, $icon, $degree, $temps, $humidity, $windSpeed);
    }

    function tempsFr($temps) :string {
        switch($temps) {
            case "clear-day":
                return "Ensoleillé";
            break;
            case "clear-night":
                return "nuit claire";
            break;
            case "rain":
                return "pluie";
            break;
            case "snow":
                return "neige";
            break;
            case "sleet":
                return "grésil";
            break;
            case "wind":
                return "vent";
            break;
            case "fog":
                return "brouillard";
            break;
            case "cloudy":
                return "nuageux";
            break;
            case "partly-cloudy-day":
                return "Nuageux dans l'ensemble";
            break;
            case "partly-cloudy-night":
                return "Nuageux dans l'ensemble";
            break;
    
            default:
                return "Unknow time";
        }
    }

    function icon($temps) :string {
        switch($temps) {
            case "clear-day":
                return '<i class="wi wi-day-sunny"></i>';
            break;
            case "clear-night":
                return '<i class="wi wi-night-clear"></i>';
            break;
            case "rain":
                return '<i class="wi wi-rain"></i>';
            break;
            case "snow":
                return '<i class="wi wi-snow"></i>';
            break;
            case "sleet":
                return '<i class="wi wi-sleet"></i>';
            break;
            case "wind":
                return '<i class="wi wi-strong-wind"></i>';
            break;
            case "fog":
                return '<i class="wi wi-fog"></i>';
            break;
            case "cloudy":
                return '<i class="wi wi-cloudy"></i>';
            break;
            case "partly-cloudy-day":
                return '<i class="wi wi-day-cloudy"></i>';
            break;
            case "partly-cloudy-night":
                return '<i class="wi wi-night-alt-cloudy"></i>';
            break;
    
            default:
                return '<i class="wi wi-na"></i>';
        }
    }

    public function jourSemaine($n) :string {
        switch($n) {
            case 1:
                return "LUNDI";
            break;
    
            case 2:
                return "MADRI";
            break;
    
            case 3:
                return "MERCREDI";
            break;
    
            case 4:
                return "JEUDI";
            break;
    
            case 5:
                return "VENDREDI";
            break;
    
            case 6:
                return "SAMEDI";
            break;
    
            case 7:
                return "DIMANCHE";
            break;
    
            default:
                return "Unknow day";
        }
    }
}