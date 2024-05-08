<?php
namespace App\Repositories;
use App\Plugins\Di\Injectable;
use PDO;

class TagRepository extends Injectable {

    private $db;

    public function __construct()
    {
        $this->db = $this->getDI()->getShared('db'); 
    }

    public function createTags($tags) {

        // Check if tag already exists
        $statement = $this->db->getConnection()->prepare("SELECT * FROM tags WHERE name = :name");
        $statement->bindParam(':name', $tags->name);
        $statement->execute();
        $existingTag = $statement->fetch(PDO::FETCH_ASSOC);

        if ($existingTag) {

            // If tag exists return its id
            return $existingTag['tag_id'];
        } else {

            // If tag does not exists create it and return its id
            $statement = $this->db->getConnection()->prepare("INSERT INTO tags (name) VALUES (:name)");
            $statement->bindParam(':name', $tags->name);
            $statement->execute();
            return $this->connection->lastInsertId();
        }
    }

    
    public function createFacilityTags($facility_id, $tag_id) {
        $statement = $this->db->getConnection()->prepare("INSERT INTO facility_tags (facility_id, tag_id) VALUES (:facility_id, :tag_id)");
        $statement->bindParam(':facility_id', $facility_id);
        $statement->bindParam(':tag_id', $tag_id);
        $statement->execute(); 
    }

    public function deleteFacilityTags($facility_id) {
        $statement = $this->db->getConnection()->prepare("DELETE FROM facility_tags WHERE facility_id = :facility_id");
        $statement->bindParam(':facility_id', $facility_id);
        $statement->execute();
    }
}