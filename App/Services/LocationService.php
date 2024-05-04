<?php
namespace App\Services;
use App\Repositories\LocationRepository;

class LocationService {
    private $locationRepository;

    public function __construct()
    {
        $this->locationRepository = new LocationRepository();
    }

    public function createLocation($location) {
        return $this->locationRepository->createLocation($location);
    }

    public function updateLocation($location) {
        return $this->locationRepository->updateLocation($location);
    }

    public function deleteLocation($location_id) {
        $this->locationRepository->deleteLocation($location_id);
    }
}