<?php
namespace App\Repositories;

class LocationRepository extends DatabaseConnection {

    #create a location and return the location_id
    public function createLocation($location) {
        $statement = $this->connection->prepare("INSERT INTO location (city, address, zip_code, country_code, phone_number) VALUES (:city, :address, :zip_code, :country_code, :phone_number)");
        $statement->bindParam(':city', $location->city);
        $statement->bindParam(':address', $location->address);
        $statement->bindParam(':zip_code', $location->zip_code);
        $statement->bindParam(':country_code', $location->country_code);
        $statement->bindParam(':phone_number', $location->phone_number);
        $statement->execute();
        return $this->connection->lastInsertId();
    }

    #get a location by location_id and return the location_id
    public function updateLocation($location) {
        $statement = $this->connection->prepare("UPDATE location SET city = :city, address = :address, zip_code = :zip_code, country_code = :country_code, phone_number = :phone_number WHERE location_id = :location_id");
        $statement->bindParam(':city', $location->city);
        $statement->bindParam(':address', $location->address);
        $statement->bindParam(':zip_code', $location->zip_code);
        $statement->bindParam(':country_code', $location->country_code);
        $statement->bindParam(':phone_number', $location->phone_number);
        $statement->bindParam(':location_id', $location->location_id);
        $statement->execute();
        return $location->location_id;
    }

    public function deleteLocation($location_id) {
        $statement = $this->connection->prepare("DELETE FROM location WHERE location_id = :location_id");
        $statement->bindParam(':location_id', $location_id);
        $statement->execute();
    }

}