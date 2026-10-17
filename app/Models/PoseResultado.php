<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoseResultado extends Model
{
    use HasFactory;

    protected $table = 'pose_resultados';
    protected $primaryKey = 'ID_Resultado';

    protected $fillable = [
        'ID_Video', 'FrameNum', 'Landmarks', 'AngulosCalculados'
    ];

    protected $casts = [
        'Landmarks' => 'array',
        'AngulosCalculados' => 'array',
    ];

    public function video()
    {
        return $this->belongsTo(Video::class, 'ID_Video', 'ID_Video');
    }
}
