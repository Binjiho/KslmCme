<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardComment extends Model
{
    use HasFactory;

    protected $primaryKey = 'sid';

    protected $guarded = [
        'sid',
        'b_sid',
        'u_sid',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected static function booted()
    {
        parent::boot();

        static::deleting(function ($board) {
            // 첨부파일 (plupload) 있을경우 하나씩 삭제
            $board->files()->each(function ($file) {
                $file->delete();
            });

            // 썸네일 있을경우 경로에 있는 실제 파일 삭제
            if (!is_null($board->thumbnail_realfile)) {
                (new CommonServices())->fileDeleteService($board->thumbnail_realfile);
            }

            // 게시판 데이터 삭제시 첨부파일(단일파일) 있을경우 경로에 있는 실제 파일 삭제
            foreach(self::boardConfig()['file'] as $key => $val) {
                $pathField = 'realfile' . $key; // 파일 경로 데이터 저장 컬럼

                if (!empty($board->{$pathField})) {
                    (new CommonServices())->fileDeleteService($board->{$pathField});
                }
            }
        });

        static::saved(function ($board) {
            $data = request();
            $c_sid = $board->sid;
            $plupload_file = $data->plupload_file;
            $plupload_file_del = $data->plupload_file_del;

            /* 첨부파일 (plupload) */
            if (!empty($plupload_file)) {
                foreach (json_decode($plupload_file, true) as $row) { // 첨부파일 (plupload) 등록
                    $file = new BoardCommentFile();
                    $file->setByData($row, $c_sid);
                    $file->save();
                }
            }

            // 첨부파일 (plupload) 삭제
            if (!empty($plupload_file_del)) {
                foreach ($board->files()->whereIn('sid', $plupload_file_del)->get() as $plFile) {
                    $plFile->delete();
                }
            }
        });
    }

    protected static function boardConfig($code = null)
    {
        return getConfig("board")[$code ?? request()->code];
    }

    public function setByData($data)
    {
        if (empty($this->sid)) {
            $this->b_sid = $data['b_sid'];
            $this->u_sid = thisPk();
        }

        $this->comment = $data['comment'];
        $this->writer = $data['writer'];
    }

    public function files()
    {
        return $this->hasMany(BoardCommentFile::class, 'c_sid');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'u_sid');
    }

    public function board()
    {
        return $this->belongsTo(Board::class, 'b_sid');
    }
}
