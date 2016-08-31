<?php

namespace App\Console\Commands;

use App\Services\MockLoginService;
use App\Services\SpiderService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class Spider extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spider:start {offset?} {limit?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '开始抓取';

    private $mockLoginService;
    private $spiderService;

    public function __construct(MockLoginService $mockLoginService, SpiderService $spiderService)
    {
        parent::__construct();
        $this->mockLoginService = $mockLoginService;
        $this->spiderService = $spiderService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        list($offset, $limit) = $this->processArgs($this->arguments());
        $searchCookie = $this->mockLoginService->postLogin();

        $this->spiderService->search($searchCookie, $offset, $limit);
    }



    private function processArgs($args)
    {
        if (isset($args['offset'])) {
            $offset = $args['offset'];
        } else {
            $offset = 0;
        }

        if (isset($args['limit'])) {
            $limit = $args['limit'];
        } else {
            $limit = 10000;
        }

        return [$offset, $limit];
    }
}
