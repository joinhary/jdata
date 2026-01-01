<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryUchiModel extends Model
{
        protected $table = 'npo_contract_history';
		protected $fillable = ['done'];
}
