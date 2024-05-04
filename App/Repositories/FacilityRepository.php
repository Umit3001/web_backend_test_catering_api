<?php
namespace App\Repositories;
use PDO;
use App\Model\Facility;

class FacilityRepository extends DatabaseConnection
{
    #create a facility and return the facility_id
    public function createFacility($facility) {

        $statement = $this->connection->prepare("INSERT INTO facility (location_id, name) VALUES (:location_id, :name)");
        $statement->bindParam(':location_id', $facility->location_id);
        $statement->bindParam(':name', $facility->name);
        $statement->execute();
        return $this->connection->lastInsertId();
    }

    #get a facility by facility_id
    public function getFacilityById($facility_id) {

        $statement = $this->connection->prepare("
            SELECT facility.*, location.*, GROUP_CONCAT(tags.name) as tag_names
            FROM facility
            INNER JOIN location ON facility.location_id = location.location_id
            INNER JOIN facility_tags ON facility.facility_id = facility_tags.facility_id
            INNER JOIN tags ON facility_tags.tag_id = tags.tag_id
            WHERE facility.facility_id = :facility_id
            GROUP BY facility.facility_id
        ");
        $statement->bindParam(':facility_id', $facility_id);
        $statement->execute();
        $facility = $statement->fetch(PDO::FETCH_ASSOC);

        #returns false if facility is not found
        if ($facility === false) {
            return false;
        }

        return $this->convertTagsToArray($facility);
    }

    #update a facility
    public function updateFacility($facility) {

        $statement = $this->connection->prepare("UPDATE facility SET location_id = :location_id, name = :name WHERE facility_id = :facility_id");
        $statement->bindParam(':location_id', $facility->location_id);
        $statement->bindParam(':name', $facility->name);
        $statement->bindParam(':facility_id', $facility->facility_id);
        $statement->execute();

    }

    #delete a facility by facility_id
    public function deleteFacilityById($facility_id) {

        $statement = $this->connection->prepare("DELETE FROM facility WHERE facility_id = :facility_id");
        $statement->bindParam(':facility_id', $facility_id);
        $statement->execute();
    }

    #search for a facility by facility_name, tag_name or location_city. Includes partial search. 
    #Also you can search for all facilities by not passing any search parameters
    public function searchFacility($search) {

        $query = "
            SELECT facility.*, location.*, tags.tag_names
            FROM facility
            INNER JOIN location ON facility.location_id = location.location_id
            INNER JOIN (
                SELECT facility_tags.facility_id, GROUP_CONCAT(tags.name) as tag_names
                FROM facility_tags
                INNER JOIN tags ON facility_tags.tag_id = tags.tag_id
                GROUP BY facility_tags.facility_id
            ) tags ON facility.facility_id = tags.facility_id
        ";

        #search for facility_name, tag_name or location_city
        if (isset($search['facility_name'])) {
            $query .= " AND facility.name LIKE :facility_name";
        }

        if (isset($search['tag_name'])) {
            $query .= " AND tags.tag_names LIKE :tag_name";
        }

        if (isset($search['location_city'])) {
            $query .= " AND location.city LIKE :location_city";
        }

        $query .= " GROUP BY facility.facility_id";

        $statement = $this->connection->prepare($query);

        #binds the search parameters
        if (isset($search['facility_name'])) {
            #adds % to the beginning and end of the search parameter to search for partial matches
            $facility_name = "%" . $search['facility_name'] . "%";
            $statement->bindParam(':facility_name', $facility_name);
        }

        if (isset($search['tag_name'])) {
            $tag_name = "%" . $search['tag_name'] . "%";
            $statement->bindParam(':tag_name', $tag_name);
        }

        if (isset($search['location_city'])) {
            $location_city = "%" . $search['location_city'] . "%";
            $statement->bindParam(':location_city', $location_city);
        }

        $statement->execute();
        $facility = $statement->fetchAll(PDO::FETCH_ASSOC);

        #return array of facilities
        return array_map([$this, 'convertTagsToArray'], $facility);
    }

    #converts tag_names from a string to array
    private function convertTagsToArray($facility) {
        $facility['tag_names'] = explode(',', $facility['tag_names']);
        return $facility;
    }

}




