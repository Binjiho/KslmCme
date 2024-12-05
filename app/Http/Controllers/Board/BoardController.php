<?php

namespace App\Http\Controllers\Board;

use App\Http\Controllers\Controller;
use App\Services\Board\BoardServices;
use Illuminate\Http\Request;

class BoardController extends Controller
{
    private $boardServices;
    private $boardConfig;

    public function __construct()
    {
        $code = request()->code ?? '';
        $this->boardConfig = getConfig("board")[$code] ?? [];
        $this->boardServices = new BoardServices();

        view()->share([
            'boardConfig' => $this->boardConfig,
            'main_menu' => $this->boardConfig['menu']['main'] ?? '',
            'sub_menu' => $this->boardConfig['menu']['sub'] ?? '',
            'code' => $code,
        ]);
    }

    public function index(Request $request)
    {
        return view("board.{$this->boardConfig['skin']}.index", $this->boardServices->listService($request));
    }

    public function view(Request $request)
    {
        return view("board.{$this->boardConfig['skin']}.view", $this->boardServices->viewService($request));
    }

    public function upsert(Request $request)
    {
        return view("board.{$this->boardConfig['skin']}.upsert", $this->boardServices->upsertService($request));
    }

    public function reply(Request $request)
    {
        return view("board.{$this->boardConfig['skin']}.reply", $this->boardServices->replyService($request));
    }

    public function data(Request $request)
    {
        return $this->boardServices->dataAction($request);
    }
}
