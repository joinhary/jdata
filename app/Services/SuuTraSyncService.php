<?php

namespace App\Services;

use App\Models\SuuTraModel;
use App\Services\SolrIndexerService;
use App\Http\Controllers\AppController;
use Illuminate\Support\Facades\DB;
use Throwable;

class SuuTraSyncService
{
    /**
     * Sync 1 bản ghi SuuTra
     */
    public static function sync(array $item): void
    {
        // Chuẩn hóa unicode + ép object
        $item = (object) AppController::convert_unicode_object($item);

        if (empty($item->ma_dong_bo)) {
            return; // hoặc throw exception
        }

        DB::transaction(function () use ($item) {

            /**
             * updateOrCreate
             * → an toàn khi có UNIQUE index
             */
            $model = SuuTraModel::updateOrCreate(
                ['ma_dong_bo' => $item->ma_dong_bo],
                self::mapDb($item)
            );

            // Đánh dấu đã merge
            $model->merged = 1;
            $model->status_spp = 1;
            $model->save();

            // Reindex Solr
            SolrIndexerService::reindex($model, $item);
        });
    }

    /**
     * Mapping DB
     */
    private static function mapDb(object $i): array
    {
        return [
            'ma_dong_bo'         => $i->ma_dong_bo,
            'uchi_id'            => $i->uchi_id ?? null,
            'texte'              => $i->texte ?? null,
            'loai'               => $i->loai ?? null,
            'duong_su'           => $i->duong_su ?? null,
            'tai_san'            => $i->tai_san ?? null,
            'ngan_chan'          => $i->ngan_chan ?? null,
            'ngay_nhap'          => $i->ngay_nhap ?? null,
            'ngay_cc'            => $i->ngay_cc ?? null,
            'so_hd'              => $i->so_hd ?? null,
            'ten_hd'             => $i->ten_hd ?? null,
            'ccv'                => $i->ccv ?? null,
            'vp'                 => $i->vp ?? null,
            'chu_y'              => $i->chu_y ?? null,

            'duong_su_en'        => $i->duong_su_en ?? null,
            'texte_en'           => $i->texte_en ?? null,

            'status'             => $i->status ?? null,
            'ma_phan_biet'       => $i->ma_phan_biet ?? null,

            'cancel_status'      => $i->cancel_status ?? null,
            'cancel_description' => $i->cancel_description ?? null,

            'file'               => $i->file ?? null,
            'sync_code'          => $i->sync_code ?? null,
            'is_update'          => $i->is_update ?? null,

            'created_at'         => $i->created_at ?? now(),
            'updated_at'         => $i->updated_at ?? now(),

            /**
             * Field tổng hợp để search
             */
            'merge_content' => trim(
                ($i->duong_su_en ?? $i->duong_su ?? '') . ' ' .
                ($i->texte_en ?? $i->texte ?? '')
            ),
        ];
    }
}
