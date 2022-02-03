<?php


namespace App\Rapture\Repositories;

use App\Rapture\Repositories\BaseRepository;

/**
 * Class FlatsRepository
 * @package App\Rapture\Repositories
 * Репо для работы с квартирами
 */
class FlatsRepository extends BaseRepository
{
    protected string $TABLE = "map_flats";

    /**
     * FlatsRepository constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }


    /** Получение массива схем по ID квартир
     * @param array $arID
     * @return array
     */
    public function getSchemas(array $arID = []): array
    {
        $arPlansID = array_keys($this->DB->Get_ID("SELECT `type_id` FROM `map_flats` WHERE `id` IN (" . implode(",", $arID) . ")", "type_id"));
        $arPlans = $this->getList(["table" => "map_flat_types", "id" => $arPlansID, "order" => ["by" => "area"]]);

        $arPlans = array_map(function ($item) {
            $item['area_schema'] = $this->FILES->getPath($item['area_schema']);
            return $item;
        }, $arPlans);

        return $arPlans;
    }

    /**
     * Получение список владельцев
     * @param int $flat_id
     * @return array
     */
    function scopeOwnerList(int $flat_id): array
    {
        $list = $this->DB->Get_ID("SELECT * FROM `map_owners` WHERE `flat_id` = {$flat_id} AND activated = 1");
        return $list;
    }

    /** Получить список квартир для владельца
     * @param int $id
     * @return array
     */
    function getListByOwner(int $id): array
    {
        $list = $this->DB->Get("SELECT f.* FROM `map_flats` as f, `map_owners` as o WHERE o.user_id = '{$id}' AND o.flat_id = f.id AND o.activated = 1 AND o.old = 0");
        return $list;
    }
}