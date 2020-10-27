<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
trait Geographical
{
    /**
     * @param Builder $query
     * @param float $latitude Latitude
     * @param float $longitude Longitude
     * @return Builder
     */
    public function scopeDistanceto($query, $latitude, $longitude)
    {
        $latName = $this->getQualifiedLatitudeColumn();
        $lonName = $this->getQualifiedLongitudeColumn();
        $query->select($this->getTable() . '.*');
        $sql = "(select ((ACOS(SIN(? * PI() / 180) * SIN(" . $latName . " * PI() / 180) + COS(? * PI() / 180) * COS(" .
            $latName . " * PI() / 180) * COS((? - " . $lonName . ") * PI() / 180)) * 180 / PI()) * 60 * ?) from user_locations where listings.user_id = user_locations.user_id) as distance";
        $kilometers = false;
        if (property_exists(static::class, 'kilometers')) {
            $kilometers = static::$kilometers;
        }
        if ($kilometers) {
            $query->selectRaw($sql, [$latitude, $latitude, $longitude, 1.1515 * 1.609344]);
        } else {
            // miles
            $query->selectRaw($sql, [$latitude, $latitude, $longitude, 1.1515]);
        }
        //echo $query->toSql();
        //var_export($query->getBindings());
        return $query;
    }
    public function scopeGeofence($query, $latitude, $longitude, $inner_radius, $outer_radius)
    {
        $query = $this->scopeDistance($query, $latitude, $longitude);
        return $query->havingRaw('distance BETWEEN ? AND ?', [$inner_radius, $outer_radius]);
    }
    protected function getQualifiedLatitudeColumn()
    {
        return 'user_locations.' . $this->getLatitudeColumn();
    }
    protected function getQualifiedLongitudeColumn()
    {
        return 'user_locations.' . $this->getLongitudeColumn();
    }
    public function getLatitudeColumn()
    {
        return defined('static::LATITUDE') ? static::LATITUDE : 'latitude';
    }
    public function getLongitudeColumn()
    {
        return defined('static::LONGITUDE') ? static::LONGITUDE : 'longitude';
    }
}
?>
