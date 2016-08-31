<?php

namespace App\Console\Commands;

use App\Services\MockLoginService;
use Illuminate\Console\Command;

class Spider extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spider:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '开始抓取';

    private $mockLoginService;

    public function __construct(MockLoginService $mockLoginService)
    {
        parent::__construct();
        $this->mockLoginService = $mockLoginService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->mockLoginService->postLogin();
    }
}
