<?php
namespace App\Rapture\Database;

use App\Rapture\Database\DatabaseGenerator;

/**
 * Class Selectable
 * @package App\Rapture\Database
 * Класс-прослойка для работы с базой данных
 */
class Selectable
{
    protected \App\Rapture\Database\DatabaseGenerator $DB;
    protected string $TABLE;

    /**
     * Selectable constructor.
     */
    public function __construct()
    {
        $this->DB = new DatabaseGenerator();
    }

    /** Функция пост-обработки данных
     * @param array $arData массив данных
     * @return array
     */
    public function getConvert(array $arData): array
    {
        return $arData;
    }

    /**
     * Функция для получения списка записей из базы данных
     * @param array $arParams массив с параметрами запроса
     * @return array
     */
    public function getList(array $arParams = []): array
    {
        if (!empty($arParams['cache']['chr'])) {
            $result = $this->DB->One("result", "sql_cache", ["filter" => ["unique_hash" => $arParams['cache']['chr']]]);
            if (!empty($result)) return json_decode($result, true);
        }

        $table_select = $arParams['table'] ?? $this->TABLE;

        if (!empty($arParams['fast'])) {
            $arData = $this->DB->getExtend($table_select, $arParams);
        } else {
            $arData = $this->getConvert(
                $this->DB->getExtend($table_select, $arParams)
            );
        }

        if (!empty($arParams['cache']['chr'])) {
            if (empty($arParams['cache']['lifetime'])) $arParams['cache']['lifetime'] = CACHE_LIFETIME_DEFAULT;
            $this->DB->Query("INSERT INTO `sql_cache` SET `unique_hash` = '{$this->DB->secure($arParams['cache']['chr'])}', `expired` = '" . date("Y-m-d H:i:s", mktime() + $arParams['cache']['lifetime']) . "', `result` = '" . $this->DB->secure(json_encode($arData, JSON_UNESCAPED_UNICODE)) . "'");
        }
        return $arData;
    }

    /**
     * Получение первой удовлетворяющей записи
     * @param array $arFilter массив с параметрами запроса
     * @return array
     */
    public function getID(array $arFilter): array
    {
        return $this->DB->getExtend($this->TABLE, ["get_id" => true, "filter" => $arFilter]);
    }

    /**
     * Получить кол-во записей
     * @param array $arFilter массив с параметрами запроса
     * @return array
     */
    public function getCount(array $arFilter): array
    {
        return $this->DB->getExtend($this->TABLE, ["count" => true, "filter" => $arFilter]);
    }

    /**
     * Функция получения целой записи или поля по ID
     * @param int $id
     * @param array $arParams
     * @return array
     */
    public function getByID(int $id, array $arParams = []): array
    {
        if (empty($id)) return [];

        $arParams['id'] = $id;
        $arParams['show_all'] = 1;

        $arData = $this->getConvert($this->DB->getExtend($this->TABLE, $arParams));
        if (empty($arData)) return false;

        return $arData[$id] ?? [];
    }

    /**
     * Получение поля записи
     * @param int $id
     * @param string $field поле для выборки
     * @return mixed
     */
    public function getField(int $id, string $field)
    {
        $value = $this->DB->One($field, $this->TABLE, ["filter" => ["id" => $id]]);
        return $value ?? null;
    }
}
