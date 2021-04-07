<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Http\Resources\CalendarResource;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email',
        'email_verified_at',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function calendars()
    {
        return $this->hasMany(Calendar::class, 'owner_id');
    }

    public function accessibleCalendars()
    {
        return CalendarResource::collection(Calendar::where('owner_id', $this->id)//owner_idがユーザーのものと一致するprojectを取得
            ->orWhereHas('members', function($query) {//加えて、memberリレーションのピボットテーブルの中で
                $query->where('user_id', $this->id);//user_idがユーザーのものと一致するものを
            })
            ->get());//取得する　　参考: https://search.readouble.com/?query=8.x+orWhereHas


        // $projectCreated = $this->projects;
        // $ids = \DB::table('project_member')->where('user_id', $this->id)->pluck('project_id');
        // $projectsSharedWith = Project::find($ids);
        // return $projectCreated->merge($projectsSharedWith);
    }
}
