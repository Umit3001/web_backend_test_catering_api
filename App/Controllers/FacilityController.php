<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Plugins\Http\Exceptions\BadRequest;
use App\Services\FacilityService;
use App\Services\TagService;
use App\Services\LocationService;
use App\Models\Facility;
use App\Models\Tags;
use App\Models\Location;
use App\Plugins\Http\Response as Status;
use App\Plugins\Http\Exceptions;


class FacilityController extends BaseController {
    /**
     * Controller function used to test whether the project was set up properly.
     * @return void
     */
    private $facilityService;
    private $tagService;
    private $locationService;

    public function __construct() {

        #creates a new instance of the Services
        $this->facilityService = new FacilityService();
        $this->tagService = new TagService();
        $this->locationService = new LocationService();
    }

    #validate the input fields
    public function validateData($data, $fields) {
        foreach($fields as $field) {
            if(empty($data[$field])) {
                (new Status\BadRequest(['message' => 'Some required fields are empty']))->send();
                exit;
            }
        }
    }

    public function createTags($tagData) {

        $tags = [];

        #loop through the tags and create a tag
        foreach($tagData as $data) {
            #check if tag name is empty
            if(empty($data['name'])) {
                (new Status\BadRequest(['message' => 'Tag name is empty']))->send();
                exit;
            }
            #create a tag
            $tag = new Tags();
            $tag->name = $data['name'];
            $tags[] = $tag;
        }
        return $tags;
    }

    public function createLocation($data) {

        #create a location
        $location = new Location();
        $location->city = $data['city'];
        $location->address = $data['address'];
        $location->zip_code = $data['zip_code'];
        $location->country_code = $data['country_code'];
        $location->phone_number = $data['phone_number'];

        return $this->locationService->createLocation($location);
    }

    public function createTheFacility($data, $location_id) {

        #create a facility
        $facility = new Facility();
        $facility->location_id = $location_id;
        $facility->name = $data['name'];

        return $this->facilityService->createFacility($facility);
    }

    private function connectTagsWithFacility($tags, $facility_id) {

        #loop through the tags and create a tag and connect it with the facility
        foreach($tags as $tag) {
            $tag_id =$this->tagService->createTags($tag);
            $this->tagService->createFacilityTags($facility_id, $tag_id);
        }
    }

    public function updateLocation($data, $location_id) {

        #update the location
        $location = new Location();
        $location->location_id =  $location_id;
        $location->city = $data['city'];
        $location->address = $data['address'];
        $location->zip_code = $data['zip_code'];
        $location->country_code = $data['country_code'];
        $location->phone_number = $data['phone_number'];

        return $this->locationService->updateLocation($location);
    }

    public function updateTheFacility($data, $facility_id, $location_id) {

        #update the facility
        $facility = new Facility();
        $facility->facility_id = $facility_id;
        $facility->location_id = $location_id;
        $facility->name = $data['name'];

        $this->facilityService->updateFacility($facility);
    }

    public function createFacility() {

        #get the data from the request
        $data = json_decode(file_get_contents('php://input'), true);

        #validate the data
        $this->validateData($data, ['city', 'address', 'zip_code', 'country_code', 'phone_number', 'name', 'tags']);

        #create the tags, location and facility
        $tags = $this->createTags($data['tags']);
        $location_id = $this->createLocation($data);
        $facility_id = $this->createTheFacility($data, $location_id);

        #connect the tags with the facility
        $this->connectTagsWithFacility($tags, $facility_id);
       
        #send a response
        (new Status\Created(['message' => 'Facility created successfully']))->send();
    }

    public function readOneFacility() {

        $facility_id = $_GET['facility_id'];

        #check if facility_id value is acceptable
        if(empty($facility_id)) {

            (new Status\BadRequest(['message' => 'Facility ID is empty']))->send();
            return;
        } else if (!is_numeric($facility_id)) {

            (new Status\BadRequest(['message' => 'Enter a valid Facility ID']))->send();
            return;
        }

        #get the facility by facility_id
        $facility = $this->facilityService->getFacilityById($facility_id);

        #check if facility is found
        if($facility === false || $facility === null) {
            (new Status\NotFound(['message' => 'Facility not found']))->send();
            return;
        }

        #send a response
        (new Status\Ok($facility))->send();
    }
    public function updateFacility() {

        #get the data from the request
        $data = json_decode(file_get_contents('php://input'), true);

        #validate the data
        $this->validateData($data, ['facility_id', 'city', 'address', 'zip_code', 'country_code', 'phone_number', 'name', 'tags']);

        #create the tags
        $tags = $this->createTags($data['tags']);
        $facility_id = $data['facility_id'];

        #get the facility by facility_id
        $facility = $this->facilityService->getFacilityById($facility_id);

        #check if facility is found
        if($facility === false || $facility === null) {
            (new Status\NotFound(['message' => 'Facility not found']))->send();
            return;
        }

        #update the location and facility
        $location_id = $this->updateLocation($data, $facility['location_id']);
        $this->updateTheFacility($data, $facility_id, $location_id);

        #delete the tags connected with the facility
        $this->tagService->deleteFacilityTags($facility_id);
        $this->connectTagsWithFacility($tags, $facility_id);

        #send a response
        (new Status\Ok(['message' => 'Facility updated successfully']))->send();
    }

    public function deleteFacility() {

        #get the data from the request
        $data = json_decode(file_get_contents('php://input'), true);

        #check if facility_id value is acceptable
        $facility_id = $data['facility_id'];

        if(empty($facility_id)) {

            (new Status\BadRequest(['message' => 'Facility ID is empty']))->send();
            return;
        } else if (!is_numeric($facility_id)) {

            (new Status\BadRequest(['message' => 'Enter a valid Facility ID']))->send();
            return;
        }

        #get the facility by facility_id
        $facility = $this->facilityService->getFacilityById($facility_id);

        #check if facility is found
        if(empty($facility)) {
            (new Status\NotFound(['message' => 'Facility not found']))->send();
            return;
        }

        #delete the facility and its information
        $this->tagService->deleteFacilityTags($facility_id);
        $this->facilityService->deleteFacilityById($facility_id); 
        $this->locationService->deleteLocation($facility['location_id']);
        
        #send a response
        (new Status\Ok(['message' => 'Facility deleted successfully']))->send();
    }

    public function searchFacility() {

        #get the data from the request
        $search = [
            'tag_name' => $_GET['tag_name'] ?? null,
            'facility_name' => $_GET['facility_name'] ?? null,
            'location_city' => $_GET['location_city'] ?? null,
        ];

        #search for the facility
        $facilities = $this->facilityService->searchFacility($search);

        #check if facility is found
        if(empty($facilities)) {
            (new Status\NotFound(['message' => 'Facility not found']))->send();
            return;
        }

        #send a response
        (new Status\Ok($facilities))->send();
    }

}
