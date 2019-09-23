<?php

namespace App\Models\Medical;

use Illuminate\Database\Eloquent\Model;

class DoctorVideo extends Model
{
    protected $table = 'doctor_video';

    /**
     *
     */
    public function getVideoData($last_id, $limit = 1000)
    {
        return $this->where('id', '>', $last_id)
            ->with('info', 'doctor', 'doctor.config', 'doctor.fristKeshi', 'doctor.secondKeshi', 'hospital')
            ->limit($limit)
            ->get()
            ->orderBy('id', 'asc')
            ->toArray();
    }

    /**
     * 关联info表
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function info()
    {
        return $this->hasOne(DoctorVideoInfo::class, 'id', 'id');
    }

    /**
     * 关联医生
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function doctor()
    {
        return $this->hasOne(Doctor::class, 'id', 'doctor_id');
    }

    /**
     * 关联医院
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hospital()
    {
        return $this->hasOne(Hospital::class, 'id', 'hospital_id');
    }


}

