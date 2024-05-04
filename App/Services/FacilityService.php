<?php
namespace App\Services;

use App\Repositories\FacilityRepository;
use Facility;
class FacilityService
{
    private $facilityRepository;

    public function __construct()
    {
        $this->facilityRepository = new FacilityRepository();
    }

    public function createFacility($facility) {
        return $this->facilityRepository->createFacility($facility);
    }

    public function getFacilityById($facility_id) {
        return $this->facilityRepository->getFacilityById($facility_id);
    }

    public function updateFacility($facility) {
        $this->facilityRepository->updateFacility($facility);
    }

    public function deleteFacilityById($facility_id) {
        $this->facilityRepository->deleteFacilityById($facility_id);
    }

    public function searchFacility($search) {
        return $this->facilityRepository->searchFacility($search);
    }
    

}