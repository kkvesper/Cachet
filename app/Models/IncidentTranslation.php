<?php

namespace CachetHQ\Cachet\Models;

use Illuminate\Database\Eloquent\Model;

class IncidentTranslation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'incident_translations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'incident_id',
        'locale',
        'name',
        'message',
    ];

    /**
     * Belongs to an incident.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }
}
