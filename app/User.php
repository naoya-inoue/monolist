<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    public function items()
    {
        return $this->belongsToMany(Item::class)->withPivot('type')->withTimestamps();
    }
    
    public function want_items()
    {
        return $this->items()->where('type', 'want');
    }
    
    public function want($itemId)
    {
        //すでに Want しているかの確認
        $exist = $this->is_wanting($itemId);
        
        if ($exist) {
            //すでに Want していれば何もしない
            return false;
        } else {
            //未 Want であれば Want
            $this->items()->attach($itemId, ['type' => 'want']);
            return true;
        }
    }
    
    public function dont_want($itemId)
    {
        //すでに Want しているかの確認
        $exist = $this->is_wanting($itemId);
        
        if ($exist) {
            //すでに Want していれば want を外す
            \DB::delete("DELETE FROM item_user WHERE user_id = ? AND item_id = ? AND type = 'want'", [$this->id, $itemId]);
        } else {
            //未 want であればなにもしない
            return false;
        }
    }
    
    public function is_wanting($itemIdOrCode)
    {
        if (is_numeric($itemIdOrCode)) {
            $item_id_exists = $this->want_items()->where('item_id', $itemIdOrCode)->exists();
            return $item_id_exists;
        } else {
            $item_code_exists = $this->want_items()->where('code', $itemIdOrCode)->exists();
            return $item_code_exists;
        }
    }
    
    //課題 have 機能
    
    public function have_items() {
        return $this->items()->where('type', 'have');
    }
    
    public function have($itemId)
    {
        //すでに have しているかの確認
        $exist = $this->is_having($itemId);
        
        if ($exist) {
            //すでに have していれば何もしない
            return false;
        } else {
            //未 have であれば have
            $this->items()->attach($itemId, ['type' => 'have']);
            return true;
        }
    }
    
    public function dont_have($itemId)
    {
        //すでに have しているかの確認
        $exist = $this->is_having($itemId);
        
        if ($exist) {
            //すでに have していれば have を外す
            \DB::delete("DELETE FROM item_user WHERE user_id = ? AND item_id = ? AND type = 'have'", [$this->id, $itemId]);
        } else {
            //未 have であればなにもしない
            return false;
        }
    }
    
    public function is_having($itemIdOrCode)
    {
        if (is_numeric($itemIdOrCode)) {
            $item_id_exists = $this->have_items()->where('item_id', $itemIdOrCode)->exists();
            return $item_id_exists;
        } else {
            $item_code_exists = $this->have_items()->where('code', $itemIdOrCode)->exists();
            return $item_code_exists;
        }
    }
}
