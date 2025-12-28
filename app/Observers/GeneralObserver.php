<?php

namespace App\Observers;
use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class GeneralObserver
{
    public function created(Model $model)
    {
        $this->log($model, 'CREATE');
    }

    public function updated(Model $model)
    {
        $this->log($model, 'UPDATE');
    }

    public function deleted(Model $model)
    {
        $this->log($model, 'DELETE');
    }

    private function log($model, $action)
    {
        if (Auth::check()) {
            ActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => $action,
                'table_name' => $model->getTable(),
                'record_id'  => $model->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }
}
