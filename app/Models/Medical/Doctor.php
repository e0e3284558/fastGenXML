<?php

namespace App\Models\Medical;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $table = 'doctor';

    /**
     * 关联医生职称表
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function config()
    {
        return $this->hasOne(DoctorConfig::class,'id','clinical_position');
    }

    /**
     * 关联医生一级科室
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function fristKeshi()
    {
        return $this->hasOne(HospitalKeshi::class,'id','hospital_p_dept_id')->select('id','name');
    }

    /**
     * 关联医生二级科室
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function secondKeshi()
    {
        return $this->hasOne(HospitalKeshi::class,'id','hospital_dept_id')->select('id','name');
    }
}
