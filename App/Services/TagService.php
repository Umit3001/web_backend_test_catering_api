<?php
namespace App\Services;
use App\Repositories\TagRepository;

class TagService {
    private $tagRepository;

    public function __construct()
    {
        $this->tagRepository = new TagRepository();
    }

    public function createTags($tags) {
        return $this->tagRepository->createTags($tags);
    }

    public function createFacilityTags($facility_id, $tag_id) {
        $this->tagRepository->createFacilityTags($facility_id, $tag_id);
    }

    public function deleteFacilityTags($facility_id) {
        $this->tagRepository->deleteFacilityTags($facility_id);
    }





}