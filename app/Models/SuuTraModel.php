<?php

namespace App\Models;

use Elasticsearch\ClientBuilder;
use Illuminate\Database\Eloquent\Model;
use Elasticquent\ElasticquentTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use DateTimeInterface;

class SuuTraModel extends Model
{
//    use ElasticquentTrait;
    use SoftDeletes;

    const NORMAL = 0;
    const PREVENT = 3;
    const WARNING = 2;
    const RELIEVE = 1;
    const TYPENORMAL = 1;
    const TYPE12A = 2;
    const TYPE12B = 3;
    const BANK = 5;
    const EMPTY = '';
    const CODE = 'D';
    protected $table = 'suutranb';
    protected $fillable = [
        'st_id',
        'ma_dong_bo',
        'uchi_id',
        'texte',
        'loai',
        'duong_su',
        'tai_san',
        'ngan_chan',
        'ngay_nhap',
        'ngay_cc',
        'so_hd',
        'ten_hd',
        'ccv',
        'vp',
        'chu_y',
        'ccv_master',
        'vp_master',
        'created_at',
        'updated_at',
        'picture',
        'duong_su_en',
        'texte_en',
        'status',
        'ma_phan_biet',
        'sync',
        'cancel_status',
        'cancel_description',
        'uchi_id_ngan_chan',
        'nguoinhap',
        'vanban',
        'type_cancel',
        'so_cc_cu',
        'phi_cong_chung',
        'thu_lao','note','real_name',
		'duong_su_index',
		'file',
		'sync_code',
		'is_update',
		'property_info',
		'transaction_content',
		'contract_period',
		'complete',
		'texte_reverse',
		'release_in_book_number',
		'release_doc_date',
		'release_file_name',
		'release_file_path',
		'release_regist_agency',
		'release_person_info',
		'release_doc_number',
		'release_doc_summary',
		'van_ban_id',
		'release_doc_receive_date',
		'prevent_doc_receive_date',
		'bank_id',
		'undisputed_date',
		'undisputed_note',
		'cv_id',
		'cv_name',
		'deleted_note',
        'merged', 'merge_content',
        'info_tittle',
        'trans_val'
    ];
    protected $keyType = 'string';
    protected $casts = [
    'st_id' => 'string',
];

    protected $primaryKey = 'st_id';
    protected $dates = ['created_at', 'updated_at'];
//    protected $mappingProperties = array(
//        'texte' => array(
//            'type' => 'text',
//            'analyzer' => 'my_analyzer'
//        ),
//        'duong_su' => array(
//            'type' => 'text',
//            'analyzer' => 'my_analyzer'
//        )
//    );

    public function notary()
    {
        return $this->belongsTo(User::class, 'id', '=', 'ccv');
    }

    public function office()
    {
        return $this->belongsTo(ChiNhanhModel::class, 'cn_id', 'vp');
    }


//    protected function fullTextWildcards($term)
//    {
//        // removing symbols used by MySQL
//        $reservedSymbols = ['-', '+', '<', '>', '@', '(', ')', '~'];
//        $term = str_replace($reservedSymbols, '', $term);
//
//        $words = explode(' ', $term);
//
//        foreach($words as $key => $word) {
//            /*
//             * applying + operator (required word) only big words
//             * because smaller ones are not indexed by mysql
//             */
//            if(strlen($word) >= 3) {
//                $words[$key] = '+' . $word . '*';
//            }
//        }
//
//        $searchTerm = implode( ' ', $words);
//
//        return $searchTerm;
//    }
    ///etc/mysql/my.cnf
    //ft_min_word_len = 2 //nếu dùng MyISAM
    //innodb_ft_min_token_size=2 //nếu dùng InnoD
//    public function scopeFullTextSearch($query, $columns, $term)
//    {
//        $query->whereRaw("MATCH ({$columns}) AGAINST (? IN BOOLEAN MODE)", $this->fullTextWildcards($term));
//
//        return $query;
//    }

//    public function scopeOrFullTextSearch($query, $columns, $term)
//    {
//        $query->orWhereRaw("MATCH ({$columns}) AGAINST (? IN BOOLEAN MODE)", $this->fullTextWildcards($term));
//
//        return $query;
//    }
//    public function addIndex(){
//
//        $hosts = [env('ELASTIC_HOST')];
//
//        $client = ClientBuilder::create()
//            ->setHosts($hosts)
//            ->build();
//        $params = [
//            'index' => 'ngram_three',
//            'type' =>"_doc",
//            'id' => $this->st_id,
//            'body' => $this->toArray()
//        ];
//        $response = $client->index($params);
//		return $response;
//    }

protected function serializeDate(DateTimeInterface $date)
{
    return $date->format('Y-m-d H:i:s');
}
}
